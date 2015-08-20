<?php

namespace Symfonian\Indonesia\AdminBundle\Event;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\EventDispatcher\Event;
use Doctrine\Common\Persistence\ObjectManager;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface;

class FilterRequestEvent extends Event
{
    protected $request;

    protected $response;

    protected $form;

    protected $formData;

    protected $entity;

    protected $entityManager;
    
    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param FormInterface $form
     */
    public function setForm(FormInterface $form)
    {
        $this->form = $form;
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param mixed $entity
     */
    public function setData($entity)
    {
        $this->formData = $entity;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->formData;
    }

    /**
     * @param ObjectManager $entityManager
     */
    public function setEntityMeneger(ObjectManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return ObjectManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param EntityInterface $entity
     */
    public function setEntity(EntityInterface $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }
}