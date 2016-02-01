<?php

namespace Symfonian\Indonesia\AdminBundle\Pagination;

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
