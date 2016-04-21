<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class RoleToArrayTransformer implements DataTransformerInterface
{
    /**
     * @param array $array
     *
     * @return string
     */
    public function transform($array)
    {
        return $array[0];
    }

    /**
     * @param string $role
     *
     * @return array
     */
    public function reverseTransform($role)
    {
        return array($role);
    }
}
