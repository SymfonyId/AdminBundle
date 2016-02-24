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

use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Kernel;
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
            ->with('kernel')
            ->willReturn($this->getMockBuilder(Kernel::class)->disableOriginalConstructor()->getMock())
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
        $this->controller->expects($this->any())->method('getConfigurator')->willReturn(
            $this->getMockBuilder(Configurator::class)->disableOriginalConstructor()->getMock()
        );
        $this->setPropertyValue($this->controller, 'container', $container);
    }

    public function testGetConfigurator()
    {
        $this->assertInstanceOf(Configurator::class, $this->invokeMethod($this->controller, 'getConfigurator', array('key')));
    }

    public function testIsProduction()
    {
        $this->assertFalse($this->invokeMethod($this->controller, 'isProduction'));
    }

    public function tearDown()
    {
        unset($this->controller);
    }
}
