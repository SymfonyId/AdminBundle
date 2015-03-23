<?php
namespace Symfonian\Indonesia\AdminBundle\DependencyInjection\Compiler;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class IsDependenciesPassedPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (! $container->hasParameter('knp_paginator.template.pagination')) {
           throw new InvalidDefinitionException('KnpPaginatorBundle is not loaded.');
        }

        if (! $container->hasParameter('fos_user.registration.confirmation.from_email')) {
           throw new InvalidDefinitionException('FOSUserBundle is not loaded.');
        }

        if (! $container->hasParameter('knp_menu.default_renderer')) {
           throw new InvalidDefinitionException('KnpMenuBundle is not loaded.');
        }

        if (! interface_exists('Ihsan\Compressor\CompressorInterface')) {
           throw new InvalidDefinitionException('Ihsan\Compressor is not loaded.');
        }

        $container->setParameter('knp_paginator.template.pagination', $container->getParameter('symfonian_id.admin.themes.pagination'));
    }
}
