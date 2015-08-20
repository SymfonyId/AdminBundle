<?php

namespace Symfonian\Indonesia\AdminBundle\Annotation\Schema;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin.
 */

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Crud
{
    private $add;

    private $edit;

    private $list;

    private $ajaxTemplate;

    private $show;

    private $form;

    private $entity;

    private $showFields;

    public function __construct(array $data)
    {
        if (isset($data['value'])) {
            $this->entity = $data['value'];
        }

        if (isset($data['add'])) {
            $this->add = $data['add'];
        }

        if (isset($data['edit'])) {
            $this->edit = $data['edit'];
        }

        if (isset($data['list'])) {
            $this->list = $data['list'];
        }

        if (isset($data['ajaxTemplate'])) {
            $this->ajaxTemplate = $data['ajaxTemplate'];
        }

        if (isset($data['show'])) {
            $this->show = $data['show'];
        }

        if (isset($data['form'])) {
            $this->form = $data['form'];
        }

        if (isset($data['entity'])) {
            $this->entity = $data['entity'];
        }

        if (isset($data['showFields'])) {
            $this->showFields = $data['showFields'];
        }

        unset($data);
    }

    public function setAdd($add)
    {
        $this->add = $add;
    }

    public function getAdd()
    {
        return $this->add;
    }

    public function setEdit($edit)
    {
        $this->edit = $edit;
    }

    public function getEdit()
    {
        return $this->edit;
    }

    public function setList($list)
    {
        $this->list = $list;
    }

    public function getList()
    {
        return $this->list;
    }

    public function setAjaxTemplate($ajaxTemplate)
    {
        $this->ajaxTemplate = $ajaxTemplate;
    }

    public function getAjaxTemplate()
    {
        return $this->ajaxTemplate;
    }

    public function setShow($show)
    {
        $this->show = $show;
    }

    public function getShow()
    {
        return $this->show;
    }

    public function setForm($form)
    {
        $this->form = $form;
    }

    public function getForm()
    {
        return $this->form;
    }

    public function setEntity($entity)
    {
        if (!is_subclass_of($entity, 'Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface')) {
            throw new \InvalidArgumentException(sprintf('Entity %s must implement Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface', $entity));
        }

        $this->entity = $entity;
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function setShowFields($fields)
    {
        $this->showFields = $fields;
    }

    public function getShowFields()
    {
        return $this->showFields;
    }
}
