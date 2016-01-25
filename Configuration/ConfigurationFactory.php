<?php

namespace Symfonian\Indonesia\AdminBundle\Configuration;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

class ConfigurationFactory
{
    private $configurations;

    public function addConfiguration(ConfigurationInterface $configuration)
    {
        $this->configurations[$configuration->getName()] = $configuration;
    }

    public function getConfiguration($name)
    {
        if (!array_key_exists($name, $this->configurations)) {
            throw new \InvalidArgumentException(sprintf('Configuration with name %s not found.', $name));
        }

        return $this->configurations[$name];
    }
}