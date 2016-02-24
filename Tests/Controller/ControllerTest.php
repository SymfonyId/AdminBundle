<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfonian\Indonesia\AdminBundle\Controller;

use Symfonian\Indonesia\AdminBundle\Controller\Controller;
use Tests\Symfonian\Indonesia\AdminBundle\TestCase;
use Tests\Symfonian\Indonesia\AdminBundle\TestHelper;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ControllerTest extends TestCase
{
    use TestHelper;

    private $controller;

    public function setUp()
    {
        $container = $this->getContainerMock();
        $container
            ->expects($this->any())
            ->method('get')
            ->with('symfonian_id.admin.congiration.configurator')
            ->willReturn(null)
        ;
        $this->controller = $this->getMockBuilder(Controller::class)
            ->disableOriginalConstructor()
            ->setMethods(array(
                'setContainer',
                'getConfigurator',
                'getClassName',
                'isProduction',
            ))
            ->getMock()
        ;
        $this->controller->expects($this->any())->method('isProduction')->willReturn(true);
    }

    public function testGetConfigurator()
    {
        $this->invokeMethod($this->controller, 'getConfigurator', array('key'));
    }

    public function tearDown()
    {
        unset($this->controller);
    }
}
