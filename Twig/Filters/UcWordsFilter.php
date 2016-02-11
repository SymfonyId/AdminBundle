<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Twig\Filters;

use Twig_Extension;
use Twig_SimpleFilter;

class UcWordsFilter extends Twig_Extension
{
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('ucwords', array($this, 'ucWords')),
        );
    }

    public function ucWords($string)
    {
        return ucwords($string);
    }

    public function getName()
    {
        return 'ucwords';
    }
}
