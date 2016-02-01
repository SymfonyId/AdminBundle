<?php

namespace Symfonian\Indonesia\AdminBundle\Configuration;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmer;

class ConfigurationCacheWarmer extends CacheWarmer
{
    public function warmUp($cacheDir)
    {
        $templates = 'a';
        $this->writeCacheFile($cacheDir.'/templates.php', sprintf('<?php return %s;', var_export($templates, true)));
    }

    public function isOptional()
    {
        return true;
    }
}