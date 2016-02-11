<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Configuration;

use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\AdminBundle\Annotation\Grid;
use Symfonian\Indonesia\AdminBundle\EventListener\AbstractListener;
use Symfonian\Indonesia\AdminBundle\Extractor\ExtractorFactory;
use Symfonian\Indonesia\AdminBundle\Grid\Column;
use Symfonian\Indonesia\AdminBundle\Grid\Filter;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class Configurator extends AbstractListener implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var ExtractorFactory
     */
    private $extractor;

    private $configurations = array();
    private $template = array();
    protected $filters = array();
    private $freeze = false;

    public function __construct(KernelInterface $kernel, ExtractorFactory $extractor, FormFactory $formFactory)
    {
        $this->kernel = $kernel;
        $this->extractor = $extractor;
        $this->formFactory = $formFactory;
    }

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

    public function setTemplate(array $template)
    {
        $this->template = $template;
    }

    /**
     * @param ConfigurationInterface $configuration
     *
     * @throws \Exception
     */
    public function addConfiguration(ConfigurationInterface $configuration)
    {
        if ($this->freeze) {
            throw new \Exception('Can\'t change any configuration during production');
        }

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
     *
     * @return ConfigurationInterface
     */
    public function getConfiguration($name)
    {
        if (!array_key_exists($name, $this->configurations)) {
            throw new \InvalidArgumentException(sprintf('Configuration for %s not found.', $name));
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
        $crud = $this->getConfiguration(Crud::class);
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
        $grid = $this->getConfiguration(Grid::class);
        $grid->setFilters($this->filters);
        $grid->setColumns(array());

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

        $this->extractor->extract($reflectionObject);
        foreach ($this->extractor->getClassAnnotations() as $annotation) {
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
        if ('prod' === strtolower($this->kernel->getEnvironment())) {
            return;
        }

        $filters = array();
        $columns = array();

        $reflection = new \ReflectionClass($class);
        foreach ($reflection->getProperties() as $reflectionProperty) {
            $this->extractor->extract($reflectionProperty);
            foreach ($this->extractor->getPropertyAnnotations() as $annotation) {
                if ($annotation instanceof Filter) {
                    $filters[] = $reflectionProperty->getName();
                }
                if ($annotation instanceof Column) {
                    $columns[] = $reflectionProperty->getName();
                }
            }
        }

        /** @var Grid $grid */
        $grid = $this->getConfiguration(Grid::class);
        if (!empty($filters)) {
            $grid->setFilters($filters);
        }
        if (!empty($columns)) {
            $grid->setColumns($columns);
        }
        $this->addConfiguration($grid);
    }

    public function freeze()
    {
        $this->freeze = true;
    }
}
