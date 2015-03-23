<?php

namespace Symfonian\Indonesia\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('symfonian_indonesia_admin');

        $rootNode
            ->children()
                ->scalarNode('app_title')
                    ->defaultValue('Symfonian Indonesia')
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
                ->arrayNode('profile_fields')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('filter')
                    ->defaultValue('name')
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

        return $treeBuilder;
    }
}