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

    public function handleRequest(Request $request, $action, $template, EntityInterface $data = null, FormInterface $form = null)
    {
        switch ($action) {
            case self::ACTION_CREATE:
                $this->createNewOrUpdate($request, $entity, $form);
                break;
            case self::ACTION_UPDATE:
                $this->createNewOrUpdate($request, $entity, $form);
                break;
            case self::ACTION_READ:
                $this->getList();
                break;
            case self::ACTION_DELETE:
                $this->delete();
                break;
            default:
                throw new \Exception('Unknow Action.');
                break;
        }

        $this->template = $template;
    }

    protected function createNewOrUpdate(Request $request, EntityInterface $data, FormInterface $form = null)
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

    public function getResponse()
    {
        return $this->container->get('templating')->renderResponse($this->template, $this->viewParams);
    }
}
