<?php

namespace Symfonian\Indonesia\AdminBundle\Event;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormInterface;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface;

class GetFormEvent extends Event
{
    protected $form;

    protected $formData;

    public function setForm(FormInterface $form)
    {
        $this->form = $form;
    }

    public function getForm()
    {
        return $this->form;
    }

    public function setData(EntityInterface $entity)
    {
        $this->formData = $entity;
    }

    public function getData()
    {
        return $this->formData;
    }
}