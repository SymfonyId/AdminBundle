<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
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
        $cacheFile = str_replace('\\', '_', $key);
        $configurations = require sprintf('%s/%s/%s.php.cache', $cacheDir, Constants::CACHE_DIR, $cacheFile);
        /** @var array $configuration */
        foreach ($configurations as $k => $configuration) {
            $config = null;
            if (Crud::class === $k) {
                $config = new Crud();
            }
            if (Grid::class === $k) {
                $config = new Grid();
            }
            if (Page::class === $k) {
                $config = new Page();
            }
            if (Util::class === $k) {
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