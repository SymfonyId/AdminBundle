<?php

namespace Symfonian\Indonesia\AdminBundle\Tests\Cache;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationInterface;

class CrudTest extends \PHPUnit_Framework_TestCase
{
    public function testAnnotation()
    {
        $crud = new Crud();
        $this->assertTrue($crud instanceof ConfigurationInterface);
    }
}
