<?php

namespace Symfonian\Indonesia\AdminBundle\Event;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Controller\CrudController;
use Symfonian\Indonesia\AdminBundle\Model\EntityInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;

class GetFormResponseEvent extends Event
{
    protected $data;

    protected $controller;

    protected $response;

    protected $form;

    public function setForm(FormInterface $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    public function setController(CrudController $controller)
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * @return CrudController
     */
    public function getController()
    {
        return $this->controller;
    }

    public function setFormData(EntityInterface $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return EntityInterface
     */
    public function getFormData()
    {
        return $this->data;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
