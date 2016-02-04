<?php

namespace Symfonian\Indonesia\AdminBundle\Controller;

use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants as Constants;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as Base;
use Symfony\Component\HttpKernel\KernelInterface;

class Controller extends Base
{
    private $configurator;

    /**
     * @return Configurator
     */
    protected function getConfigurator()
    {
        if ($this->configurator) {
            return $this->configurator;
        }

        $this->configurator = $this->getConfigurator();
        if (!$this->isProduction()) {
            return $this->configurator;
        }

        $cacheDir = $this->container->getParameter('kernel.cache_dir');
        $caches = require $cacheDir.Constants::CACHE_PATH;

        var_dump($caches);
        exit();
    }

    private function isProduction()
    {
        /** @var KernelInterface $kernel */
        $kernel = $this->container->get('kernel');
        if ('prod' === $kernel->getEnvironment()) {
            return true;
        }

        return false;
    }
}