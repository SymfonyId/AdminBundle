<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Controller;

use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\AdminBundle\Annotation\Grid;
use Symfonian\Indonesia\AdminBundle\Annotation\Page;
use Symfonian\Indonesia\AdminBundle\Annotation\Plugins;
use Symfonian\Indonesia\AdminBundle\Annotation\Util\AutoComplete;
use Symfonian\Indonesia\AdminBundle\Annotation\Util\DatePicker;
use Symfonian\Indonesia\AdminBundle\Annotation\Util\ExternalJavascript;
use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\Handler\CrudHandler;
use Symfonian\Indonesia\AdminBundle\Model\BulkDeletableInterface;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants as Constants;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
abstract class CrudController extends Controller
{
    /**
     * View variables store.
     *
     * @var array
     */
    private $viewParams = array();

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        /** @var Configurator $configuration */
        $configuration = $this->getConfigurator($this->getClassName());
        /** @var Crud $crud */
        $crud = $configuration->getConfiguration(Crud::class);
        $this->isAllowOr404Error($crud, Constants::ACTION_CREATE);
        /** @var Plugins $plugins */
        $plugins = $configuration->getConfiguration(Plugins::class);
        $template = $plugins->isUseBulkInsert() ? $crud->getBulkCreateTemplate() : $crud->getCreateTemplate();

        $entityClass = $crud->getEntityClass();
        $entity = new $entityClass();
        $form = $crud->getForm($entity);

