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

    private $showFields = array();
    private $entity;
    private $form;
    private $menuIcon = 'fa-bars';
    private $create = Constants::TEMPLATE_CREATE;
    private $edit = Constants::TEMPLATE_EDIT;
    private $show = Constants::TEMPLATE_SHOW;
    private $list = Constants::TEMPLATE_LIST;
    private $ajaxTemplate = Constants::TEMPLATE_AJAX;
    private $allowCreate = true;
    private $allowEdit = true;
    private $allowShow = true;
    private $allowDelete = true;

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
