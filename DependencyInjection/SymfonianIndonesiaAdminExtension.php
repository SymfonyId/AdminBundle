<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\DependencyInjection;

use Symfonian\Indonesia\AdminBundle\Configuration\ParameterBuilder;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants as Constants;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class SymfonianIndonesiaAdminExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array $configs An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $parameterBuilder = new ParameterBuilder($container);
        $parameterBuilder->build($configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/Resources/config'));
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
     * @param array $config
     * @param ContainerBuilder $container
     *
     * @return Configuration
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        $reflection = new \ReflectionClass(Configuration::class);
        $container->addResource(new FileResource($reflection->getFileName()));

        return new Configuration();
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return Constants::CONFIGURATION_ALIAS;
    }
}
