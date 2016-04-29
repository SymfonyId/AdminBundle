<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Util;

use Symfonian\Indonesia\AdminBundle\Toolkit\Util\StringUtil\CamelCasizer;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class MethodInvoker
{
    /**
     * @param mixed  $object   Object
     * @param string $property name of property that want to invoke
     *
     * @return mixed | void return value of method that invoked
     */
    public static function invokeGet($object, $property)
    {
        $method = CamelCasizer::underScoretToCamelCase($property);
        if (method_exists($object, $method)) {
            return call_user_func_array(array($object, $method), array());
        }

        $method = CamelCasizer::underScoretToCamelCase('get_'.$property);
        if (method_exists($object, $method)) {
            return call_user_func_array(array($object, $method), array());
        }

        $method = CamelCasizer::underScoretToCamelCase('is_'.$property);
        if (method_exists($object, $method)) {
            return call_user_func_array(array($object, $method), array());
        }
    }
}
