<?php

namespace Symfonian\Indonesia\AdminBundle\Configuration;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Doctrine\Common\Annotations\Reader;
use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\AdminBundle\Annotation\Grid;
use Symfonian\Indonesia\AdminBundle\Annotation\Page;
use Symfonian\Indonesia\AdminBundle\Annotation\Util;
use Symfonian\Indonesia\AdminBundle\Controller\CrudController;
use Symfonian\Indonesia\AdminBundle\Grid\Column;
use Symfonian\Indonesia\AdminBundle\Grid\Filter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class Configurator implements CompilerPassInterface, ContainerAwareInterface
{
    /**
     * @var array
     */
    private $configurations;

    /**
     * @var array
     */
    protected $filters = array();

    /**
     * @var array
     */
    private $columns = array();

    /**
     * @var array
     */
    private $template;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var CrudController
     */
    private $controller;

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
     * @param Reader $reader
     */
    public function setReader(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param array $filter
     */
    public function setFilter(array $filter)
    {
        $this->filters = $filter;
    }

    /**
     * @param array $columns
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }

    public function setTemplate(array $template)
    {
        $this->template = $template;
    }

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('symfonian_id.admin.congiration.configurator')) {
            return;
        }
        $definition = $container->findDefinition('symfonian_id.admin.congiration.configurator');

        $taggedServices = $container->findTaggedServiceIds('siab.config');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addConfiguration', array(new Reference($id)));
        }
    }

    /**
     * @param ConfigurationInterface $configuration
     */
    public function addConfiguration(ConfigurationInterface $configuration)
    {
        if ($configuration instanceof ContainerAwareInterface) {
            $configuration->setContainer($this->container);
        }
        $this->configurations[$configuration->getName()] = $configuration;
    }

    /**
     * @param $name
     * @return ConfigurationInterface
     */
    public function getConfiguration($name)
    {
        if (!array_key_exists($name, $this->configurations)) {
            throw new \InvalidArgumentException(sprintf('Configuration with name %s not found.', $name));
        }

        return $this->configurations[$name];
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function configureTemplate(FilterControllerEvent $event)
    {
        if (!$this->isValidEvent($event)) {
            return;
        }

        $crud = $this->getCrud();
        $crud->setCreateTemplate($this->template['new']);
        $crud->setEditTemplate($this->template['edit']);
        $crud->setShowTemplate($this->template['show']);
        $crud->setListTemplate($this->template['list']);

        $this->addConfiguration($crud);
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function configureGrid(FilterControllerEvent $event)
    {
        if (!$this->isValidEvent($event)) {
            return;
        }

        $grid = $this->getGrid();
        $grid->setFilters($this->filters);
        $grid->setColumns($this->columns);

        $this->addConfiguration($grid);
    }

    /**
     * @param string $class
     */
    public function parseClass($class)
    {
        $reflection = new \ReflectionClass($class);
        foreach ($reflection->getProperties() as $reflectionProperty) {
            $annotations = $this->reader->getPropertyAnnotations($reflectionProperty);
            foreach ($annotations as $annotation) {
                if ($annotation instanceof Filter) {
                    $this->filters[] = $reflectionProperty->getName();
                }
                if ($annotation instanceof Column) {
                    $this->columns[] = $reflectionProperty->getName();
                }
            }
        }

        $grid = $this->getGrid();
        $grid->setFilters($this->filters);
        $grid->setColumns($this->columns);

        $this->addConfiguration($grid);
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function parseAnnotation(FilterControllerEvent $event)
    {
        if (!$this->isValidEvent($event)) {
            return;
        }

        $reflectionObject = new \ReflectionObject($this->getController());
        unset($controller);
        foreach ($this->reader->getClassAnnotations($reflectionObject) as $annotation) {
            if ($annotation instanceof ConfigurationInterface) {
                if ($annotation instanceof ContainerAwareInterface) {
                    $annotation->setContainer($this->container);
                }

                if ($annotation instanceof Crud) {
                    $annotation->setFormFactory($this->formFactory);
                }

                $this->addConfiguration($annotation);
            }
        }
    }

    private function isValidEvent(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (!is_array($controller)) {
            return false;
        }

        $controller = $controller[0];
        if (!$controller instanceof CrudController) {
            return false;
        }

        $this->controller = $controller;

        return true;
    }

    private function getController()
    {
        return $this->controller;
    }

    private function getGrid()
    {
        try {
            $grid = $this->getConfiguration('grid');
        } catch (\InvalidArgumentException $e) {
            $grid = new Grid();
        }

        return clone $grid;
    }

    private function getCrud()
    {
        try {
            $crud = $this->getConfiguration('crud');
        } catch (\InvalidArgumentException $e) {
            $crud = new Crud();
        }

        return clone $crud;
    }

    private function getPage()
    {
        try {
            $page = $this->getConfiguration('page');
        } catch (\InvalidArgumentException $e) {
            $page = new Page();
        }

        return clone $page;
    }

    private function getUtil()
    {
        try {
            $util = $this->getConfiguration('util');
        } catch (\InvalidArgumentException $e) {
            $util = new Util();
        }

        return clone $util;
    }
}