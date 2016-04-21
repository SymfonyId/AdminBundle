<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Annotation;

use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationInterface;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants as Constants;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;

/**
 * @Annotation
 * @Target({"CLASS"})
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class Crud implements ConfigurationInterface, ContainerAwareInterface
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Entity fields you want to display.
     *
     * Ex: @Crud(showFields={"first_name", "last_name"})
     *
     * @var array
     */
    private $showFields = array();

    /**
     * Ex: @Crud(entity="AppBundle/Entity/Product").
     *
     * @var string
     */
    private $entity;

    /**
     * Ex: @Crud(form="AppBundle/Form/ProductType").
     *
     * @var string
     */
    private $form;

    /**
     * @var string
     */
    private $menuIcon = 'fa-bars';

    /**
     * @var string
     */
    private $create = Constants::TEMPLATE_CREATE;

    /**
     * @var string
     */
    private $bulkCreate = Constants::TEMPLATE_BULK_CREATE;

    /**
     * @var string
     */
    private $edit = Constants::TEMPLATE_EDIT;

    /**
     * @var string
     */
    private $show = Constants::TEMPLATE_SHOW;

    /**
     * @var string
     */
    private $list = Constants::TEMPLATE_LIST;

    /**
     * Internal use only.
     *
     * @var string
     */
    private $ajaxTemplate = Constants::TEMPLATE_AJAX;

    /**
     * @var bool
     */
    private $allowCreate = true;

    /**
     * @var bool
     */
    private $allowEdit = true;

    /**
     * @var bool
     */
    private $allowShow = true;

    /**
     * @var bool
     */
    private $allowDelete = true;

    /**
     * @param array $data
     */
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

        if (isset($data['bulkCreate'])) {
            $this->bulkCreate = $data['bulkCreate'];
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

        if (isset($data['menuIcon'])) {
            $this->menuIcon = $data['menuIcon'];
        }

        if (isset($data['showFields'])) {
            $this->setShowFields((array) $data['showFields']);
        }

        if (isset($data['allowCreate'])) {
            $this->allowCreate = (bool) $data['allowCreate'];
        }

        if (isset($data['allowEdit'])) {
            $this->allowEdit = (bool) $data['allowEdit'];
        }

        if (isset($data['allowShow'])) {
            $this->allowShow = (bool) $data['allowShow'];
        }

        if (isset($data['allowDelete'])) {
            $this->allowDelete = (bool) $data['allowDelete'];
        }

        unset($data);
    }

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param FormFactory $formFactory
     */
    public function setFormFactory(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @return string
     */
    public function getCreateTemplate()
    {
        return $this->create;
    }

    /**
     * @param string $createTemplate
     */
    public function setCreateTemplate($createTemplate)
    {
        $this->create = $createTemplate;
    }

    /**
     * @return string
     */
    public function getBulkCreateTemplate()
    {
        return $this->bulkCreate;
    }

    /**
     * @param string $bulkCreateTemplate
     */
    public function setBulkCreateTemplate($bulkCreateTemplate)
    {
        $this->bulkCreate = $bulkCreateTemplate;
    }

    /**
     * @return string
     */
    public function getEditTemplate()
    {
        return $this->edit;
    }

    /**
     * @param string $editTemplate
     */
    public function setEditTemplate($editTemplate)
    {
        $this->edit = $editTemplate;
    }

    /**
     * @return string
     */
    public function getListTemplate()
    {
        return $this->list;
    }

    /**
     * @param string $listTemplate
     */
    public function setListTemplate($listTemplate)
    {
        $this->list = $listTemplate;
    }

    /**
     * @return string
     */
    public function getShowTemplate()
    {
        return $this->show;
    }

    /**
     * @param string $showTemplate
     */
    public function setShowTemplate($showTemplate)
    {
        $this->show = $showTemplate;
    }

    /**
     * @return string
     */
    public function getAjaxTemplate()
    {
        return $this->ajaxTemplate;
    }

    /**
     * @return string
     */
    public function getFormClass()
    {
        return $this->form;
    }

    /**
     * @param string $formClass
     */
    public function setFormClass($formClass)
    {
        $this->form = $formClass;
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entity;
    }

    /**
     * @param string $entityClass
     */
    public function setEntityClass($entityClass)
    {
        $this->entity = $entityClass;
    }

    /**
     * @return array
     */
    public function getShowFields()
    {
        return $this->showFields;
    }

    /**
     * @param array $showFields
     */
    public function setShowFields(array $showFields)
    {
        $this->showFields = $showFields;
    }

    /**
     * @param bool $allowCreate
     */
    public function setAllowCreate($allowCreate)
    {
        $this->allowCreate = $allowCreate;
    }

    /**
     * @param bool $allowEdit
     */
    public function setAllowEdit($allowEdit)
    {
        $this->allowEdit = $allowEdit;
    }

    /**
     * @param bool $allowShow
     */
    public function setAllowShow($allowShow)
    {
        $this->allowShow = $allowShow;
    }

    /**
     * @param bool $allowDelete
     */
    public function setAllowDelete($allowDelete)
    {
        $this->allowDelete = $allowDelete;
    }

    /**
     * @return bool
     */
    public function isAllowDelete()
    {
        return $this->allowDelete;
    }

    /**
     * @return bool
     */
    public function isAllowShow()
    {
        return $this->allowShow;
    }

    /**
     * @return bool
     */
    public function isAllowEdit()
    {
        return $this->allowEdit;
    }

    /**
     * @return bool
     */
    public function isAllowCreate()
    {
        return $this->allowCreate;
    }

    /**
     * @param EntityInterface | null $formData
     *
     * @return FormInterface
     */
    public function getForm($formData = null)
    {
        $formClass = $this->getFormClass();

        if (class_exists($formClass)) {
            $formObject = new $formClass();
        } else {
            $formObject = $this->container->get($formClass);
        }

        $form = $this->formFactory->create(get_class($formObject));
        $form->setData($formData);

        return $form;
    }

    /**
     * @return array
     */
    public function getAction()
    {
        $action = array();

        if ($this->isAllowEdit()) {
            $action[] = Constants::GRID_ACTION_EDIT;
        }
        if ($this->isAllowShow()) {
            $action[] = Constants::GRID_ACTION_SHOW;
        }
        if ($this->isAllowDelete()) {
            $action[] = Constants::GRID_ACTION_DELETE;
        }

        return $action;
    }

    /**
     * @return string
     */
    public function getMenuIcon()
    {
        return $this->menuIcon;
    }
}
