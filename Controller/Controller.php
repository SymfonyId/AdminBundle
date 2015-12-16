<?php

namespace Symfonian\Indonesia\AdminBundle\Controller;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Form\GenericFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

abstract class Controller extends BaseController
{
    protected $viewParams = array();

    protected $title = 'SIAB';

    protected $description = 'Symfonian Indonesia Admin bundle';

    protected $showFields = array();

    protected $entity;

    protected $form;

    public function getEntityFields()
    {
        $fields = array();
        $reflection = new \ReflectionClass($this->entity);
        $reflection->getProperties();

        foreach ($reflection->getProperties() as $key => $property) {
            $fields[$key] = $property->getName();
        }

        return $fields;
    }

    protected function showFields()
    {
        if (!empty($this->showFields)) {
            return $this->showFields;
        }

        return $this->getEntityFields();
    }

    /**
     * @param array $fields
     */
    public function setShowFields(array $fields)
    {
        $this->showFields = $fields;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    protected function getForm($data = null)
    {
        try {
            $formObject = $this->container->get($this->form);
        } catch (\Exception $ex) {
            if ($this->form) {
                $formObject = new $this->form();
            } else {
                $formObject = new GenericFormType($this, $this->container);
            }
        }

        $form = $this->createForm(get_class($formObject));
        $form->setData($data);

        return $form;
    }

    /**
     * @param string $form
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * @param string $entity
     *
     * @return \Symfonian\Indonesia\AdminBundle\Controller\CrudController
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
