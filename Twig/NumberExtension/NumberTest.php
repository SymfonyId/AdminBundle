<?php

namespace Symfonian\Indonesia\AdminBundle\Twig\NumberExtension;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin.
 */

use Twig_Extension;
use Twig_SimpleTest;

class NumberTest extends Twig_Extension
{
    public function getTests()
    {
        return array(
            new Twig_SimpleTest('numeric', array($this, 'isNumeric')),
        );
    }

    public function isNumeric($number)
    {
        return is_numeric($number);
    }

    public function getName()
    {
        return 'is_numeric';
    }
}