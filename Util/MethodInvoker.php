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

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
final class MethodInvoker
{
    /**
     * @param mixed  $object   Object
     * @param string $property name of property that want to invoke
     *
     * @return mixed | void return value of method that invoked
     */
    public static function invokeGet($object, $property)
    {
        $method = CamelCaser::underScoretToCamelCase($property);
        if (method_exists($object, $method)) {
            return call_user_func_array(array($object, $method), array());
        }

        $method = CamelCaser::underScoretToCamelCase('get_'.$property);
        if (method_exists($object, $method)) {
            return call_user_func_array(array($object, $method), array());
        }

        $method = CamelCaser::underScoretToCamelCase('is_'.$property);
        if (method_exists($object, $method)) {
            return call_user_func_array(array($object, $method), array());
        }
    }

    /**
     * @param array $data   with key => value
     * @param mixed $object Object that you want to bind
     *
     * @return mixed $object Object
     */
    public static function bindSet(array $data, $object)
    {
        if (!is_object($object)) {
            return;
        }

        foreach ($data as $key => $value) {
            $method = CamelCaser::underScoretToCamelCase(sprintf('set_%s', $key));

            if (method_exists($object, $method)) {
                call_user_func_array(array($object, $method), array($value));
            } else {
                $method = CamelCaser::underScoretToCamelCase($key);

                if (!method_exists($object, $method)) {
                    $method = CamelCaser::underScoretToCamelCase(sprintf('is_%s', $key));
                }

                call_user_func_array(array($object, $method), array($value));
            }
        }

        return $object;
    }
}
