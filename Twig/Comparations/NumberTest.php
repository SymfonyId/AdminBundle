<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Twig\Comparations;

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
