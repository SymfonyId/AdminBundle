<?php

namespace Tests\Symfonian\Indonesia\AdminBundle\Cache;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Annotation\Page;
use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationInterface;

class PageTest extends \PHPUnit_Framework_TestCase
{
    public function testAnnotation()
    {
        $page = new Page();
        $this->assertTrue($page instanceof ConfigurationInterface);
    }
}
