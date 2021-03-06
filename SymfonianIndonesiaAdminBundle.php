<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle;

use Symfonian\Indonesia\AdminBundle\DependencyInjection\Compiler\ConfigurationPass;
use Symfonian\Indonesia\AdminBundle\DependencyInjection\Compiler\DoctrineFilterPass;
use Symfonian\Indonesia\AdminBundle\DependencyInjection\Compiler\DoctrineManagerPass;
use Symfonian\Indonesia\AdminBundle\DependencyInjection\Compiler\ExtractorPass;
use Symfonian\Indonesia\AdminBundle\DependencyInjection\Compiler\PaginationTemplatePass;
use Symfonian\Indonesia\AdminBundle\DependencyInjection\SymfonianIndonesiaAdminExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class SymfonianIndonesiaAdminBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new PaginationTemplatePass());
        $container->addCompilerPass(new ConfigurationPass());
        $container->addCompilerPass(new ExtractorPass());
        $container->addCompilerPass(new DoctrineManagerPass());
        $container->addCompilerPass(new DoctrineFilterPass());
    }

    /**
     * @return SymfonianIndonesiaAdminExtension
     */
    public function getContainerExtension()
    {
        return new SymfonianIndonesiaAdminExtension();
    }
}
