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
class DoctrineManagerPass implements CompilerPassInterface
{
    const MANAGER_FACTORY = 'symfonian_id.admin.manager.factory';

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::MANAGER_FACTORY)) {
            return;
        }

        $definition = $container->findDefinition(self::MANAGER_FACTORY);
        $definition->addMethodCall('addManager', array(
            $container->getParameter('symfonian_id.admin.driver'),
            new Reference(ManagerFactory::$DRIVERS[$container->getParameter('symfonian_id.admin.driver')])
        ));
    }
}
