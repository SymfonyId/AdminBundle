<?php

namespace Tests\Symfonian\Indonesia\AdminBundle\Cache;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Annotation\Grid;
use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationInterface;

class GridTest extends \PHPUnit_Framework_TestCase
{
    public function testAnnotation()
    {
        $grid = new Grid();
        $this->assertTrue($grid instanceof ConfigurationInterface);
    }
}
