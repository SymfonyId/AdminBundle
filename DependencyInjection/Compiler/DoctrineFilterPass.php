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
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class DoctrineFilterPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    const DOCTRINE_CONFIGURATION = 'doctrine.orm.default_configuration';

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::DOCTRINE_CONFIGURATION)) {
            return;
        }

        /*
         * Add all service with tag name siab.filter
         */
        $definition = $container->findDefinition(self::DOCTRINE_CONFIGURATION);
        $taggedServices = $container->findTaggedServiceIds('siab.filter');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addFilter', array($id, new Reference($id)));
        }
    }
}