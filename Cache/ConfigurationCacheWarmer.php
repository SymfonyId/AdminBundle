<?php

namespace Symfonian\Indonesia\AdminBundle\Cache;

use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants as Constants;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmer;

class ConfigurationCacheWarmer extends CacheWarmer
{
    public function warmUp($cacheDir)
    {
        $templates = array(
            'a' => 'b',
            'c' => 'd',
        );

        $this->writeCacheFile($cacheDir.Constants::CACHE_CONTROLLER_PATH, sprintf('<?php return %s;', var_export($templates, true)));
    }

    public function isOptional()
    {
        return true;
    }
}