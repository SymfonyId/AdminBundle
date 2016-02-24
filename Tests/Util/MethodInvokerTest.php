<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfonian\Indonesia\AdminBundle\Util;

use Symfonian\Indonesia\AdminBundle\Util\MethodInvoker;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class MethodInvokerTest extends \PHPUnit_Framework_TestCase
{
    private $stub;

    public function setUp()
    {
        $this->stub = new Stub();
    }

    public function testInvokeMethod()
    {
        $this->assertTrue(MethodInvoker::invokeGet($this->stub, 'value'));
    }

    public function testInvokeUndefinedMethod()
    {
        $this->assertNull(MethodInvoker::invokeGet($this->stub, 'undefined'));
    }

    public function tearDown()
    {
        unset($this->stub);
    }
}
