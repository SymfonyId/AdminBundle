<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Configuration;

use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\AdminBundle\Annotation\Grid;
use Symfonian\Indonesia\AdminBundle\Annotation\Page;
use Symfonian\Indonesia\AdminBundle\Annotation\Plugins;
use Symfonian\Indonesia\AdminBundle\Annotation\Util\AutoComplete;
use Symfonian\Indonesia\AdminBundle\Annotation\Util\DatePicker;
use Symfonian\Indonesia\AdminBundle\Annotation\Util\ExternalJavascript;
use Symfonian\Indonesia\AdminBundle\Annotation\Util\Upload;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants;
use Symfonian\Indonesia\CoreBundle\Toolkit\Util\ArrayUtil\ArrayNormalizer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

trait ConfiguratorAwareTrait
{
    /**
     * @return ContainerInterface
     */
    abstract protected function getContainer();

    /**
     * @param string $key
     *
     * @return Configurator
     */
    protected function getConfigurator($key)
    {
        /** @var KernelInterface $kernel */
        $kernel = $this->getContainer()->get('kernel');
        /** @var Configurator $configurator */
        $configurator = $this->getContainer()->get('symfonian_id.admin.congiration.configurator');
        if ('prod' !== strtolower($kernel->getEnvironment())) {
            return $configurator;
        }

        return $this->fetchFromCache($configurator, $key);
    }

    /**
     * @param Configurator $configurator
     * @param $cacheKey
     *
     * @return Configurator
     *
     * @throws \Exception
     */
    private function fetchFromCache(Configurator $configurator, $cacheKey)
    {
        $cacheDir = $this->getContainer()->getParameter('kernel.cache_dir');
        $cacheFile = str_replace('\\', '_', $cacheKey);
        $fullPath = sprintf('%s/%s/%s.php.cache', $cacheDir, SymfonianIndonesiaAdminConstants::CACHE_DIR, $cacheFile);
        if (!file_exists($fullPath)) {
            //It's impossible but we need to prevent and make sure it is not throwing an exception
            return $configurator;
        }

        $configurations = require $fullPath;
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
            if (Plugins::class === $k) {
                $config = new Plugins();
            }
            if (AutoComplete::class === $k) {
                $config = new AutoComplete();
            }
            if (DatePicker::class === $k) {
                $config = new DatePicker();
            }
            if (ExternalJavascript::class === $k) {
                $config = new ExternalJavascript();
            }
            if (Upload::class === $k) {
                $config = new Upload();
            }
            $configurator->addConfiguration(ArrayNormalizer::convertToObject($configuration, $config));
        }

        return $configurator;
    }
}
