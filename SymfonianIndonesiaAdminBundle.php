<?php

namespace Symfonian\Indonesia\AdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfonian\Indonesia\AdminBundle\DependencyInjection\Compiler\DependencyCheckerCompiler;
use Symfonian\Indonesia\AdminBundle\DependencyInjection\SymfonianIndonesiaAdminExtension;

class SymfonianIndonesiaAdminBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DependencyCheckerCompiler());
    }

    public function getContainerExtension()
    {
        return new SymfonianIndonesiaAdminExtension();
    }
}
