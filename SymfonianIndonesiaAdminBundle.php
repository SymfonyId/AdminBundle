<?php

namespace Symfonian\Indonesia\AdminBundle;

use Symfonian\Indonesia\AdminBundle\Command\GenerateCrudCommand;
use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\Pagination\PaginationTemplateOverriden;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants as Constants;
use Symfonian\Indonesia\BundlePlugins\PluginBundle as Bundle;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\KernelInterface;

class SymfonianIndonesiaAdminBundle extends Bundle
{
    protected $kernel;

    public function __construct(KernelInterface $kernel, array $plugins = array())
    {
        parent::__construct($plugins);
        $this->kernel = $kernel;
    }

    public function addConfiguration(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->scalarNode('app_title')
                    ->defaultValue('Symfonian Indonesia')
                ->end()
                ->scalarNode('app_short_title')
                    ->defaultValue('SFID')
                ->end()
                ->integerNode('per_page')
                    ->defaultValue(10)
                ->end()
                ->scalarNode('identifier')
                    ->defaultValue('id')
                ->end()
                ->scalarNode('date_time_format')
                    ->defaultValue('d-m-Y')
                ->end()
                ->scalarNode('menu')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('upload_dir')
                    ->defaultValue('uploads')
                ->end()
                ->arrayNode('home')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('controller')
                            ->defaultValue('SymfonianIndonesiaAdminBundle:Home:index')
                        ->end()
                        ->scalarNode('route_path')
                            ->defaultValue('/')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('profile_fields')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('filter')
                    ->prototype('scalar')->end()
                    ->defaultValue(array('name'))
                ->end()
                ->arrayNode('number_format')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('decimal_precision')
                            ->defaultValue(0)
                        ->end()
                        ->scalarNode('decimal_separator')
                            ->defaultValue(',')
                        ->end()
                        ->scalarNode('thousand_separator')
                            ->defaultValue('.')
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('translation_domain')
                    ->defaultValue('SymfonianIndonesiaAdminBundle')
                ->end()
                ->arrayNode('user')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->children()
                        ->scalarNode('form_class')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('entity_class')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->booleanNode('auto_enable')->defaultTrue()->end()
                        ->arrayNode('show_fields')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('full_name', 'username', 'email', 'roles', 'enabled'))
                        ->end()
                        ->arrayNode('grid_fields')
                            ->defaultValue(array('full_name', 'username', 'email', 'roles', 'enabled'))
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('filter')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('full_name', 'username'))
                        ->end()
                        ->scalarNode('password_form')
                            ->defaultValue('symfonian_id.admin.change_password_form')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('themes')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('dashboard')
                            ->defaultValue(Constants::TEMPLATE_DASHBOARD)
                        ->end()
                        ->scalarNode('profile')
                            ->defaultValue(Constants::TEMPLATE_PROFILE)
                        ->end()
                        ->scalarNode('change_password')
                            ->defaultValue(Constants::TEMPLATE_CHANGE_PASSWORD)
                        ->end()
                        ->scalarNode('form_theme')
                            ->defaultValue(Constants::TEMPLATE_FORM)
                        ->end()
                        ->scalarNode('new_view')
                            ->defaultValue(Constants::TEMPLATE_CREATE)
                        ->end()
                        ->scalarNode('edit_view')
                            ->defaultValue(Constants::TEMPLATE_EDIT)
                        ->end()
                        ->scalarNode('show_view')
                            ->defaultValue(Constants::TEMPLATE_SHOW)
                        ->end()
                        ->scalarNode('list_view')
                            ->defaultValue(Constants::TEMPLATE_LIST)
                        ->end()
                        ->scalarNode('pagination')
                            ->defaultValue(Constants::TEMPLATE_PAGINATION)
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    public function load(array $config, ContainerBuilder $container)
    {
        $container->setParameter('symfonian_id.admin.app_title', $config['app_title']);
        $container->setParameter('symfonian_id.admin.app_short_title', $config['app_short_title']);
        $container->setParameter('symfonian_id.admin.per_page', $config['per_page']);
        $container->setParameter('symfonian_id.admin.menu', $config['menu']);
        $container->setParameter('symfonian_id.admin.profile_fields', $config['profile_fields']);
        $container->setParameter('symfonian_id.admin.identifier', $config['identifier']);
        $container->setParameter('symfonian_id.admin.filter', $config['filter']);
        $container->setParameter('symfonian_id.admin.date_time_format', $config['date_time_format']);
        $container->setParameter('symfonian_id.admin.translation_domain', $config['translation_domain']);
        $container->setParameter('symfonian_id.admin.themes.dashboard', $config['themes']['dashboard']);
        $container->setParameter('symfonian_id.admin.themes.profile', $config['themes']['profile']);
        $container->setParameter('symfonian_id.admin.themes.change_password', $config['themes']['change_password']);
        $container->setParameter('symfonian_id.admin.themes.form_theme', $config['themes']['form_theme']);
        $container->setParameter('symfonian_id.admin.themes.new_view', $config['themes']['new_view']);
        $container->setParameter('symfonian_id.admin.themes.edit_view', $config['themes']['edit_view']);
        $container->setParameter('symfonian_id.admin.themes.show_view', $config['themes']['show_view']);
        $container->setParameter('symfonian_id.admin.themes.list_view', $config['themes']['list_view']);
        $container->setParameter('symfonian_id.admin.themes.pagination', $config['themes']['pagination']);
        $container->setParameter('symfonian_id.admin.user.user_form', $config['user']['form_class']);
        $container->setParameter('symfonian_id.admin.user.auto_enable', $config['user']['auto_enable']);
        $container->setParameter('symfonian_id.admin.user.user_entity', $config['user']['entity_class']);
        $container->setParameter('symfonian_id.admin.user.show_fields', $config['user']['show_fields']);
        $container->setParameter('symfonian_id.admin.user.grid_fields', $config['user']['grid_fields']);
        $container->setParameter('symfonian_id.admin.user.grid_filters', $config['user']['filter']);
        $container->setParameter('symfonian_id.admin.user.password_form', $config['user']['password_form']);
        $container->setParameter('symfonian_id.admin.home.controller', $config['home']['controller']);
        $container->setParameter('symfonian_id.admin.home.route_path', $config['home']['route_path']);

        $number = array(
            'decimal_precision' => $config['number_format']['decimal_precision'],
            'decimal_separator' => $config['number_format']['decimal_separator'],
            'thousand_separator' => $config['number_format']['thousand_separator'],
        );
        $container->setParameter('symfonian_id.admin.number', $number);

        $container->setParameter('symfonian_id.admin.upload_dir', array(
            'server_path' => $container->getParameter('kernel.root_dir').'/../web/'.$config['upload_dir'],
            'web_path' => '/'.$config['upload_dir'].'/',
        ));

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/Resources/config'));
        $loader->load('configuration.yml');
        $loader->load('form.yml');
        $loader->load('listeners.yml');
        $loader->load('menu.yml');
        $loader->load('services.yml');
        $loader->load('twig.yml');
    }

    public function addCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new PaginationTemplateOverriden());
        $container->addCompilerPass(new Configurator($this->kernel));
    }

    public function addCommand(Application $application)
    {
        if ('dev' !== strtolower($this->kernel->getEnvironment())) {
            return;
        }
        parent::addCommand($application);
        $application->add(new GenerateCrudCommand());
    }

    public function getAlias()
    {
        return 'symfonyid_admin';
    }
}
