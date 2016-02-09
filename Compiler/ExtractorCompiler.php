<?php

namespace Symfonian\Indonesia\AdminBundle\Compiler;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ExtractorCompiler implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('symfonian_id.admin.extractor.extractor_factory')) {
            return;
        }

        $definition = $container->findDefinition('symfonian_id.admin.extractor.extractor_factory');
        $taggedServices = $container->findTaggedServiceIds('siab.extractor');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addExtractor', array(new Reference($id)));
        }

        $definition->addMethodCall('freeze');
    }
}
