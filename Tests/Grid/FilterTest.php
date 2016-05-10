<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfonian\Indonesia\AdminBundle\Cache;

use Symfonian\Indonesia\AdminBundle\Contract\ConfigurationInterface;
use Symfonian\Indonesia\AdminBundle\Grid\Filter;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class FilterTest extends \PHPUnit_Framework_TestCase
{
    public function testAnnotation()
    {
        $crud = new Filter();
        $this->assertInstanceOf(ConfigurationInterface::class, $crud);
    }
}
