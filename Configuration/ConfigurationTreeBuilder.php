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

use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants as Constants;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ConfigurationTreeBuilder
{
    /**
     * @param ArrayNodeDefinition $rootNode
     */
    public function build(ArrayNodeDefinition $rootNode)
    {
        $this->buildRootConfiguration($rootNode);
        $this->buildNumberFormatConfiguration($rootNode);
        $this->buildUserConfiguration($rootNode);
        $this->buildThemeConfiguration($rootNode);
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function buildRootConfiguration(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->scalarNode('app_title')->defaultValue('Symfonian Indonesia')->end()
                ->scalarNode('app_short_title')->defaultValue('SFID')->end()
                ->integerNode('per_page')->defaultValue(10)->end()
                ->scalarNode('identifier')->defaultValue('id')->end()
                ->integerNode('max_records')->defaultValue(1000)->end()
                ->scalarNode('date_time_format')->defaultValue('d-m-Y')->end()
                ->scalarNode('menu')->defaultValue('symfonian_indonesia_admin_main_menu')->end()
                ->scalarNode('upload_dir')->defaultValue('uploads')->end()
                ->scalarNode('driver')
                    ->isRequired()
                ->end()
                ->scalarNode('translation_domain')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('profile_fields')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('filter')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function buildNumberFormatConfiguration(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('number_format')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('decimal_precision')->defaultValue(0)->end()
                        ->scalarNode('decimal_separator')->defaultValue(',')->end()
                        ->scalarNode('thousand_separator')->defaultValue('.')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function buildUserConfiguration(ArrayNodeDefinition $rootNode)
    {
        $rootNode
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
                        ->scalarNode('password_form')->defaultValue('symfonian_id.admin.change_password_form')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function buildThemeConfiguration(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('themes')
                ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('dashboard')->defaultValue(Constants::TEMPLATE_DASHBOARD)->end()
                        ->scalarNode('profile')->defaultValue(Constants::TEMPLATE_PROFILE)->end()
                        ->scalarNode('change_password')->defaultValue(Constants::TEMPLATE_CHANGE_PASSWORD)->end()
                        ->scalarNode('form_theme')->defaultValue(Constants::TEMPLATE_FORM)->end()
                        ->scalarNode('new_view')->defaultValue(Constants::TEMPLATE_CREATE)->end()
                        ->scalarNode('bulk_new_view')->defaultValue(Constants::TEMPLATE_BULK_CREATE)->end()
                        ->scalarNode('edit_view')->defaultValue(Constants::TEMPLATE_EDIT)->end()
                        ->scalarNode('show_view')->defaultValue(Constants::TEMPLATE_SHOW)->end()
                        ->scalarNode('list_view')->defaultValue(Constants::TEMPLATE_LIST)->end()
                        ->scalarNode('pagination')->defaultValue(Constants::TEMPLATE_PAGINATION)->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
