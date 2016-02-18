<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Cache;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ApcCacheCleaner
{
    public static function clearAllCache()
    {
        self::clearUserCache();
        self::clearOpcache();
    }

    public static function clearUserCache()
    {
        if (function_exists('apc_clear_cache')) {
            apc_clear_cache();
            apc_clear_cache('user');

            return true;
        }
    }

    public static function clearOpcache()
    {
        if (function_exists('apc_clear_cache')) {
            apc_clear_cache();
            apc_clear_cache('opcode');

            return true;
        }
    }
}