<?php
namespace Symfonian\Indonesia\AdminBundle\Controller;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfonian\Indonesia\AdminBundle\Event\GetEntityEvent;
use Symfonian\Indonesia\AdminBundle\Event\GetEntityResponseEvent;
use Symfonian\Indonesia\AdminBundle\Event\GetFormResponseEvent;
use Symfonian\Indonesia\AdminBundle\Event\GetQueryEvent;
use Symfonian\Indonesia\AdminBundle\Event\GetResponseEvent;
use Symfonian\Indonesia\AdminBundle\Event\GetDataEvent;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminEvents as Event;

abstract class CrudController extends Controller
{
    protected $outputParameter = array();

    protected $normalizeFilter = false;

    protected $gridFields = array();

    protected $newActionTemplate = 'SymfonianIndonesiaAdminBundle:Crud:new.html.twig';

    protected $editActionTemplate = 'SymfonianIndonesiaAdminBundle:Crud:new.html.twig';

    protected $showActionTemplate = 'SymfonianIndonesiaAdminBundle:Crud:show.html.twig';

    protected $listActionTemplate = 'SymfonianIndonesiaAdminBundle:Crud:list.html.twig';

    protected $listAjaxActionTemplate = 'SymfonianIndonesiaAdminBundle:Crud:list_template.html.twig';

    protected $useAjaxList = false;

    protected $useDatePicker = false;

    protected $useFileStyle = false;

    protected $useEditor = false;

    protected $autocomplete = array();

    protected $filterFields = array();

    const ENTITY_ALIAS = 'o';

    /**
     * @Route("/new/")
     * @Method({"POST", "GET"})
     */
    public function newAction(Request $request)
    {
        $event = new GetFormResponseEvent();
        $event->setController($this);

        $this->fireEvent(Event::PRE_FORM_CREATE_EVENT, $event);

        $response = $event->getResponse();
        if ($response) {

            return $response;
        }

        $entity = $event->getFormData();
        if (! $entity) {
            $entity = new $this->entityClass();
        }

        return $this->handle($request, $entity, $this->newActionTemplate, 'new', $event->getForm());
    }

    /**
     * @Route("/{id}/edit/")
     * @Method({"POST", "GET"})
     */
    public function editAction(Request $request, $id)
    {
        $this->isAllowedOr404Error('edit');

        $event = new GetFormResponseEvent();
        $event->setController($this);

        $this->fireEvent(Event::PRE_FORM_CREATE_EVENT, $event);

        $response = $event->getResponse();
        if ($response) {

            return $response;
        }

        $entity = $event->getFormData();
        if (! $entity) {
            $entity = $this->findOr404Error($id);
        }

        return $this->handle($request, $entity, $this->editActionTemplate, 'edit', $event->getForm());
    }

    /**
     * @Route("/{id}/show/")
     * @Method({"GET"})
     */
    public function showAction(Request $request, $id)
    {
        $this->isAllowedOr404Error('show');

        $entity = $this->findOr404Error($id);

        $data = array();
        foreach ($this->showFields() as $key => $property) {
            $method = 'get'.ucfirst($property);

            if (method_exists($entity, $method)) {
                array_push($data, array(
                    'name' => $property,
                    'value' => call_user_func_array(array($entity, $method), array()),
                ));
            } else {
                $method = 'is'.ucfirst($property);

                if (method_exists($entity, $method)) {
                    array_push($data, array(
                        'name' => $property,
                        'value' => call_user_func_array(array($entity, $method), array()),
                    ));
                }
            }
        }

        $event = new GetDataEvent();
        $event->setData($data);

        $this->fireEvent(Event::PRE_SHOW_EVENT, $event);

        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        $this->outputParameter['data'] = $data;
        $this->outputParameter['menu'] = $this->container->getParameter('symfonian_id.admin.menu');
        $this->outputParameter['page_title'] = $translator->trans($this->pageTitle, array(), $translationDomain);
        $this->outputParameter['action_method'] = $translator->trans('page.show', array(), $translationDomain);
        $this->outputParameter['page_description'] = $translator->trans($this->pageDescription, array(), $translationDomain);
        $this->outputParameter['back'] = $request->headers->get('referer');
        $this->outputParameter['action'] = $this->container->getParameter('symfonian_id.admin.grid_action');
        $this->outputParameter['number'] = $this->container->getParameter('symfonian_id.admin.number');
        $this->outputParameter['upload_dir'] = $this->container->getParameter('symfonian_id.admin.upload_dir');

        return $this->render($this->showActionTemplate, $this->outputParameter);
    }

