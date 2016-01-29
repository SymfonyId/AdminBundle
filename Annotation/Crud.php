<?php

namespace Symfonian\Indonesia\AdminBundle\Annotation;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin.
 */
use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationInterface;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Crud implements ConfigurationInterface
{
    protected $showFields = array();

    protected $entity;

    protected $form;

    protected $create = 'SymfonianIndonesiaAdminBundle:Crud:new.html.twig';

    protected $edit = 'SymfonianIndonesiaAdminBundle:Crud:new.html.twig';

    protected $show = 'SymfonianIndonesiaAdminBundle:Crud:show.html.twig';

    protected $list = 'SymfonianIndonesiaAdminBundle:Crud:list.html.twig';

    protected $ajaxTemplate = 'SymfonianIndonesiaAdminBundle:Crud:list_template.html.twig';

    public function __construct(array $data = array())
    {
        if (isset($data['value'])) {
            $this->entity = $data['value'];
        }

        if (isset($data['entity'])) {
            $this->entity = $data['entity'];
        }

        if (isset($data['create'])) {
            $this->create = $data['create'];
        }

        if (isset($data['edit'])) {
            $this->edit = $data['edit'];
        }

        if (isset($data['list'])) {
            $this->list = $data['list'];
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

    public function getCreateTemplate()
    {
        return $this->create;
    }

    public function setCreateTemplate($createTemplate)
    {
        $this->create = $createTemplate;
    }

    public function getEditTemplate()
    {
        return $this->edit;
    }

    public function setEditTemplate($editTemplate)
    {
        $this->edit = $editTemplate;
    }

    public function getListTemplate()
    {
        return $this->list;
    }

    public function setListTemplate($listTemplate)
    {
        $this->list = $listTemplate;
    }

    public function getShowTemplate()
    {
        return $this->show;
    }

    public function setShowTemplate($showTemplate)
    {
        $this->show = $showTemplate;
    }

    public function getAjaxTemplate()
    {
        return $this->ajaxTemplate;
    }

    public function getFormClass()
    {
        return $this->form;
    }

    public function setFormClass($formClass)
    {
        $this->form = $formClass;
    }

    public function getEntityClass()
    {
        return $this->entity;
    }

    public function setEntityClass($entityClass)
    {
        $this->entity = $entityClass;
    }

    public function getShowFields()
    {
        return $this->showFields;
    }

    public function setShowFields($showFields)
    {
        $this->showFields = $showFields;
    }

    public function getName()
    {
        return 'crud';
    }
}
