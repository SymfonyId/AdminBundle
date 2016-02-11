<?php

namespace Tests\Symfonian\Indonesia\AdminBundle\Cache;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Annotation\Util;
use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationInterface;

class UtilTest extends \PHPUnit_Framework_TestCase
{
    public function testAnnotation()
    {
        $util = new Util();
        $this->assertTrue($util instanceof ConfigurationInterface);
    }
}