    /**
     * @Route("/{id}/delete/")
     * @Method({"DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {
        $this->isAllowedOr404Error('delete');
        $entity = $this->findOr404Error($id);
        $entityManager = $this->getDoctrine()->getManager();

        $event = new GetEntityResponseEvent();
        $event->setEntity($entity);
        $event->setEntityMeneger($entityManager);

        $this->fireEvent(Event::PRE_DELETE_EVENT, $event);

        if ($event->getResponse()) {

            return $event->getResponse();
        }

        $entityManager->remove($entity);
        $entityManager->flush();

        return new JsonResponse(array('status' => true));
    }

    /**
     * @Route("/list/")
     * @Method({"GET"})
     */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($this->entityClass);

        $qb = $repo->createQueryBuilder(self::ENTITY_ALIAS)
            ->select(self::ENTITY_ALIAS)
            ->addOrderBy(sprintf('%s.%s', self::ENTITY_ALIAS, $this->container->getParameter('symfonian_id.admin.identifier')), 'DESC');
        $filter = $this->normalizeFilter ? strtoupper($request->query->get('filter')) : $request->query->get('filter');

        if ($filter) {
            foreach ($this->filterFields as $key => $value) {
                $qb->orWhere(sprintf('%s.%s LIKE ?%d', self::ENTITY_ALIAS, $value, $key));
                $qb->setParameter($key, strtr('%filter%', array('filter' => $filter)));
            }
        }

        $event = new GetQueryEvent();
        $event->setQueryBuilder($qb);
        $event->setEntityAlias(self::ENTITY_ALIAS);
        $event->setEntityClass($this->entityClass);

        $this->fireEvent(Event::FILTER_LIST_EVENT, $event);

        $page = $request->query->get('page', 1);
        $paginator  = $this->container->get('knp_paginator');

        $pagination = $paginator->paginate($qb, $page, $this->container->getParameter('symfonian_id.admin.per_page'));

        $data = array();
        $identifier = array();
        foreach ($pagination as $key => $record) {
            $temp = array();
            $identifier[$key] = $record->getId();

            foreach ($this->gridFields() as $k => $property) {
                $method = 'get'.ucfirst($property);

                if (method_exists($record, $method)) {
                    array_push($temp, call_user_func_array(array($record, $method), array()));
                } else {
                    $method = 'is'.ucfirst($property);

                    if (method_exists($record, $method)) {
                        array_push($temp, call_user_func_array(array($record, $method), array()));
                    }
                }
            }

            $data[$key] = $temp;
        }

        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        $this->outputParameter['pagination'] = $pagination;
        $this->outputParameter['use_ajax'] = $this->useAjaxList;
        $this->outputParameter['start'] = ($page - 1) * $this->container->getParameter('symfonian_id.admin.per_page');
        $this->outputParameter['menu'] = $this->container->getParameter('symfonian_id.admin.menu');
        $this->outputParameter['header'] = array_merge($this->gridFields(), array('action'));
        $this->outputParameter['page_title'] = $translator->trans($this->pageTitle, array(), $translationDomain);
        $this->outputParameter['action_method'] = $translator->trans('page.list', array(), $translationDomain);
        $this->outputParameter['page_description'] = $translator->trans($this->pageDescription, array(), $translationDomain);
        $this->outputParameter['identifier'] = $identifier;
        $this->outputParameter['action'] = $this->container->getParameter('symfonian_id.admin.grid_action');
        $this->outputParameter['number'] = $this->container->getParameter('symfonian_id.admin.number');
        $this->outputParameter['record'] = $data;
        $this->outputParameter['filter'] = $filter;

        $listTemplate = $request->isXmlHttpRequest() ? $this->listAjaxActionTemplate : $this->listActionTemplate;

