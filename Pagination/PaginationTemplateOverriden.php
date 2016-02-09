<?php

namespace Symfonian\Indonesia\AdminBundle\Pagination;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PaginationTemplateOverriden implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasParameter('knp_paginator.template.pagination')) {
            $container->setParameter('knp_paginator.template.pagination', $container->getParameter('symfonian_id.admin.themes.pagination'));
        }
    }
}
