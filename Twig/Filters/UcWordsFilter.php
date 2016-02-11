<?php

namespace Symfonian\Indonesia\AdminBundle\Twig\Filters;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

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
