<?php

namespace Symfonian\Indonesia\AdminBundle\Controller;

use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\AdminBundle\Annotation\Grid;
use Symfonian\Indonesia\AdminBundle\Annotation\Page;
use Symfonian\Indonesia\AdminBundle\Annotation\Util;
use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants as Constants;
use Symfonian\Indonesia\CoreBundle\Toolkit\Util\ArrayUtil\ArrayNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as Base;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class Controller extends Base
{
    abstract protected function getClassName();

    /**
     * @return Configurator
     */
    protected function getConfigurator($key)
    {
        /** @var Configurator $configurator */
        $configurator = $this->container->get('symfonian_id.admin.congiration.configurator');
        if (!$this->isProduction()) {
            return $configurator;
        }

        $cacheDir = $this->container->getParameter('kernel.cache_dir');
        $caches = require $cacheDir.Constants::CACHE_PATH;
        $configurations = $caches[$key];
        /** @var array $configuration */
        foreach ($configurations as $key => $configuration) {
            $config = null;
            if (Crud::class === $key) {
                $config = new Crud();
            }
            if (Grid::class === $key) {
                $config = new Grid();
            }
            if (Page::class === $key) {
                $config = new Page();
            }
            if (Util::class === $key) {
                $config = new Util();
            }
            $configurator->addConfiguration(ArrayNormalizer::convertToObject($configuration, $config));
        }

        return $configurator;
    }

    private function isProduction()
    {
        /** @var KernelInterface $kernel */
        $kernel = $this->container->get('kernel');
        if ('prod' === strtolower($kernel->getEnvironment())) {
            return true;
        }

        return false;
    }
}