<?php

namespace Symfonian\Indonesia\AdminBundle\Cache;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use ReflectionClass;
use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\AdminBundle\Annotation\Grid;
use Symfonian\Indonesia\AdminBundle\Annotation\Page;
use Symfonian\Indonesia\AdminBundle\Annotation\Util;
use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationInterface;
use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\Controller\CrudController;
use Symfonian\Indonesia\AdminBundle\Grid\Column;
use Symfonian\Indonesia\AdminBundle\Grid\Filter;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants as Constants;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmer;
use Symfony\Component\Routing\Route;

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

    private $configurations = array();
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

        $caches = array();
        /** @var Configurator $configurator */
        foreach ($this->configurations as $class => $configurator) {
            /** @var ConfigurationInterface $configuration */
            $configs = array();
            foreach ($configurator->getAllConfigurations() as $configuration) {
                $configs[] = $this->parseConfiguration($configuration);
            }

            $caches[$class] = $configs;
        }

        $this->writeCacheFile($cacheDir.Constants::CACHE_PATH, sprintf('<?php return %s;', var_export($caches, true)));
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
            if (!$reflectionClass->isSubclassOf(CrudController::class)) {
                continue;
            }

            $configuration = clone $this->configuration;
            $configuration = $this->parseClassAnnotation($reflectionClass, $configuration);
            $this->configurations[$reflectionClass->getName()] = $configuration;
        }
    }

    private function compileEntityConfiguration()
    {
        foreach ($this->getEntities() as $entity) {
            /** @var Configurator $cache */
            foreach ($this->configurations as $key => $cache) {
                $reflectionClass = new ReflectionClass($entity);
                $cache = $this->configure($reflectionClass, $cache);
                $this->configurations[$key] = $cache;
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

        return $entities;
    }

    private function parseController($controller)
    {
        $temp = explode(':', $controller);
        if (3 === count($temp)) {
            $controllerClass = $temp[0];
        } else {
            $controllerClass = get_class($this->container->get($temp[0]));
        }

        return $controllerClass;
    }

    private function parseConfiguration(ConfigurationInterface $configuration)
    {
        $output = array();
        $output['crud'] = array();
        $output['grid'] = array();
        $output['page'] = array();
        $output['util'] = array();

        /** @var Crud $configuration */
        if ($configuration instanceof Crud) {
            $output['crud']['entity_class'] = $configuration->getEntityClass();
            $output['crud']['form_class'] = $configuration->getFormClass();
            $output['crud']['show_fields'] = $configuration->getShowFields();
            $output['crud']['create_template'] = $configuration->getCreateTemplate();
            $output['crud']['edit_template'] = $configuration->getEditTemplate();
            $output['crud']['list_template'] = $configuration->getListTemplate();
            $output['crud']['show_template'] = $configuration->getShowTemplate();
            $output['crud']['allow_create'] = $configuration->isAllowCreate();
            $output['crud']['allow_edit'] = $configuration->isAllowEdit();
            $output['crud']['allow_show'] = $configuration->isAllowShow();
            $output['crud']['allow_delete'] = $configuration->isAllowDelete();
        }

        /** @var Grid $configuration */
        if ($configuration instanceof Grid) {
            $output['grid']['columns'] = $configuration->getColumns();
            $output['grid']['filters'] = $configuration->getFilters();
            $output['grid']['normalize_filter'] = $configuration->isNormalizeFilter();
            $output['grid']['format_number'] = $configuration->isFormatNumber();
        }

        /** @var Page $configuration */
        if ($configuration instanceof Page) {
            $output['page']['title'] = $configuration->getTitle();
            $output['page']['description'] = $configuration->getDescription();
        }

        /** @var Util $configuration */
        if ($configuration instanceof Util) {
            $output['util']['auto_complete'] = $configuration->getAutoComplete();
            $output['util']['include_javascript'] =  $configuration->getIncludeJavascript();
            $output['util']['include_route'] = $configuration->getIncludeRoute();
            $output['util']['uploadable_field'] = $configuration->getUploadableField();
            $output['util']['use_date_picker'] = $configuration->isUseDatePicker();
            $output['util']['use_file_chooser'] = $configuration->isUseFileChooser();
            $output['util']['use_html_editor'] = $configuration->isUseHtmlEditor();
            $output['util']['use_numeric'] = $configuration->isUseNumeric();
        }

        return $output;
    }
}
