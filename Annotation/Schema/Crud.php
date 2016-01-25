<?php

namespace Symfonian\Indonesia\AdminBundle\Annotation\Schema;

/**
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

    protected $entityClass;

    protected $formClass;

    protected $addTemplate = 'SymfonianIndonesiaAdminBundle:Crud:new.html.twig';

    protected $editTemplate = 'SymfonianIndonesiaAdminBundle:Crud:new.html.twig';

    protected $showTemplate = 'SymfonianIndonesiaAdminBundle:Crud:show.html.twig';

    protected $listTemplate = 'SymfonianIndonesiaAdminBundle:Crud:list.html.twig';

    protected $ajaxTemplate = 'SymfonianIndonesiaAdminBundle:Crud:list_template.html.twig';

    protected $useAjax = true;

    public function __construct(array $data = array())
    {
        if (isset($data['value'])) {
            $this->entityClass = $data['value'];
        }

        if (isset($data['entity'])) {
            $this->entityClass = $data['entity'];
        }

        if (isset($data['add'])) {
            $this->addTemplate = $data['add'];
        }

        if (isset($data['edit'])) {
            $this->editTemplate = $data['edit'];
        }

        if (isset($data['list'])) {
            $this->listTemplate = $data['list'];
        }

        if (isset($data['ajaxTemplate'])) {
            $this->ajaxTemplate = $data['ajaxTemplate'];
            $this->useAjax = true;
        }

        if (isset($data['show'])) {
            $this->showTemplate = $data['show'];
        }

        if (isset($data['form'])) {
            $this->formClass = $data['form'];
        }

        if (isset($data['showFields'])) {
            if (!is_array($data['showFields'])) {
                $data['showFields'] = (array) $data['showFields'];
            }

            $this->showFields = $data['showFields'];
        }

        unset($data);
    }

    public function getAddTemplate()
    {
        return $this->addTemplate;
    }

    public function setAddTemplate($addTemplate)
    {
        $this->addTemplate = $addTemplate;
    }

    public function getEditTemplate()
    {
        return $this->editTemplate;
    }

    public function setEditTemplate($editTemplate)
    {
        $this->editTemplate = $editTemplate;
    }

    public function getListTemplate()
    {
        return $this->listTemplate;
    }

    public function setListTemplate($listTemplate)
    {
        $this->listTemplate = $listTemplate;
    }

    public function getShowTemplate()
    {
        return $this->showTemplate;
    }

    public function setShowTemplate($showTemplate)
    {
        $this->showTemplate = $showTemplate;
    }

    public function getAjaxTemplate()
    {
        return $this->ajaxTemplate;
    }

    public function isUseAjax()
    {
        return $this->useAjax;
    }

    public function setAjaxTemplate($ajaxTemplate, $useAjax = true)
    {
        $this->ajaxTemplate = $ajaxTemplate;
        $this->useAjax = $useAjax;
    }

    public function getFormClass()
    {
        return $this->formClass;
    }

    public function getEntityClass()
    {
        return $this->entityClass;
    }

    public function getShowFields()
    {
        return $this->showFields;
    }

    public function getName()
    {
        return 'crud';
    }
}
