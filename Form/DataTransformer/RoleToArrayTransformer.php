<?php

/**
 * This file is part of JKN.
 *
 * (c) Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 *
 * @author : Muhamad Surya Iksanudin
 **/
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
