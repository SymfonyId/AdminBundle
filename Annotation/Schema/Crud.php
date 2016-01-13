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

        if (isset($data['entity'])) {
            $this->entity = $data['entity'];
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

        if (isset($data['showFields'])) {
            if (!is_array($data['showFields'])) {
                $data['showFields'] = (array) $data['showFields'];
            }

            $this->showFields = $data['showFields'];
        }

        unset($data);
    }

    public function getAdd()
    {
        return $this->add;
    }

    public function getEdit()
    {
        return $this->edit;
    }

    public function getList()
    {
        return $this->list;
    }

    public function getAjaxTemplate()
    {
        return $this->ajaxTemplate;
    }

    public function getShow()
    {
        return $this->show;
    }

    public function getForm()
    {
        return $this->form;
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function getShowFields()
    {
        return $this->showFields;
    }
}
