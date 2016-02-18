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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfonian\Indonesia\AdminBundle\Cache\ApcCacheCleaner;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as Base;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * @Route("/apc")
 */
class ApcCacheController extends Base
{
    /**
     * @Route("/cache-clear", name="apc_cache_clear")
     * @Method({"DELETE"})
     */
    public function clearApcCache()
    {
        ApcCacheCleaner::clearAllCache();

        return new Response(true);
    }
}