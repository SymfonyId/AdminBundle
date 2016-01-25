<?php

namespace Symfonian\Indonesia\AdminBundle\Controller;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\Event\FilterFormEvent;
use Symfonian\Indonesia\AdminBundle\Handler\CrudHandler;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminEvents as Event;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class CrudController extends Controller
{
    protected $viewParams = array();

    /**
     * @Route("/new/")
     * @Method({"POST", "GET"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $event = new FilterFormEvent();

        $this->fireEvent(Event::PRE_FORM_CREATE, $event);

        $response = $event->getResponse();
        if ($response) {
            return $response;
        }

        /** @var Configurator $configuration */
        $configuration = $this->container->get('symfonian_id.admin.configurator');

        $entityClass = $configuration->getEntityClass();
        $entity = new $entityClass();
        $form = $event->getForm() ?: $configuration->getForm($entity);

        return $this->handle($request, CrudHandler::ACTION_CREATE, $configuration->getAddTemplate(), $entity, $form);
    }

    /**
     * @Route("/{id}/edit/")
     * @Method({"POST", "GET"})
     *
     * @param Request $request
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        $this->isAllowedOr404Error(CrudHandler::GRID_ACTION_EDIT);

        $event = new FilterFormEvent();

        $this->fireEvent(Event::PRE_FORM_CREATE, $event);

        $response = $event->getResponse();
        if ($response) {
            return $response;
        }

        /** @var Configurator $configuration */
        $configuration = $this->container->get('symfonian_id.admin.configurator');

        $entity = $this->findOr404Error($id);
        $form = $event->getForm() ?: $configuration->getForm($entity);

        return $this->handle($request, CrudHandler::ACTION_UPDATE, $configuration->getEditTemplate(), $entity, $form);
    }

    /**
     * @Route("/{id}/show/")
     * @Method({"GET"})
     *
     * @param Request $request
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, $id)
    {
        $this->isAllowedOr404Error(CrudHandler::GRID_ACTION_SHOW);
        /** @var EntityInterface $entity */
        $entity = $this->findOr404Error($id);

        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        /** @var Configurator $configuration */
        $configuration = $this->container->get('symfonian_id.admin.configurator');

        $this->viewParams['page_title'] = $translator->trans($configuration->getTitle(), array(), $translationDomain);
        $this->viewParams['page_description'] = $translator->trans($configuration->getDescription(), array(), $translationDomain);

        /** @var CrudHandler $handler */
        $handler = $this->container->get('symfonian_id.admin.handler.crud');
        $handler->setEntity($configuration->getEntityClass());
        $handler->setViewParams($this->viewParams);
        $handler->setTemplate($configuration->getShowTemplate());
        $handler->showDetail($request, $entity, $configuration->getShowFields());

        return $handler->getResponse();
    }

    /**
     * @Route("/{id}/delete/")
     * @Method({"DELETE"})
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function deleteAction($id)
    {
        $this->isAllowedOr404Error(CrudHandler::GRID_ACTION_DELETE);
        /** @var EntityInterface $entity */
        $entity = $this->findOr404Error($id);

        /** @var CrudHandler $handler */
        $handler = $this->container->get('symfonian_id.admin.handler.crud');
        /** @var Configurator $configuration */
        $configuration = $this->container->get('symfonian_id.admin.configurator');

        $handler->setEntity($configuration->getEntityClass());
        $returnHandler = $handler->remove($entity);
        if ($returnHandler instanceof Response) {
            return $returnHandler;
        }

        return new JsonResponse(array('status' => $returnHandler));
    }

    /**
     * @Route("/")
     * @Route("/list/")
     * @Method({"GET"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        /** @var CrudHandler $handler */
        $handler = $this->container->get('symfonian_id.admin.handler.crud');
        /** @var Configurator $configuration */
        $configuration = $this->container->get('symfonian_id.admin.configurator');

        $listTemplate = $request->isXmlHttpRequest() ? $configuration->getAjaxTemplate() : $configuration->getListTemplate();

        $this->viewParams['use_ajax'] = $configuration->isUseAjax();
        $this->viewParams['page_title'] = $translator->trans($configuration->getTitle(), array(), $translationDomain);
        $this->viewParams['page_description'] = $translator->trans($configuration->getDescription(), array(), $translationDomain);

        $handler->setEntity($configuration->getEntityClass());
        $handler->setViewParams($this->viewParams);
        $handler->setTemplate($listTemplate);
        $handler->viewList($request, $configuration->getGridFields(), $configuration->getGridFilter(), $configuration->isNormalizeFilter(), $configuration->isFormatNumber());

        return $handler->getResponse();
    }

    protected function handle(Request $request, $action, $template, EntityInterface $data = null, FormInterface $form = null)
    {
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        /** @var CrudHandler $handler */
        $handler = $this->container->get('symfonian_id.admin.handler.crud');
        /** @var Configurator $configuration */
        $configuration = $this->container->get('symfonian_id.admin.configurator');

        $this->viewParams['page_title'] = $translator->trans($configuration->getTitle(), array(), $translationDomain);
        $this->viewParams['page_description'] = $translator->trans($configuration->getDescription(), array(), $translationDomain);
        $this->viewParams['action_method'] = $translator->trans('page.'.strtolower($action), array(), $translationDomain);
        $this->viewParams['use_date_picker'] = $configuration->isUseDatePicker();
        $this->viewParams['use_file_style'] = $configuration->isUseFileStyle();
        $this->viewParams['use_editor'] = $configuration->isUseEditor();
        $this->viewParams['autocomplete'] = $configuration->getAutocomplete();

        $handler->setEntity($configuration->getEntityClass());
        $handler->setViewParams($this->viewParams);
        $handler->setTemplate($template);
        $handler->createNewOrUpdate($this, $request, $data, $form);

        return $handler->getResponse();
    }

    /**
     * @param $id
     * @return null | EntityInterface
     */
    protected function findOr404Error($id)
    {
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        /** @var Configurator $configuration */
        $configuration = $this->container->get('symfonian_id.admin.configurator');
        $entity = $this->container->get('doctrine.orm.entity_manager')->getRepository($configuration->getEntityClass())->find($id);

        if (!$entity) {
            throw new NotFoundHttpException($translator->trans('message.data_not_found', array('%id%' => $id), $translationDomain));
        }

        return $entity;
    }

    protected function isAllowedOr404Error($action)
    {
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        if (!in_array($action, $this->container->getParameter('symfonian_id.admin.grid_action'))) {
            throw new NotFoundHttpException($translator->trans('message.request_not_found', array(), $translationDomain));
        }

        return true;
    }

    protected function fireEvent($name, $handler)
    {
        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch($name, $handler);
    }
}
