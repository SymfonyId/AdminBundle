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

use Symfonian\Indonesia\AdminBundle\Command\GenerateCrudCommand;
use Symfonian\Indonesia\AdminBundle\Compiler\ConfigurationCompiler;
use Symfonian\Indonesia\AdminBundle\Compiler\ExtractorCompiler;
use Symfonian\Indonesia\AdminBundle\Compiler\PaginationTemplateCompiler;
use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationTreeBuilder;
use Symfonian\Indonesia\AdminBundle\Configuration\ParameterBuilder;
use Symfonian\Indonesia\BundlePlugins\PluginBundle as Bundle;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class SymfonianIndonesiaAdminBundle extends Bundle
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @param KernelInterface $kernel
     * @param array $plugins
     */
    public function __construct(KernelInterface $kernel, array $plugins = array())
    {
        parent::__construct($plugins);
        $this->kernel = $kernel;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    public function addConfiguration(ArrayNodeDefinition $rootNode)
    {
        $configurationBuilder = new ConfigurationTreeBuilder();
        $configurationBuilder->build($rootNode);
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $parameterBuilder = new ParameterBuilder($container);
        $parameterBuilder->build($config);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/Resources/config'));
        $loader->load('configurations.yml');
        $loader->load('extractors.yml');
        $loader->load('filters.yml');
        $loader->load('form.yml');
        $loader->load('listeners.yml');
        $loader->load('menu.yml');
        $loader->load('services.yml');
        $loader->load('twig.yml');
    }

    /**
     * @param ContainerBuilder $container
     */
    public function addCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new PaginationTemplateCompiler());
        $container->addCompilerPass(new ConfigurationCompiler());
        $container->addCompilerPass(new ExtractorCompiler());
    }

    /**
     * @param Application $application
     */
    public function addCommand(Application $application)
    {
        if ('dev' !== strtolower($this->kernel->getEnvironment())) {
            return;
        }
        parent::addCommand($application);
        $application->add(new GenerateCrudCommand());
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'symfonyid_admin';
    }
}
