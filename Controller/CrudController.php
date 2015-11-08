<?php

namespace Symfonian\Indonesia\AdminBundle\Controller;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfonian\Indonesia\AdminBundle\Event\FilterResponseEvent;
use Symfonian\Indonesia\AdminBundle\Handler\CrudHandler;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminEvents as Event;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class CrudController extends Controller
{
    protected $normalizeFilter = false;

    protected $formatNumber = true;

    protected $gridFields = array();

    protected $newTemplate = 'SymfonianIndonesiaAdminBundle:Crud:new.html.twig';

    protected $editTemplate = 'SymfonianIndonesiaAdminBundle:Crud:new.html.twig';

    protected $showTemplate = 'SymfonianIndonesiaAdminBundle:Crud:show.html.twig';

    protected $listTemplate = 'SymfonianIndonesiaAdminBundle:Crud:list.html.twig';

    protected $listAjaxTemplate = 'SymfonianIndonesiaAdminBundle:Crud:list_template.html.twig';

    protected $useAjaxList = false;

    protected $useDatePicker = false;

    protected $useFileStyle = false;

    protected $useEditor = false;

    protected $autocomplete = array();

    protected $filterFields = array();

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
        $event = new FilterResponseEvent();
        $event->setController($this);

        $this->fireEvent(Event::PRE_FORM_CREATE, $event);

        $response = $event->getResponse();
        if ($response) {
            return $response;
        }
        
        $entity = new $this->entity();
        $form = $event->getForm() ?: $this->getForm($entity);

        return $this->handle($request, CrudHandler::ACTION_CREATE, $this->newTemplate, $entity, $form);
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

        $event = new FilterResponseEvent();
        $event->setController($this);

        $this->fireEvent(Event::PRE_FORM_CREATE, $event);

        $response = $event->getResponse();
        if ($response) {
            return $response;
        }

        $entity = $this->findOr404Error($id);
        $form = $event->getForm() ?: $this->getForm($entity);

        return $this->handle($request, CrudHandler::ACTION_UPDATE, $this->editTemplate, $entity, $form);
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
        $entity = $this->findOr404Error($id);

        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        $this->viewParams['page_title'] = $translator->trans($this->title, array(), $translationDomain);
        $this->viewParams['page_description'] = $translator->trans($this->description, array(), $translationDomain);

        $handler = $this->container->get('symfonian_id.admin.handler.crud');
        $handler->setEntity($this->entity);
        $handler->setViewParams($this->viewParams);
        $handler->setTemplate($this->showTemplate);
        $handler->showDetail($request, $entity, $this->showFields());

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
        $entity = $this->findOr404Error($id);
        $handler = $this->container->get('symfonian_id.admin.handler.crud');
        $handler->setEntity($this->entity);

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
        $listTemplate = $request->isXmlHttpRequest() ? $this->listAjaxTemplate : $this->listTemplate;

        $this->viewParams['use_ajax'] = $this->useAjaxList;
        $this->viewParams['page_title'] = $translator->trans($this->title, array(), $translationDomain);
        $this->viewParams['page_description'] = $translator->trans($this->description, array(), $translationDomain);

        $handler = $this->container->get('symfonian_id.admin.handler.crud');
        $handler->setEntity($this->entity);
        $handler->setViewParams($this->viewParams);
        $handler->setTemplate($listTemplate);
        $handler->viewList($request, $this->gridFields(), $this->filterFields, $this->normalizeFilter, $this->formatNumber);

        return $handler->getResponse();
    }

    /**
     * @param bool $normalizeFilter
     *
     * @return \Symfonian\Indonesia\AdminBundle\Controller\CrudController
     */
    public function upperCaseFilter($normalizeFilter = true)
    {
        $this->normalizeFilter = $normalizeFilter;

        return $this;
    }

    /**
     * @param bool $formatNumber
     *
     * @return \Symfonian\Indonesia\AdminBundle\Controller\CrudController
     */
    public function formatNumber($formatNumber = true)
    {
        $this->formatNumber = $formatNumber;

        return $this;
    }

    /**
     * @param array $fields
     *
     * @return \Symfonian\Indonesia\AdminBundle\Controller\CrudController
     */
    public function setGridFields(array $fields)
    {
        $this->gridFields = $fields;

        return $this;
    }

    /**
     * @param string $template
     *
     * @return \Symfonian\Indonesia\AdminBundle\Controller\CrudController
     */
    public function setNewTemplate($template)
    {
        $this->newTemplate = $template;

        return $this;
    }

    /**
     * @param string $template
     *
     * @return \Symfonian\Indonesia\AdminBundle\Controller\CrudController
     */
    public function setEditTemplate($template)
    {
        $this->editTemplate = $template;

        return $this;
    }

    /**
     * @param string $template
     *
     * @return \Symfonian\Indonesia\AdminBundle\Controller\CrudController
     */
    public function setShowTemplate($template)
    {
        $this->showTemplate = $template;

        return $this;
    }

    /**
     * @param string $template
     *
     * @return \Symfonian\Indonesia\AdminBundle\Controller\CrudController
     */
    public function setListTemplate($template)
    {
        $this->listTemplate = $template;

        return $this;
    }

    /**
     * @param string $template
     * @param bool   $useAjax
     *
     * @return \Symfonian\Indonesia\AdminBundle\Controller\CrudController
     */
    public function setAjaxTemplate($template, $useAjax = true)
    {
        $this->listAjaxTemplate = $template;
        $this->useAjaxList = $useAjax;

        return $this;
    }

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function setFilter(array $fields)
    {
        $this->filterFields = $fields;

        return $this;
    }

    /**
     * @param string $javascriptTwigPath
     * @param array  $includeRoute
     *
     * @return \Symfonian\Indonesia\AdminBundle\Controller\CrudController
     */
    public function includeJs($javascriptTwigPath, array $includeRoute = null)
    {
        $this->viewParams['include_javascript'] = $javascriptTwigPath;

        if ($includeRoute) {
            $this->viewParams['include_route'] = $includeRoute;
        }

        return $this;
    }

    /**
     * @return \Symfonian\Indonesia\AdminBundle\Controller\CrudController
     */
    public function useDatePicker()
    {
        $this->useDatePicker = true;

        return $this;
    }

    /**
     * @return \Symfonian\Indonesia\AdminBundle\Controller\CrudController
     */
    public function useCustomFileChooser()
    {
        $this->useFileStyle = true;

        return $this;
    }

    /**
     * @return \Symfonian\Indonesia\AdminBundle\Controller\CrudController
     */
    public function useEditor()
    {
        $this->useEditor = true;

        return $this;
    }

    /**
     * @param string $route
     * @param string $valueStorageSelector
     *
     * @return \Symfonian\Indonesia\AdminBundle\Controller\CrudController
     */
    public function setAutoComplete($route, $valueStorageSelector)
    {
        $this->autocomplete['route'] = $route;
        $this->autocomplete['value_storage_selector'] = $valueStorageSelector;

        return $this;
    }

    protected function handle(Request $request, $action, $template, EntityInterface $data = null, FormInterface $form = null)
    {
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        if (empty($this->autocomplete)) {
            $this->autocomplete['route'] = 'home';
            $this->autocomplete['value_storage_selector'] = '.selector';
        }

        $this->viewParams['page_title'] = $translator->trans(strtolower($this->title), array(), $translationDomain);
        $this->viewParams['action_method'] = $translator->trans('page.'.strtolower($action), array(), $translationDomain);
        $this->viewParams['page_description'] = $translator->trans(strtolower($this->description), array(), $translationDomain);
        $this->viewParams['use_date_picker'] = $this->useDatePicker;
        $this->viewParams['use_file_style'] = $this->useFileStyle;
        $this->viewParams['use_editor'] = $this->useEditor;
        $this->viewParams['autocomplete'] = $this->autocomplete;

        $handler = $this->container->get('symfonian_id.admin.handler.crud');
        $handler->setEntity($this->entity);
        $handler->setViewParams($this->viewParams);
        $handler->setTemplate($template);
        $handler->createNewOrUpdate($this, $request, $data, $form);

        return $handler->getResponse();
    }

    protected function findOr404Error($id)
    {
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        $entity = $this->getDoctrine()->getRepository($this->entity)->find($id);

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

    protected function gridFields()
    {
        if (!empty($this->gridFields)) {
            return $this->gridFields;
        }

        return $this->getEntityFields();
    }

    protected function fireEvent($name, $handler)
    {
        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch($name, $handler);
    }
}
