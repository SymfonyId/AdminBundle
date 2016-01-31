<?php

namespace Symfonian\Indonesia\AdminBundle\Configuration;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Annotation\Grid;
use Symfonian\Indonesia\AdminBundle\Controller\CrudController;
use Symfonian\Indonesia\AdminBundle\Grid\Column;
use Symfonian\Indonesia\AdminBundle\Grid\Filter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;
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
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
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

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('symfonian_id.admin.congiration.factory')) {
            return;
        }
        $definition = $container->findDefinition('symfonian_id.admin.congiration.factory');

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
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        if (!is_array($controller)) {
            return;
        }

        $controller = $controller[0];

        if (!$controller instanceof CrudController) {
            return;
        }

        $grid = new Grid();
        $grid->setFilters($this->filters);
        $grid->setColumns($this->columns);

        $this->addConfiguration($grid);
    }

    public function map($entity)
    {
        $reflection = new \ReflectionClass($entity);
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

        $grid = new Grid();
        $grid->setFilters($this->filters);
        $grid->setColumns($this->columns);

        $this->addConfiguration($grid);
    }
}