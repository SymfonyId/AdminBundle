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
class PaginationTemplatePass implements CompilerPassInterface
{
    const KNP_PAGINATOR_TEMPLATE = 'knp_paginator.template.pagination';

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        /*
         * Override knp paginator template
         */
        if ($container->hasParameter(self::KNP_PAGINATOR_TEMPLATE)) {
            $container->setParameter(self::KNP_PAGINATOR_TEMPLATE, $container->getParameter('symfonian_id.admin.themes.pagination'));
        }
    }
}
