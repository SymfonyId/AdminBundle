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

use Symfonian\Indonesia\AdminBundle\Manager\ManagerFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class DoctrineFilterPass implements CompilerPassInterface
{
    const DEFAULT_CONFIGURATION = 'doctrine.orm.default_configuration';

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::DEFAULT_CONFIGURATION)) {
            return;
        }

        /*
         * Add all service with tag name siab.extractor
         */
        $definition = $container->findDefinition(self::DEFAULT_CONFIGURATION);
        $taggedServices = $container->findTaggedServiceIds('siab.filter');
        foreach ($taggedServices as $id => $tags) {
            $filter = $container->findDefinition($id);
            $definition->addMethodCall('addFilter', array($id, $filter->getClass()));
        }
    }
}