        return $this->render($listTemplate, $this->outputParameter);
    }

    protected function handle(Request $request, $data, $template, $action = 'new', $formObject = null)
    {
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');
        $form = $formObject?: $this->getForm($data);

        $event = new GetFormResponseEvent();
        $event->setController($this);
        $event->setFormData($data);
        $event->setForm($form);

        $this->fireEvent(Event::PRE_FORM_SUBMIT_EVENT, $event);

        $response = $event->getResponse();
        if ($response) {

            return $response;
        }
        $form->handleRequest($request);

        if (empty($this->autocomplete)) {
            $this->autocomplete['route'] = 'home';
            $this->autocomplete['value_storage_selector'] = '.selector';
        }

        $this->outputParameter['page_title'] = $translator->trans($this->pageTitle, array(), $translationDomain);
        $this->outputParameter['action_method'] = $translator->trans('page.'.$action, array(), $translationDomain);
        $this->outputParameter['page_description'] = $translator->trans($this->pageDescription, array(), $translationDomain);
        $this->outputParameter['form'] = $form->createView();
        $this->outputParameter['form_theme'] = $this->container->getParameter('symfonian_id.admin.themes.form_theme');
        $this->outputParameter['menu'] = $this->container->getParameter('symfonian_id.admin.menu');
        $this->outputParameter['use_date_picker'] = $this->useDatePicker;
        $this->outputParameter['use_file_style'] = $this->useFileStyle;
        $this->outputParameter['use_editor'] = $this->useEditor;
        $this->outputParameter['autocomplete'] = $this->autocomplete;

        if ($request->isMethod('POST')) {
            $preFormValidationEvent = new GetResponseEvent();
            $preFormValidationEvent->setRequest($request);
            $preFormValidationEvent->setForm($form);

            $this->fireEvent(Event::PRE_FORM_VALIDATION_EVENT, $preFormValidationEvent);

            if (! $form->isValid()) {

                $this->outputParameter['errors'] = true;
            } else {
                $entity = $form->getData();
                $entityManager = $this->getDoctrine()->getManager();

                $preSaveEvent = new GetEntityResponseEvent();
                $preSaveEvent->setRequest($request);
                $preSaveEvent->setEntity($entity);
                $preSaveEvent->setEntityMeneger($entityManager);
                $preSaveEvent->setForm($form);

                $postSaveEvent = new GetEntityEvent();
                $postSaveEvent->setEntityMeneger($entityManager);
                $postSaveEvent->setEntity($entity);

                $this->fireEvent(Event::PRE_SAVE_EVENT, $preSaveEvent);

                $entityManager->persist($entity);
                $entityManager->flush();

                $this->fireEvent(Event::POST_SAVE_EVENT, $postSaveEvent);

                $this->outputParameter['success'] = $translator->trans('message.data_saved', array(), $translationDomain);
            }
        }

        return $this->render($template, $this->outputParameter);
    }

    protected function findOr404Error($id)
    {
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        $entity = $this->getDoctrine()->getManager()->getRepository($this->entityClass)->find($id);

        if (! $entity) {
            throw new NotFoundHttpException($translator->trans('message.data_not_found', array('%id%' => $id), $translationDomain));
        }

        return $entity;
    }

    protected function isAllowedOr404Error($action)
    {
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        if (! in_array($action, $this->container->getParameter('symfonian_id.admin.grid_action'))) {
            throw new NotFoundHttpException($translator->trans('message.request_not_found', array(), $translationDomain));
        }

        return true;
    }

    protected function gridFields()
    {
        if (! empty($this->gridFields)) {

            return $this->gridFields;
        }

        return $this->entityProperties();
    }

    protected function fireEvent($name, $handler)
    {
        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch($name, $handler);
    }

    /**
     * @param boolean $normalizeFilter
     * @return \Symfonian\Indonesia\AdminBundle\Controller\CrudController
     */
    public function normalizeFilter($normalizeFilter = true)
    {
        $this->normalizeFilter = $normalizeFilter;

        return $this;
    }

    /**
     * @param array $fields
     * @return \Symfonian\Indonesia\AdminBundle\Controller\CrudController
     */
    public function setGridFields(array $fields)
    {
        $this->gridFields = $fields;

        return $this;
    }

    /**
     * @param string $template
     * @return \Symfonian\Indonesia\AdminBundle\Controller\CrudController
     */
    public function setNewTemplate($template)
    {
        $this->newActionTemplate = $template;

        return $this;
    }

    /**
     * @param string $template
     * @return \Symfonian\Indonesia\AdminBundle\Controller\CrudController
     */
    public function setEditTemplate($template)
    {
        $this->editActionTemplate = $template;

        return $this;
    }

    /**
     * @param string $template
     * @return \Symfonian\Indonesia\AdminBundle\Controller\CrudController
     */
    public function setShowTemplate($template)
    {
        $this->showActionTemplate = $template;

        return $this;
    }

    /**
     * @param string $template
     * @return \Symfonian\Indonesia\AdminBundle\Controller\CrudController
     */
    public function setListTemplate($template)
    {
        $this->listActionTemplate = $template;

        return $this;
    }

    /**
     * @param string $template
     * @param boolean $useAjax
     * @return \Symfonian\Indonesia\AdminBundle\Controller\CrudController
     */
    public function setListAjaxTemplate($template, $useAjax = true)
    {
        $this->listAjaxActionTemplate = $template;
        $this->useAjaxList = $useAjax;

        return $this;
    }

    public function setFilterFields(array $fields)
    {
        $this->filterFields = $fields;

        return $this;
    }

    /**
     * @param string $javascriptTwigPath
     * @param string $includeRoute
     * @return \Symfonian\Indonesia\AdminBundle\Controller\CrudController
     */
    public function includeJavascript($javascriptTwigPath, array $includeRoute = null)
    {
        $this->outputParameter['include_javascript'] = $javascriptTwigPath;

        if ($includeRoute) {
            $this->outputParameter['include_route'] = $includeRoute;
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
    public function useFileStyle()
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
     *
     * @param string $route
     * @return \Symfonian\Indonesia\AdminBundle\Controller\CrudController
     */
    public function setAutoComplete($route, $valueStorageSelector)
    {
        $this->autocomplete['route'] = $route;
        $this->autocomplete['value_storage_selector'] = $valueStorageSelector;

        return $this;
    }
}
