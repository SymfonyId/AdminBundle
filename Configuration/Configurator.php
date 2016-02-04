<?php

namespace Symfonian\Indonesia\AdminBundle\Configuration;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Doctrine\Common\Annotations\Reader;
use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\AdminBundle\Annotation\Grid;
use Symfonian\Indonesia\AdminBundle\Controller\CrudController;
use Symfonian\Indonesia\AdminBundle\EventListener\AbstractListener;
use Symfonian\Indonesia\AdminBundle\Grid\Column;
use Symfonian\Indonesia\AdminBundle\Grid\Filter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelInterface;

class Configurator extends AbstractListener implements CompilerPassInterface, ContainerAwareInterface
{
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
     * @var KernelInterface
     */
    private $kernel;

    private $configurations = array();
    protected $filters = array();
    private $columns = array();

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
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
        if ($configuration instanceof Crud) {
            $configuration->setFormFactory($this->formFactory);
        }

        $this->configurations[get_class($configuration)] = $configuration;
    }

    /**
     * @param $name
     * @return ConfigurationInterface
     */
    public function getConfigForClass($name)
    {
        if (!array_key_exists($name, $this->configurations)) {
            throw new \InvalidArgumentException(sprintf('Configuration with name %s not found.', $name));
        }

        return $this->configurations[$name];
    }

    /**
     * @return array
     */
    public function getAllConfigurations()
    {
        return $this->configurations;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function configureTemplate(FilterControllerEvent $event)
    {
        if (!$this->isValidCrudListener($event)) {
            return;
        }

        /** @var Crud $crud */
        $crud = $this->getConfigForClass(Crud::class);
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
        if (!$this->isValidCrudListener($event)) {
            return;
        }

        /** @var Grid $grid */
        $grid = $this->getConfigForClass(Grid::class);
        $grid->setFilters($this->filters);
        $grid->setColumns($this->columns);

        $this->addConfiguration($grid);
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function parseAnnotation(FilterControllerEvent $event)
    {
        if (!$this->isValidCrudListener($event)) {
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

    /**
     * @param string $class
     */
    public function parseClass($class)
    {
        if ('prod' === $this->kernel->getEnvironment()) {
            return;
        }

        $filters = array();
        $columns = array();

        $reflection = new \ReflectionClass($class);
        foreach ($reflection->getProperties() as $reflectionProperty) {
            $annotations = $this->reader->getPropertyAnnotations($reflectionProperty);
            foreach ($annotations as $annotation) {
                if ($annotation instanceof Filter) {
                    $filters[] = $reflectionProperty->getName();
                }
                if ($annotation instanceof Column) {
                    $columns[] = $reflectionProperty->getName();
                }
            }
        }

        /** @var Grid $grid */
        $grid = $this->getConfigForClass(Grid::class);
        if (!empty($filters)) {
            $grid->setFilters($filters);
        }
        if (!empty($columns)) {
            $grid->setColumns($columns);
        }
        $this->addConfiguration($grid);
    }
}