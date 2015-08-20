<?php

namespace Symfonian\Indonesia\AdminBundle\Handler;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfonian\Indonesia\AdminBundle\Controller\CrudController;
use Symfonian\Indonesia\AdminBundle\Model\EntityInterface;
use Symfonian\Indonesia\AdminBundle\Event\GetDataEvent;
use Symfonian\Indonesia\AdminBundle\Event\GetEntityEvent;
use Symfonian\Indonesia\AdminBundle\Event\GetEntityResponseEvent;
use Symfonian\Indonesia\AdminBundle\Event\GetFormResponseEvent;
use Symfonian\Indonesia\AdminBundle\Event\GetQueryEvent;
use Symfonian\Indonesia\AdminBundle\Event\GetResponseEvent;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminEvents as Event;

class CrudHandler
{
    const ACTION_CREATE = 'ACTION_CREATE';

    const ACTION_UPDATE = 'ACTION_UPDATE';

    const ACTION_DELETE = 'ACTION_DELETE';

    const ACTION_READ = 'ACTION_READ';

    const GRID_ACTION_SHOW = 'GRID_ACTION_SHOW';

    const GRID_ACTION_EDIT = 'GRID_ACTION_EDIT';

    const GRID_ACTION_DELETE = 'GRID_ACTION_DELETE';

    const ENTITY_ALIAS = 'e';

    protected $container;

    protected $manager;

    protected $repository;

    protected $class;

    protected $template;

