<?php
/**
 * Created by PhpStorm.
 * User: ihsan
 * Date: 3/3/16
 * Time: 6:40 AM
 */

namespace Symfonian\Indonesia\AdminBundle\Configuration;


use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\AdminBundle\Annotation\Grid;
use Symfonian\Indonesia\AdminBundle\Annotation\Page;
use Symfonian\Indonesia\AdminBundle\Annotation\Util;
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
            if (Util::class === $k) {
                $config = new Util();
            }
            $configurator->addConfiguration(ArrayNormalizer::convertToObject($configuration, $config));
        }

        return $configurator;
    }
}