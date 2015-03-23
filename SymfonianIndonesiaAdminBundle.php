<?php

namespace Symfonian\Indonesia\AdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfonian\Indonesia\AdminBundle\DependencyInjection\Compiler\IsDependenciesPassedPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SymfonianIndonesiaAdminBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new IsDependenciesPassedPass());
    }
}
