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
class ExtractorPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('symfonian_id.admin.extractor.extractor_factory')) {
            return;
        }

        /*
         * Add all service with tag name siab.extractor
         */
        $definition = $container->findDefinition('symfonian_id.admin.extractor.extractor_factory');
        $taggedServices = $container->findTaggedServiceIds('siab.extractor');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addExtractor', array(new Reference($id)));
        }

        $definition->addMethodCall('freeze');
    }
}
