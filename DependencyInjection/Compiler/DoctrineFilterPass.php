<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class DoctrineFilterPass implements CompilerPassInterface
{
    const ORM_CONFIGURATION = 'doctrine.orm.default_configuration';
    const ODM_CONFIGURATION = 'doctrine_mongodb.odm.default_configuration';

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        /*
         * Add all service with tag name siab.orm_filter
         */
        if ($container->has(self::ORM_CONFIGURATION)) {
            $definition = $container->findDefinition(self::ORM_CONFIGURATION);
            $taggedServices = $container->findTaggedServiceIds('siab.orm_filter');
            foreach ($taggedServices as $id => $tags) {
                $filter = $container->findDefinition($id);
                $definition->addMethodCall('addFilter', array($id, $filter->getClass()));
            }
        }

        /*
         * Add all service with tag name siab.odm_filter
         */
        if ($container->has(self::ODM_CONFIGURATION)) {
            $definition = $container->findDefinition(self::ODM_CONFIGURATION);
            $taggedServices = $container->findTaggedServiceIds('siab.odm_filter');
            foreach ($taggedServices as $id => $tags) {
                $filter = $container->findDefinition($id);
                $definition->addMethodCall('addFilter', array($id, $filter->getClass()));
            }
        }
    }
}
