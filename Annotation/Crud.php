<?php

namespace Symfonian\Indonesia\AdminBundle\Annotation;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin.
 */
use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationInterface;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;

/**
 * @Annotation
 * @Target({"CLASS"})
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

    private $create = 'SymfonianIndonesiaAdminBundle:Crud:new.html.twig';

    private $edit = 'SymfonianIndonesiaAdminBundle:Crud:new.html.twig';

    private $show = 'SymfonianIndonesiaAdminBundle:Crud:show.html.twig';

    private $list = 'SymfonianIndonesiaAdminBundle:Crud:list.html.twig';

    private $ajaxTemplate = 'SymfonianIndonesiaAdminBundle:Crud:list_template.html.twig';

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
            $this->setShowFields((array) $data['showFields']);
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
        $this->showFields = array_unique(array_merge($this->showFields, $showFields));
    }

    /**
     * @param EntityInterface | null $formData
     *
     * @return FormInterface
     */
    public function getForm($formData = null)
    {
        $formClass = $this->getFormClass();
        try {
            $formObject = $this->container->get($formClass);
        } catch (\Exception $ex) {
            $formObject = new $formClass();
        }

        $form = $this->formFactory->create(get_class($formObject));
        $form->setData($formData);

        return $form;
    }

    public function getName()
    {
        return 'crud';
    }
}
