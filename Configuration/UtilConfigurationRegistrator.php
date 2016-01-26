<?php

namespace Symfonian\Indonesia\AdminBundle\Configuration;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class UtilConfigurationRegistrator implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->has('symfonian_id.admin.congiration.factory')) {
            $configuration = $container->getDefinition('symfonian_id.admin.congiration.factory');
            $configuration->addMethodCall('addConfiguration', array(new Reference('symfonian_id.admin.annotation.util')));//Add default value
        }
    }
}
