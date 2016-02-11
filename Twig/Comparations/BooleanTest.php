<?php

namespace Symfonian\Indonesia\AdminBundle\Twig\Comparations;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Twig_Extension;
use Twig_SimpleTest;

class BooleanTest extends Twig_Extension
{
    public function getTests()
    {
        return array(
            new Twig_SimpleTest('boolean', array($this, 'isBoolean')),
        );
    }

    public function isBoolean($boolean)
    {
        return is_bool($boolean);
    }

    public function getName()
    {
        return 'is_boolean';
    }
}
