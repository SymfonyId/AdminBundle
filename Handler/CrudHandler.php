<?php

namespace Symfonian\Indonesia\AdminBundle\Handler;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfonian\Indonesia\AdminBundle\Model\EntityInterface;
use Symfonian\Indonesia\AdminBundle\Form\GenericFormType;
use Symfonian\Indonesia\AdminBundle\Event\GetDataEvent;
use Symfonian\Indonesia\AdminBundle\Event\GetEntityEvent;
use Symfonian\Indonesia\AdminBundle\Event\GetEntityResponseEvent;
use Symfonian\Indonesia\AdminBundle\Event\GetFormResponseEvent;
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

    protected $container;

    protected $manager;

    protected $repository;

    protected $class;

    protected $template;

    protected $viewParams = array();

    protected $showFields = array();

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

    public function setShowFields(array $showFields)
    {
        $this->showFields = $showFields;
    }

    public function show(Request $request, EntityInterface $data)
    {
        $session = $this->container->get('session');

        $referer = $session->get('referer');
        $refererHeader = $request->headers->get('referer');
        if ($refererHeader) {
            $referer = $refererHeader;
            $session->set('referer', $refererHeader);
        }

        $output = array();
        foreach ($this->showFields as $key => $property) {
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

        $this->fireEvent(Event::PRE_SHOW_EVENT, $event);

        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        $viewParams['data'] = $data;
        $viewParams['menu'] = $this->container->getParameter('symfonian_id.admin.menu');
        $viewParams['action_method'] = $translator->trans('page.show', array(), $translationDomain);
        $viewParams['back'] = $referer;
        $viewParams['action'] = $this->container->getParameter('symfonian_id.admin.grid_action');
        $viewParams['number'] = $this->container->getParameter('symfonian_id.admin.number');
        $viewParams['upload_dir'] = $this->container->getParameter('symfonian_id.admin.upload_dir');

        $this->viewParams = array_merge($this->viewParams, $viewParams);
    }

    public function createNewOrUpdate(Request $request, EntityInterface $data, FormInterface $form = null)
    {
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');
        $form = $form ?: $this->getForm($data);

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

        $viewParams['form'] = $form->createView();
        $viewParams['form_theme'] = $this->container->getParameter('symfonian_id.admin.themes.form_theme');
        $viewParams['menu'] = $this->container->getParameter('symfonian_id.admin.menu');

        if ($request->isMethod('POST')) {
            $preFormValidationEvent = new GetResponseEvent();
            $preFormValidationEvent->setRequest($request);
            $preFormValidationEvent->setForm($form);

            $this->fireEvent(Event::PRE_FORM_VALIDATION_EVENT, $preFormValidationEvent);

            $response = $preFormValidationEvent->getResponse();
            if ($response) {
                return $response;
            }

            if (!$form->isValid()) {
                $viewParams['errors'] = true;
            } else {
                $entity = $form->getData();

                $preSaveEvent = new GetEntityResponseEvent();
                $preSaveEvent->setRequest($request);
                $preSaveEvent->setEntity($entity);
                $preSaveEvent->setEntityMeneger($this->manager);
                $preSaveEvent->setForm($form);

                $postSaveEvent = new GetEntityEvent();
                $postSaveEvent->setEntityMeneger($this->manager);
                $postSaveEvent->setEntity($entity);

                $this->fireEvent(Event::PRE_SAVE_EVENT, $preSaveEvent);

                $this->manager->persist($entity);
                $this->manager->flush();

                $this->fireEvent(Event::POST_SAVE_EVENT, $postSaveEvent);

                $viewParams['success'] = $translator->trans('message.data_saved', array(), $translationDomain);
            }
        }

        $this->viewParams = array_merge($this->viewParams, $viewParams);
    }

    protected function getForm(EntityInterface $data)
    {
        try {
            $formType = $this->container->get($this->formClass);
        } catch (\Exception $exception) {
            if ($this->formClass) {
                $formType = new $this->formClass();
            } else {
                $formType = new GenericFormType($this, $this->container);
            }
        }

        $form = $this->createForm($formType);
        $form->setData($data);

        return $form;
    }

    protected function fireEvent($name, $handler)
    {
        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch($name, $handler);
    }
}
