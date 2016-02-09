<?php

namespace Symfonian\Indonesia\AdminBundle\Cache;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use ReflectionClass;
use ReflectionObject;
use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\AdminBundle\Annotation\Grid;
use Symfonian\Indonesia\AdminBundle\Annotation\Page;
use Symfonian\Indonesia\AdminBundle\Annotation\Util;
use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationInterface;
use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\Controller\CrudController;
use Symfonian\Indonesia\AdminBundle\Controller\ProfileController;
use Symfonian\Indonesia\AdminBundle\Controller\UserController;
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

    private $userForm;
    private $userEntity;
    private $profileForm;
    private $configurations = array();
    private $template = array();
    private $filters = array();
    private $userShowFields = array();
    private $userGridFields = array();
    private $userGridFilters = array();
    private $profileFields = array();

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
     * @param array $filters
     */
    public function setFilter(array $filters)
    {
        $this->filters = $filters;
    }

    public function setForm($formClass, $entityClass)
    {
        $this->userForm = $formClass;
        $this->userEntity = $entityClass;
    }

    public function setView(array $showFields, array $gridFields, array $gridFilters)
    {
        $this->userShowFields = $showFields;
        $this->userGridFields = $gridFields;
        $this->userGridFilters = $gridFilters;
    }

    public function setProfileForm($formClass)
    {
        $this->profileForm = $formClass;
    }

    public function setProfileFields($profileFields)
    {
        $this->profileFields = $profileFields;
    }

    /**
     * @param string $cacheDir
     */
    public function warmUp($cacheDir)
    {
        $this->setDefaultConfig();
        $this->compileControllerConfiguration();
        $this->compileEntityConfiguration();
        $this->compileProfileController();

        $cacheDir = sprintf('%s/%s', $cacheDir, Constants::CACHE_DIR);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir);
        }

        /** @var Configurator $configurator */
        foreach ($this->configurations as $class => $configurator) {
            $this->write($configurator, $cacheDir, $class);
        }
        $this->compileUserController($cacheDir);
    }

    /**
     * @return bool
     */
    public function isOptional()
    {
        return false;
    }

    private function setDefaultConfig()
    {
        /** @var Crud $crud */
        $crud = $this->configuration->getConfiguration(Crud::class);
        $crud->setCreateTemplate($this->template['new']);
        $crud->setEditTemplate($this->template['edit']);
        $crud->setShowTemplate($this->template['show']);
        $crud->setListTemplate($this->template['list']);

        /** @var Grid $grid */
        $grid = $this->configuration->getConfiguration(Grid::class);
        $grid->setFilters($this->filters);
        $grid->setColumns(array());

        $this->configuration->addConfiguration($crud);
        $this->configuration->addConfiguration($grid);
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
                $cache = $this->configureGrid($reflectionClass, $cache);
                $this->configurations[$key] = $cache;
            }
        }
    }

    private function compileUserController($cacheDir)
    {
        $this->setDefaultConfig();
        $configuration = clone $this->configuration;
        /** @var Crud $crud */
        $crud = $this->configuration->getConfiguration(Crud::class);
        $crud->setFormClass($this->userForm);
        $crud->setEntityClass($this->userEntity);
        $crud->setShowFields($this->userShowFields);

        /** @var Grid $grid */
        $grid = $this->configuration->getConfiguration(Grid::class);
        $grid->setColumns($this->userGridFields);
        $grid->setFilters($this->userGridFilters);

        $configuration->addConfiguration($crud);
        $configuration->addConfiguration($grid);

        $this->write($configuration, $cacheDir, UserController::class);
    }

    private function compileProfileController()
    {
        $this->setDefaultConfig();
        $configuration = clone $this->configuration;
        /** @var Crud $crud */
        $crud = $this->configuration->getConfiguration(Crud::class);
        $crud->setFormClass($this->profileForm);
        $crud->setShowFields($this->profileFields);

        $configuration->addConfiguration($crud);

        $this->configurations[ProfileController::class] = $configuration;
    }

    private function configureGrid(ReflectionClass $entity, Configurator $configuration)
    {
        $config = clone $configuration;
        /** @var Crud $crud */
        $crud = $config->getConfiguration(Crud::class);
        if ($entity->getName() === $crud->getEntityClass()) {
            /** @var Grid $grid */
            $grid = $config->getConfiguration(Grid::class);
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

        /** @var Crud $configuration */
        if ($configuration instanceof Crud) {
            $output['entity_class'] = $configuration->getEntityClass();
            $output['form_class'] = $configuration->getFormClass();
            $output['show_fields'] = $configuration->getShowFields();
            $output['create_template'] = $configuration->getCreateTemplate();
            $output['edit_template'] = $configuration->getEditTemplate();
            $output['list_template'] = $configuration->getListTemplate();
            $output['show_template'] = $configuration->getShowTemplate();
            $output['allow_create'] = $configuration->isAllowCreate();
            $output['allow_edit'] = $configuration->isAllowEdit();
            $output['allow_show'] = $configuration->isAllowShow();
            $output['allow_delete'] = $configuration->isAllowDelete();
        }

        /** @var Grid $configuration */
        if ($configuration instanceof Grid) {
            $output['columns'] = $configuration->getColumns();
            $output['filters'] = $configuration->getFilters();
            $output['normalize_filter'] = $configuration->isNormalizeFilter();
            $output['format_number'] = $configuration->isFormatNumber();
        }

        /** @var Page $configuration */
        if ($configuration instanceof Page) {
            $output['title'] = $configuration->getTitle();
            $output['description'] = $configuration->getDescription();
        }

        /** @var Util $configuration */
        if ($configuration instanceof Util) {
            $output['auto_complete'] = $configuration->getAutoComplete();
            $output['include_javascript'] =  $configuration->getIncludeJavascript();
            $output['include_route'] = $configuration->getIncludeRoute();
            $output['uploadable_field'] = $configuration->getUploadableField();
            $output['use_date_picker'] = $configuration->isUseDatePicker();
            $output['use_file_chooser'] = $configuration->isUseFileChooser();
            $output['use_html_editor'] = $configuration->isUseHtmlEditor();
            $output['use_numeric'] = $configuration->isUseNumeric();
        }

        return $output;
    }

    private function write(Configurator $configurator, $cacheDir, $class)
    {
        /** @var ConfigurationInterface $configuration */
        $configs = array();
        foreach ($configurator->getAllConfigurations() as $configuration) {
            $reflection = new ReflectionObject($configuration);
            $configs[$reflection->getName()] = $this->parseConfiguration($configuration);
        }

        $cacheFile = str_replace('\\', '_', $class);
        $this->writeCacheFile(sprintf('%s/%s.php.cache', $cacheDir, $cacheFile), sprintf('<?php return %s;', var_export($configs, true)));
    }
}
