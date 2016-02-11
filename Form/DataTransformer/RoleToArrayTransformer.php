<?php

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

namespace Symfonian\Indonesia\AdminBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class RoleToArrayTransformer implements DataTransformerInterface
{
    public function transform($array)
    {
        return $array[0];
    }

    public function reverseTransform($role)
    {
        return array($role);
    }
}
