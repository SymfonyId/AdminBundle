<?php

namespace Symfonian\Indonesia\AdminBundle;

use Symfonian\Indonesia\AdminBundle\Compiler\OverridePaginationTemplate;
use Symfonian\Indonesia\AdminBundle\Handler\CrudHandler;
use Symfonian\Indonesia\BundlePlugins\PluginBundle as Bundle;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SymfonianIndonesiaAdminBundle extends Bundle
{
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
                ->arrayNode('security')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->children()
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
                                    ->defaultValue(array('username', 'email', 'roles'))
                                ->end()
                                ->arrayNode('grid_fields')
                                    ->defaultValue(array('username', 'email', 'roles'))
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('change_password')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('form_class')
                                    ->defaultValue('symfonian_id.admin.change_password_form')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('grid_action')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('show')->defaultTrue()->end()
                        ->booleanNode('edit')->defaultTrue()->end()
                        ->booleanNode('delete')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('list')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('use_ajax')->defaultTrue()->end()
                        ->scalarNode('ajax_template')
                            ->defaultValue('SymfonianIndonesiaAdminBundle:Crud:list_template.html.twig')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('themes')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('dashboard')
                            ->defaultValue('SymfonianIndonesiaAdminBundle:Index:index.html.twig')
                        ->end()
                        ->scalarNode('profile')
                            ->defaultValue('SymfonianIndonesiaAdminBundle:Index:profile.html.twig')
                        ->end()
                        ->scalarNode('change_password')
                            ->defaultValue('SymfonianIndonesiaAdminBundle:Index:change_password.html.twig')
                        ->end()
                        ->scalarNode('form_theme')
                            ->defaultValue('SymfonianIndonesiaAdminBundle:Form:fields.html.twig')
                        ->end()
                        ->scalarNode('new_view')
                            ->defaultValue('SymfonianIndonesiaAdminBundle:Crud:new.html.twig')
                        ->end()
                        ->scalarNode('edit_view')
                            ->defaultValue('SymfonianIndonesiaAdminBundle:Crud:new.html.twig')
                        ->end()
                        ->scalarNode('show_view')
                            ->defaultValue('SymfonianIndonesiaAdminBundle:Crud:show.html.twig')
                        ->end()
                        ->scalarNode('list_view')
                            ->defaultValue('SymfonianIndonesiaAdminBundle:Crud:list.html.twig')
                        ->end()
                        ->scalarNode('pagination')
                            ->defaultValue('SymfonianIndonesiaAdminBundle:Layout:pagination.html.twig')
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
        $container->setParameter('symfonian_id.admin.themes.ajax_template', $config['list']['ajax_template']);
        $container->setParameter('symfonian_id.admin.themes.pagination', $config['themes']['pagination']);
        $container->setParameter('symfonian_id.admin.security.user_form', $config['security']['user']['form_class']);
        $container->setParameter('symfonian_id.admin.security.auto_enable', $config['security']['user']['auto_enable']);
        $container->setParameter('symfonian_id.admin.security.user_entity', $config['security']['user']['entity_class']);
        $container->setParameter('symfonian_id.admin.security.show_fields', $config['security']['user']['show_fields']);
        $container->setParameter('symfonian_id.admin.security.grid_fields', $config['security']['user']['grid_fields']);
        $container->setParameter('symfonian_id.admin.security.change_password', $config['security']['change_password']['form_class']);
        $container->setParameter('symfonian_id.admin.home.controller', $config['home']['controller']);
        $container->setParameter('symfonian_id.admin.home.route_path', $config['home']['route_path']);
        $container->setParameter('symfonian_id.admin.list.use_ajax', $config['list']['use_ajax']);

        $action = array();
        if ($config['grid_action']['show']) {
            array_push($action, CrudHandler::GRID_ACTION_SHOW);
        }

        if ($config['grid_action']['edit']) {
            array_push($action, CrudHandler::GRID_ACTION_EDIT);
        }

        if ($config['grid_action']['delete']) {
            array_push($action, CrudHandler::GRID_ACTION_DELETE);
        }
        $container->setParameter('symfonian_id.admin.grid_action', $action);

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
        $loader->load('services.yml');
    }

    public function addCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new OverridePaginationTemplate());
    }

    public function getAlias()
    {
        return 'symfonyid_admin';
    }
}
