<?php

namespace Symfonian\Indonesia\AdminBundle\Event;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfony\Component\Form\FormInterface;

class FilterRequestEvent extends FilterEntityEvent
{
    protected $form;

    protected $formData;

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
}
