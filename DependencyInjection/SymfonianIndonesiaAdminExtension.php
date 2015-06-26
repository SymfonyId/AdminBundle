<?php

namespace Symfonian\Indonesia\AdminBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class SymfonianIndonesiaAdminExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

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
        $container->setParameter('symfonian_id.admin.themes.list_ajax', $config['list']['ajax_template']);
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
            array_push($action, 'show');
        }
        if ($config['grid_action']['edit']) {
            array_push($action, 'edit');
        }
        if ($config['grid_action']['delete']) {
            array_push($action, 'delete');
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

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