    protected $viewParams = array();

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->manager = $container->get('doctrine.orm.entity_manager');
    }

    public function getResponse()
    {
        return $this->container->get('templating')->renderResponse($this->template, $this->viewParams);
    }

    public function setViewParams(array $viewParams)
    {
        $this->viewParams = array_merge($this->viewParams, $viewParams);
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function setEntityClass($class)
    {
        $this->repository = $this->manager->getRepository($class);
        $this->class = $this->manager->getClassMetadata($class)->getName();
    }

    public function viewList(Request $request, array $gridFields, array $filterFields, $normalizeFilter = false)
    {
        $queryBuilder = $this->repository->createQueryBuilder(self::ENTITY_ALIAS);
        $queryBuilder->addOrderBy(sprintf('%s.%s', self::ENTITY_ALIAS, $this->container->getParameter('symfonian_id.admin.identifier')), 'DESC');
        $filter = $normalizeFilter ? strtoupper($request->query->get('filter')) : $request->query->get('filter');

        if ($filter) {
            foreach ($filterFields as $key => $value) {
                $queryBuilder->orWhere(sprintf('%s.%s LIKE ?%d', self::ENTITY_ALIAS, $value, $key));
                $queryBuilder->setParameter($key, strtr('%filter%', array('filter' => $filter)));
            }
        }

        $event = new GetQueryEvent();
        $event->setQueryBuilder($queryBuilder);
        $event->setEntityAlias(self::ENTITY_ALIAS);
        $event->setEntityClass($this->class);

        $this->fireEvent(Event::FILTER_LIST, $event);

        $page = $request->query->get('page', 1);
        $perPage = $this->container->getParameter('symfonian_id.admin.per_page');
        $paginator = $this->container->get('knp_paginator');

        $pagination = $paginator->paginate($queryBuilder, $page, $perPage);

        $data = array();
        $identifier = array();
        foreach ($pagination as $key => $record) {
            $temp = array();
            $identifier[$key] = $record->getId();

            foreach ($gridFields as $k => $property) {
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

        $viewParams['pagination'] = $pagination;
        $viewParams['start'] = ($page - 1) * $perPage;
        $viewParams['menu'] = $this->container->getParameter('symfonian_id.admin.menu');
        $viewParams['header'] = array_merge($gridFields, array('action'));
        $viewParams['action_method'] = $translator->trans('page.list', array(), $translationDomain);
        $viewParams['identifier'] = $identifier;
        $viewParams['action'] = $this->container->getParameter('symfonian_id.admin.grid_action');
        $viewParams['number'] = $this->container->getParameter('symfonian_id.admin.number');
        $viewParams['record'] = $data;
        $viewParams['filter'] = $filter;

        $this->viewParams = array_merge($this->viewParams, $viewParams);
    }

    public function remove(EntityInterface $data)
    {
        $event = new GetEntityResponseEvent();
        $event->setEntity($data);
        $event->setEntityMeneger($this->manager);

        $this->fireEvent(Event::PRE_DELETE, $event);

        if ($event->getResponse()) {
            return $event->getResponse();
        }

        $this->manager->remove($data);
        $this->manager->flush();

        return true;
    }

    public function showDetail(Request $request, EntityInterface $data, array $showFields)
    {
        $session = $this->container->get('session');

        $referer = $session->get('referer');
        $refererHeader = $request->headers->get('referer');
        if ($refererHeader) {
            $referer = $refererHeader;
            $session->set('referer', $refererHeader);
        }

        $output = array();
        foreach ($showFields as $key => $property) {
            $method = 'get'.ucfirst($property);

            if (method_exists($data, $method)) {
                array_push($output, array(
                    'name' => $property,
                    'value' => call_user_func_array(array($data, $method), array()),
                ));
            } else {
                $method = 'is'.ucfirst($property);

                if (method_exists($data, $method)) {
                    array_push($output, array(
                        'name' => $property,
                        'value' => call_user_func_array(array($data, $method), array()),
                    ));
                }
            }
        }

        $event = new GetDataEvent();
        $event->setData($output);

        $this->fireEvent(Event::PRE_SHOW, $event);

        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        $viewParams['data'] = $output;
        $viewParams['menu'] = $this->container->getParameter('symfonian_id.admin.menu');
        $viewParams['action_method'] = $translator->trans('page.show', array(), $translationDomain);
        $viewParams['back'] = $referer;
        $viewParams['action'] = $this->container->getParameter('symfonian_id.admin.grid_action');
        $viewParams['number'] = $this->container->getParameter('symfonian_id.admin.number');
        $viewParams['upload_dir'] = $this->container->getParameter('symfonian_id.admin.upload_dir');

        $this->viewParams = array_merge($this->viewParams, $viewParams);
    }

    public function createNewOrUpdate(CrudController $controller, Request $request, EntityInterface $data, FormInterface $form = null)
    {
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        $event = new GetFormResponseEvent();
        $event->setController($controller);
        $event->setFormData($data);
        $event->setForm($form);

        $this->fireEvent(Event::PRE_FORM_SUBMIT, $event);

        $response = $event->getResponse();
        if ($response) {
            return $response;
        }

        $form->handleRequest($request);

        $viewParams['form'] = $form->createView();
        $viewParams['form_theme'] = $this->container->getParameter('symfonian_id.admin.themes.form_theme');
        $viewParams['menu'] = $this->container->getParameter('symfonian_id.admin.menu');

        if ($request->isMethod('POST')) {
            $preFormValidationEvent = new GetResponseEvent();
            $preFormValidationEvent->setRequest($request);
            $preFormValidationEvent->setForm($form);

            $this->fireEvent(Event::PRE_FORM_VALIDATION, $preFormValidationEvent);

            $response = $preFormValidationEvent->getResponse();
            if ($response) {
                return $response;
            }

            if (!$form->isValid()) {
                $viewParams['errors'] = true;
            } else {
                $data = $form->getData();

                $preSaveEvent = new GetEntityResponseEvent();
                $preSaveEvent->setRequest($request);
                $preSaveEvent->setEntity($data);
                $preSaveEvent->setEntityMeneger($this->manager);
                $preSaveEvent->setForm($form);

                $postSaveEvent = new GetEntityEvent();
                $postSaveEvent->setEntityMeneger($this->manager);
                $postSaveEvent->setEntity($data);

                $this->fireEvent(Event::PRE_SAVE, $preSaveEvent);

                $this->manager->persist($data);
                $this->manager->flush();

                $this->fireEvent(Event::POST_SAVE, $postSaveEvent);

                $viewParams['success'] = $translator->trans('message.data_saved', array(), $translationDomain);
            }
        }

        $this->viewParams = array_merge($this->viewParams, $viewParams);
    }

    protected function fireEvent($name, $handler)
    {
        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch($name, $handler);
    }
}
