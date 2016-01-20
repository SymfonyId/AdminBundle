<?php

namespace Symfonian\Indonesia\AdminBundle\Handler;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;

class ConfigurationHandler
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    protected $formFactory;

    protected $title = 'SIAB';

    protected $description = 'Symfonian Indonesia Admin Bundle';

    protected $showFields = array();

    protected $entityClass;

    protected $formClass;

    protected $normalizeFilter = false;

    protected $formatNumber = true;

    protected $gridFields = array();

    protected $newTemplate = 'SymfonianIndonesiaAdminBundle:Crud:new.html.twig';

    protected $editTemplate = 'SymfonianIndonesiaAdminBundle:Crud:new.html.twig';

    protected $showTemplate = 'SymfonianIndonesiaAdminBundle:Crud:show.html.twig';

    protected $listTemplate = 'SymfonianIndonesiaAdminBundle:Crud:list.html.twig';

    protected $ajaxTemplate = 'SymfonianIndonesiaAdminBundle:Crud:list_template.html.twig';

    protected $useAjax = false;

    protected $useDatePicker = false;

    protected $useFileStyle = false;

    protected $useEditor = false;

    protected $autocomplete = array();

    protected $javascript = array();

    protected $filterFields = array();

    public function __construct(ContainerInterface $container, FormFactory $formFactory)
    {
        $this->container = $container;
        $this->formFactory = $formFactory;

        $this->autocomplete['route'] = 'home';
        $this->autocomplete['value_storage_selector'] = '.selector';
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return array
     */
    public function getShowFields()
    {
        if (!empty($this->showFields)) {
            return $this->showFields;
        }

        return $this->getEntityFields();
    }

    /**
     * @param array $showFields
     */
    public function setShowFields($showFields)
    {
        $this->showFields = $showFields;
    }

    /**
     * @return mixed
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * @param string $entityClass
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
    }

    /**
     * @param EntityInterface | null $formData
     *
     * @return FormInterface
     */
    public function getForm($formData = null)
    {
        $options = array();
        try {
            $formObject = $this->container->get($this->formClass);
        } catch (\Exception $ex) {
            if ($this->formClass) {
                $formObject = new $this->formClass();
            } else {
                $formObject = $this->container->get('symfonian_id.core.generic_form');
                $formObject->setEntity($this->getEntityClass());
                $formObject->setTranslationDomain($this->container->getParameter('symfonian_id.admin.translation_domain'));
                $options = array(
                    'fields' => $this->getEntityFields(),
                    'attr' => array(
                        'style' => 'form-control',
                    ),
                );
            }
        }

        $form = $this->formFactory->create(get_class($formObject), null, $options);
        $form->setData($formData);

        return $form;
    }

    /**
     * @param string $formClass
     */
    public function setFormClass($formClass)
    {
        $this->formClass = $formClass;
    }

    /**
     * @return bool
     */
    public function isNormalizeFilter()
    {
        return $this->normalizeFilter;
    }

    /**
     * @param bool $normalizeFilter
     */
    public function setNormalizeFilter($normalizeFilter)
    {
        $this->normalizeFilter = $normalizeFilter;
    }

    /**
     * @return bool
     */
    public function isFormatNumber()
    {
        return $this->formatNumber;
    }

    /**
     * @param bool $formatNumber
     */
    public function setFormatNumber($formatNumber)
    {
        $this->formatNumber = $formatNumber;
    }

    /**
     * @return array
     */
    public function getGridFields()
    {
        if (!empty($this->gridFields)) {
            return $this->gridFields;
        }

        return $this->getEntityFields();
    }

    /**
     * @param array $gridFields
     */
    public function setGridFields($gridFields)
    {
        $this->gridFields = $gridFields;
    }

    /**
     * @return string
     */
    public function getNewTemplate()
    {
        return $this->newTemplate;
    }

    /**
     * @param string $newTemplate
     */
    public function setNewTemplate($newTemplate)
    {
        $this->newTemplate = $newTemplate;
    }

    /**
     * @return string
     */
    public function getEditTemplate()
    {
        return $this->editTemplate;
    }

    /**
     * @param string $editTemplate
     */
    public function setEditTemplate($editTemplate)
    {
        $this->editTemplate = $editTemplate;
    }

    /**
     * @return string
     */
    public function getShowTemplate()
    {
        return $this->showTemplate;
    }

    /**
     * @param string $showTemplate
     */
    public function setShowTemplate($showTemplate)
    {
        $this->showTemplate = $showTemplate;
    }

    /**
     * @return string
     */
    public function getListTemplate()
    {
        return $this->listTemplate;
    }

    /**
     * @param string $listTemplate
     */
    public function setListTemplate($listTemplate)
    {
        $this->listTemplate = $listTemplate;
    }

    /**
     * @param string $template
     * @param bool   $useAjax
     */
    public function setAjaxTemplate($template, $useAjax = true)
    {
        $this->ajaxTemplate = $template;
        $this->useAjax = $useAjax;
    }

    /**
     * @return string
     */
    public function getAjaxTemplate()
    {
        return $this->ajaxTemplate;
    }

    /**
     * @return bool
     */
    public function isUseAjax()
    {
        return $this->useAjax;
    }

    /**
     * @return bool
     */
    public function isUseDatePicker()
    {
        return $this->useDatePicker;
    }

    /**
     * @param bool $useDatePicker
     */
    public function setUseDatePicker($useDatePicker)
    {
        $this->useDatePicker = $useDatePicker;
    }

    /**
     * @return bool
     */
    public function isUseFileStyle()
    {
        return $this->useFileStyle;
    }

    /**
     * @param bool $useFileStyle
     */
    public function setUseFileStyle($useFileStyle)
    {
        $this->useFileStyle = $useFileStyle;
    }

    /**
     * @return bool
     */
    public function isUseEditor()
    {
        return $this->useEditor;
    }

    /**
     * @param bool $useEditor
     */
    public function setUseEditor($useEditor)
    {
        $this->useEditor = $useEditor;
    }

    /**
     * @return array
     */
    public function getAutocomplete()
    {
        return $this->autocomplete;
    }

    /**
     * @param string $route
     * @param string $valueStorageSelector
     */
    public function setAutoComplete($route, $valueStorageSelector)
    {
        $this->autocomplete['route'] = $route;
        $this->autocomplete['value_storage_selector'] = $valueStorageSelector;
    }

    public function getJavascript()
    {
        return $this->javascript;
    }

    /**
     * @param string $javascriptTwigPath
     * @param array  $includeRoute
     */
    public function setJavascript($javascriptTwigPath, array $includeRoute = null)
    {
        $this->javascript['include_javascript'] = $javascriptTwigPath;

        if ($includeRoute) {
            $this->javascript['include_route'] = $includeRoute;
        }
    }

    /**
     * @return array
     */
    public function getFilter()
    {
        return $this->filterFields;
    }

    /**
     * @param array $fields
     */
    public function setFilter($fields)
    {
        $this->filterFields = $fields;
    }

    /**
     * @return array
     */
    protected function getEntityFields()
    {
        $fields = array();
        $reflection = new \ReflectionClass($this->entityClass);
        $reflection->getProperties();

        foreach ($reflection->getProperties() as $key => $property) {
            $fields[$key] = $property->getName();
        }

        return $fields;
    }
}