        return $this->handle($request, $entity, $form, Constants::ACTION_CREATE, $template);
    }

    /**
     * @param Request $request
     * @param $id
     *
     * @return Response
     */
    public function editAction(Request $request, $id)
    {
        /** @var Configurator $configuration */
        $configuration = $this->getConfigurator($this->getClassName());
        /** @var Crud $crud */
        $crud = $configuration->getConfiguration(Crud::class);
        $this->isAllowOr404Error($crud, Constants::ACTION_UPDATE);

        $entity = $this->findOr404Error($id);
        $form = $crud->getForm($entity);

        return $this->handle($request, $entity, $form, Constants::ACTION_UPDATE, $crud->getEditTemplate());
    }

    /**
     * @param Request $request
     * @param $id
     *
     * @return Response
     */
    public function showAction(Request $request, $id)
    {
        /** @var Configurator $configuration */
        $configuration = $this->getConfigurator($this->getClassName());
        /** @var Crud $crud */
        $crud = $configuration->getConfiguration(Crud::class);
        $this->isAllowOr404Error($crud, Constants::ACTION_READ);

        /** @var EntityInterface $entity */
        $entity = $this->findOr404Error($id);
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        /** @var Page $page */
        $page = $configuration->getConfiguration(Page::class);

        $this->viewParams['page_title'] = $translator->trans($page->getTitle(), array(), $translationDomain);
        $this->viewParams['page_description'] = $translator->trans($page->getDescription(), array(), $translationDomain);

        /** @var CrudHandler $handler */
        $handler = $this->container->get('symfonian_id.admin.handler.crud');
        $handler->setEntity($crud->getEntityClass());
        $handler->setViewParams($this->viewParams);
        $handler->setTemplate($crud->getShowTemplate());
        $handler->showDetail($request, $entity, $crud->getShowFields() ?: $this->getEntityFields($crud), $crud->isAllowDelete());

        return $handler->getResponse();
    }

    /**
     * @param $id
     *
     * @return bool|JsonResponse|Response
     */
    public function deleteAction($id)
    {
        /** @var Configurator $configuration */
        $configuration = $this->getConfigurator($this->getClassName());
        /** @var Crud $crud */
        $crud = $configuration->getConfiguration(Crud::class);
        $this->isAllowOr404Error($crud, Constants::ACTION_DELETE);

        /** @var EntityInterface $entity */
        $entity = $this->findOr404Error($id);
        /** @var CrudHandler $handler */
        $handler = $this->container->get('symfonian_id.admin.handler.crud');

        $handler->setEntity($crud->getEntityClass());
        $returnHandler = $handler->remove($entity);
        if ($returnHandler instanceof Response) {
            return $returnHandler;
        }

        return new JsonResponse(array('status' => $returnHandler, 'message' => $handler->getErrorMessage()));
    }

    /**
     * @return bool|JsonResponse
     */
    public function bulkDeleteAction(Request $request)
    {
        /** @var Configurator $configuration */
        $configuration = $this->getConfigurator($this->getClassName());
        /** @var Crud $crud */
        $crud = $configuration->getConfiguration(Crud::class);
        $this->isAllowOr404Error($crud, Constants::ACTION_DELETE);

        $isDeleted = array();
        $countData = 0;
        foreach ($request->get('id', array()) as $id) {
            $entity = $this->findOr404Error($id);
            if (!$entity instanceof BulkDeletableInterface) {
                return;
            }
            $deleteMessage = $entity->getDeleteInformation();

            /** @var CrudHandler $handler */
            $handler = $this->container->get('symfonian_id.admin.handler.crud');

            $handler->setEntity($crud->getEntityClass());
            if (true === $handler->remove($entity)) {
                $isDeleted[] = $deleteMessage;
            }

            ++$countData;
        }

        if (0 === count($isDeleted)) {
            $message = 'Tidak ada data yang berhasil dihapus. Kemungkinan data-data tersebut berelasi.';
        } else {
            $message = sprintf('Data yang berhasil dari hapus adalah %d dari %d [%s]', count($isDeleted), $countData, implode(', ', $isDeleted));
        }

        return new JsonResponse(array('status' => empty($isDeleted) ? false : true, 'message' => $message));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');
        /** @var Configurator $configuration */
        $configuration = $this->getConfigurator($this->getClassName());
        /** @var Crud $crud */
        $crud = $configuration->getConfiguration(Crud::class);
        $this->isAllowOr404Error($crud, Constants::ACTION_READ);

        /** @var CrudHandler $handler */
        $handler = $this->container->get('symfonian_id.admin.handler.crud');
        $configuration->parseClass($crud->getEntityClass());
        /** @var Page $page */
        $page = $configuration->getConfiguration(Page::class);
        /** @var Grid $grid */
        $grid = $configuration->getConfiguration(Grid::class);

        $listTemplate = $request->isXmlHttpRequest() ? $crud->getAjaxTemplate() : $crud->getListTemplate();
        $columns = $grid->getColumns() ?: $this->getEntityFields($crud);
        $filters = $grid->getFilters() ?: (array) $columns[0];

        $this->viewParams['page_title'] = $translator->trans($page->getTitle(), array(), $translationDomain);
        $this->viewParams['page_description'] = $translator->trans($page->getDescription(), array(), $translationDomain);

        /*
         * Translate tentity fields
         */
        $this->viewParams['filter_fields'] = implode(', ', array_map(function ($value) use ($translator, $translationDomain) {
            return $translator->trans(sprintf('entity.fields.%s', $value), array(), $translationDomain);
        }, $filters));
        $this->viewParams['filter_fields_entity'] = implode(', ', $filters);

        $allowBulkDelete = false;
        $reflectionEntity = new \ReflectionClass($crud->getEntityClass());
        foreach ($reflectionEntity->getInterfaces() as $reflectionClass) {
            if ($reflectionClass->getName() === BulkDeletableInterface::class && $crud->isAllowDelete()) {
                $allowBulkDelete = true;
            }
        }

        $handler->setEntity($crud->getEntityClass());
        $handler->setViewParams($this->viewParams);
        $handler->setTemplate($listTemplate);
        $handler->viewList($request, $columns, $crud->getAction(), $crud->isAllowCreate(), $allowBulkDelete, $grid->isFormatNumber());

        return $handler->getResponse();
    }

    /**
     * @param Request         $request
     * @param EntityInterface $data
     * @param FormInterface   $form
     * @param $action
     * @param $template
     *
     * @return Response
     */
    private function handle(Request $request, EntityInterface $data, FormInterface $form, $action, $template)
    {
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        /** @var CrudHandler $handler */
        $handler = $this->container->get('symfonian_id.admin.handler.crud');

        /** @var Configurator $configuration */
        $configuration = $this->getConfigurator($this->getClassName());
        /** @var Crud $crud */
        $crud = $configuration->getConfiguration(Crud::class);
        /** @var Page $page */
        $page = $configuration->getConfiguration(Page::class);
        /** @var Plugins $util */
        $util = $configuration->getConfiguration(Plugins::class);
        /** @var AutoComplete $autoComplete */
        $autoComplete = $configuration->getConfiguration(AutoComplete::class);
        /** @var DatePicker $datePicker */
        $datePicker = $configuration->getConfiguration(DatePicker::class);
        /** @var ExternalJavascript $externalJavascript */
        $externalJavascript = $configuration->getConfiguration(ExternalJavascript::class);

        $this->viewParams['page_title'] = $translator->trans($page->getTitle(), array(), $translationDomain);
        $this->viewParams['page_description'] = $translator->trans($page->getDescription(), array(), $translationDomain);
        $this->viewParams['action_method'] = $translator->trans('page.'.strtolower($action), array(), $translationDomain);
        $this->viewParams['use_file_style'] = $util->isUseFileChooser();
        $this->viewParams['use_editor'] = $util->isUseHtmlEditor();
        $this->viewParams['use_numeric'] = $util->isUseNumeric();
        $this->viewParams['autocomplete'] = false;
        $this->viewParams['include_javascript'] = false;
        //Auto complete
        if ($autoComplete->getRouteStore()) {
            $this->viewParams['autocomplete'] = true;
            $this->viewParams['ac_config'] = array(
                'route' => $autoComplete->getRouteStore(),
                'route_callback' => $autoComplete->getRouteCallback(),
                'selector_storage' => $autoComplete->getTargetSelector(),
            );
        }
        //Date picker
        $this->viewParams['use_date_picker'] = true;
        $this->viewParams['date_picker'] = array(
            'date_format' => $datePicker->getDateFormat(),
            'flatten' => $datePicker->isFlatten(),
        );
        //External Javascript
        if (!empty($externalJavascript->getFiles())) {
            $this->viewParams['include_javascript'] = true;
            $this->viewParams['js_include'] = array(
                'files' => $externalJavascript->getFiles(),
                'route' => $externalJavascript->getRoutes(),
            );
        }

        $handler->setEntity($crud->getEntityClass());
        $handler->setViewParams($this->viewParams);
        $handler->setTemplate($template);
        $handler->createNewOrUpdate($this, $request, $data, $form);

        return $handler->getResponse();
    }

    /**
     * @param $id
     *
     * @return EntityInterface
     */
    private function findOr404Error($id)
    {
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        /** @var Configurator $configuration */
        $configuration = $this->getConfigurator($this->getClassName());
        /** @var Crud $crud */
        $crud = $configuration->getConfiguration(Crud::class);

        /** @var EntityInterface $entity */
        $entity = $this->container->get('doctrine.orm.entity_manager')->getRepository($crud->getEntityClass())->find($id);

        if (!$entity) {
            throw new NotFoundHttpException($translator->trans('message.data_not_found', array('%id%' => $id), $translationDomain));
        }

        return $entity;
    }

    /**
     * @param Crud $crud
     *
     * @return array
     */
    private function getEntityFields(Crud $crud)
    {
        $fields = array();
        $reflection = new \ReflectionClass($crud->getEntityClass());

        foreach ($reflection->getProperties() as $key => $property) {
            if ('id' !== $name = $property->getName()) {
                $fields[] = $name;
            }
        }

        return $fields;
    }

    /**
     * @param Crud   $crud
     * @param string $action
     *
     * @return bool
     */
    private function isAllowOr404Error(Crud $crud, $action)
    {
        $granted = false;
        switch ($action) {
            case Constants::ACTION_CREATE:
                $granted = $crud->isAllowCreate();
                break;
            case Constants::ACTION_UPDATE:
                $granted = $crud->isAllowEdit();
                break;
            case Constants::ACTION_READ:
                $granted = $crud->isAllowShow();
                break;
            case Constants::ACTION_DELETE:
                $granted = $crud->isAllowDelete();
                break;
        }
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        if (!$granted) {
            throw new NotFoundHttpException($translator->trans('message.request_not_found', array(), $translationDomain));
        }

        return $granted;
    }

    /**
     * @param mixed $key
     * @param mixed $value
     */
    private function setViewParam($key, $value)
    {
        $this->viewParams[$key] = $value;
    }
}
