<?php

namespace Symfonian\Indonesia\AdminBundle\Configuration;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin.
 */

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ConfigurationRegistrator implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('symfonian_id.admin.congiration.factory')) {
            return;
        }
        $definition = $container->findDefinition('symfonian_id.admin.congiration.factory');

        $taggedServices = $container->findTaggedServiceIds('siab.config');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addConfiguration', array(new Reference($id)));
        }
    }
}
