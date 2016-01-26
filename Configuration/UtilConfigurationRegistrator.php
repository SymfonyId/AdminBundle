<?php

namespace Symfonian\Indonesia\AdminBundle\Configuration;

use Symfonian\Indonesia\AdminBundle\Annotation\Util;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class UtilConfigurationRegistrator implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->has('symfonian_id.admin.congiration.factory')) {
            /** @var ConfigurationFactory $configuration */
            $configuration = $container->get('symfonian_id.admin.congiration.factory');
            $configuration->addConfiguration(new Util());//Add default value
        }
    }
}
