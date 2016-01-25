<?php

namespace Symfonian\Indonesia\AdminBundle\Configuration;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Crud;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Grid;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Page;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;

class Configurator implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ConfigurationFactory
     */
    protected $configurationFactory;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    protected $translationDomain;

    protected $useDatePicker = false;

    protected $useFileStyle = false;

    protected $useEditor = false;

    protected $autocomplete = array();

    protected $javascript = array();

    /**
     * @var Page
     */
    protected $page;

    /**
     * @var Crud
     */
    protected $crud;

    /**
     * @var Grid
     */
    protected $grid;

    public function __construct(ConfigurationFactory $configurationFactory, FormFactory $formFactory, $translationDomain)
    {
        $this->configurationFactory = $configurationFactory;
        $this->formFactory = $formFactory;
        $this->translationDomain = $translationDomain;

        $this->autocomplete['route'] = 'home';
        $this->autocomplete['value_storage_selector'] = '.selector';
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        /** @var Page $page */
        $page = $this->page?:$this->configurationFactory->getConfiguration('page');

        return $page->getTitle();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        /** @var Page $page */
        $page = $this->page?:$this->configurationFactory->getConfiguration('page');

        return $page->getDescription();
    }

    /**
     * @return array
     */
    public function getShowFields()
    {
        /** @var Crud $crud */
        $crud = $this->crud?: $this->configurationFactory->getConfiguration('crud');

        if (!empty($crud->getShowFields())) {
            return $this->getShowFields();
        }

        return $this->getEntityFields();
    }

    /**
     * @return mixed
     */
    public function getEntityClass()
    {
        /** @var Crud $crud */
        $crud = $this->crud?: $this->configurationFactory->getConfiguration('crud');

        return $crud->getEntityClass();
    }

    /**
     * @param EntityInterface | null $formData
     *
     * @return FormInterface
     */
    public function getForm($formData = null)
    {
        /** @var Crud $crud */
        $crud = $this->crud?: $this->configurationFactory->getConfiguration('crud');
        $formClass = $crud->getFormClass();

        $options = array();
        try {
            $formObject = new $formClass();
        } catch (\Exception $ex) {
            $formObject = $this->container->get($formClass);
        }

        $form = $this->formFactory->create(get_class($formObject), null, $options);
        $form->setData($formData);

        return $form;
    }

    /**
     * @return string
     */
    public function getAddTemplate()
    {
        /** @var Crud $crud */
        $crud = $this->crud?: $this->configurationFactory->getConfiguration('crud');

        return $crud->getAddTemplate();
    }

    /**
     * @return string
     */
    public function getEditTemplate()
    {
        /** @var Crud $crud */
        $crud = $this->crud?: $this->configurationFactory->getConfiguration('crud');

        return $crud->getEditTemplate();
    }

    /**
     * @return string
     */
    public function getShowTemplate()
    {
        /** @var Crud $crud */
        $crud = $this->crud?: $this->configurationFactory->getConfiguration('crud');

        return $crud->getShowTemplate();
    }

    /**
     * @return string
     */
    public function getListTemplate()
    {
        /** @var Crud $crud */
        $crud = $this->crud?: $this->configurationFactory->getConfiguration('crud');

        return $crud->getListTemplate();
    }

    /**
     * @return string
     */
    public function getAjaxTemplate()
    {
        /** @var Crud $crud */
        $crud = $this->crud?: $this->configurationFactory->getConfiguration('crud');

        return $crud->getAjaxTemplate();
    }

    /**
     * @return bool
     */
    public function isUseAjax()
    {
        /** @var Crud $crud */
        $crud = $this->crud?: $this->configurationFactory->getConfiguration('crud');

        return $crud->isUseAjax();
    }

    /**
     * @return bool
     */
    public function isNormalizeFilter()
    {
        /** @var Crud $crud */
        $this->grid?: $this->configurationFactory->getConfiguration('grid');

        return $this->grid->isNormalizeFilter();
    }

    /**
     * @return bool
     */
    public function isFormatNumber()
    {
        /** @var Crud $crud */
        $this->grid?: $this->configurationFactory->getConfiguration('grid');

        return $this->grid->isFormatNumber();
    }

    /**
     * @return array
     */
    public function getGridFields()
    {
        /** @var Crud $crud */
        $this->grid?: $this->configurationFactory->getConfiguration('grid');

        if (!empty($this->grid->getGridFields())) {
            return $this->grid->getGridFields();
        }

        return $this->getEntityFields();
    }

    /**
     * @return array
     */
    public function getGridFilter()
    {
        /** @var Crud $crud */
        $this->grid?: $this->configurationFactory->getConfiguration('grid');

        return $this->grid->getGridFilter();
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
