<?php

namespace Symfonian\Indonesia\AdminBundle\Cache;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use ReflectionClass;
use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\AdminBundle\Annotation\Grid;
use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationInterface;
use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\Grid\Column;
use Symfonian\Indonesia\AdminBundle\Grid\Filter;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants as Constants;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmer;
use Symfony\Component\Routing\Route;
use Symfony\Component\VarDumper\VarDumper;

class ConfigurationCacheWarmer extends CacheWarmer implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Configurator
     */
    private $configuration;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var FormFactory
     */
    private $formFactory;

    private $caches = array();
    private $controllers = array();
    private $entities = array();
    private $template = array();
    private $filter = array();

    public function __construct(Configurator $configuration)
    {
        $this->configuration = $configuration;
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
     * @param array $template
     */
    public function setTemplate(array $template)
    {
        $this->template = $template;
    }

    /**
     * @param array $filter
     */
    public function setFilter(array $filter)
    {
        $this->filter = $filter;
    }

    /**
     * @param string $cacheDir
     */
    public function warmUp($cacheDir)
    {
        $this->compileControllerConfiguration();
        $this->compileEntityConfiguration();

        $this->writeCacheFile($cacheDir.Constants::CACHE_CONTROLLER_PATH, sprintf('<?php return %s;', var_export($this->caches, true)));
    }

    /**
     * @return bool
     */
    public function isOptional()
    {
        return true;
    }

    private function compileControllerConfiguration()
    {
        foreach ($this->getControllers() as $controller) {
            $reflectionClass = new ReflectionClass($controller);
            $configuration = clone $this->configuration;
            $this->parseClassAnnotation($reflectionClass, $configuration);
            $this->caches[$reflectionClass->getName()] = $configuration;
        }
    }

    private function compileEntityConfiguration()
    {
        foreach ($this->entities as $entity) {
            /** @var Configurator $cache */
            foreach ($this->caches as $key => $cache) {
                $reflectionClass = new ReflectionClass($entity);
                $cache = $this->configure($reflectionClass, $cache);
                $this->caches[$key] = $cache;
            }
        }
    }

    private function configure(ReflectionClass $entity, Configurator $configuration)
    {
        $config = clone $configuration;
        /** @var Crud $crud */
        $crud = $config->getConfigForClass(Crud::class);
        if ($entity->getName() === $crud->getEntityClass()) {
            /** @var Grid $grid */
            $grid = $config->getConfigForClass(Grid::class);
            $grid = $this->parsePropertyAnnotation($entity, $grid);
            $config->addConfiguration($grid);
        }

        return $config;
    }

    private function parseClassAnnotation(ReflectionClass $reflectionClass, Configurator $configuration)
    {
        $config = clone $configuration;
        foreach ($this->reader->getClassAnnotations($reflectionClass) as $annotation) {
            if ($annotation instanceof ConfigurationInterface) {
                if ($annotation instanceof ContainerAwareInterface) {
                    $annotation->setContainer($this->container);
                }

                if ($annotation instanceof Crud) {
                    $annotation->setFormFactory($this->formFactory);
                }

                $config->addConfiguration($annotation);
            }
        }

        return $config;
    }

    private function parsePropertyAnnotation(ReflectionClass $reflectionClass, Grid $grid)
    {
        $config = clone $grid;
        $columns = array();
        $filters = array();
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
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

        if (!empty($filters)) {
            $config->setFilters($filters);
        }
        if (!empty($columns)) {
            $config->setColumns($columns);
        }

        return $config;
    }

    private function getControllers()
    {
        $controllers = array();
        $this->container->get('http_kernel');
        $routers = $this->container->get('router')->getRouteCollection()->all();
        /** @var Route $router */
        foreach ($routers as $router) {
            $attribute = $router->getDefaults();
            if (array_key_exists('_controller', $attribute)) {
                $controllers[] = $this->parseController($attribute['_controller']);
            }
        }

        return $controllers;
    }

    private function getEntities()
    {
        $entities = array();
        /** @var EntityManager $em */
        $em = $this->container->get('doctrine')->getManager();
        $meta = $em->getMetadataFactory()->getAllMetadata();
        /** @var ClassMetadata $m */
        foreach ($meta as $m) {
            $entities[] = $m->getName();
        }
    }

    private function parseController($controller)
    {
        $temp = explode(':', $controller);
        if (3 === count($temp)) {
            $controllerClass = get_class($this->container->get($temp[0]));
        } else {
            $controllerClass = $temp[0];
        }

        return $controllerClass;
    }
}
