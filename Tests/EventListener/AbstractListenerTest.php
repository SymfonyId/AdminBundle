<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfonian\Indonesia\AdminBundle\EventListener;

use Symfonian\Indonesia\AdminBundle\EventListener\AbstractListener;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class AbstractListenerTest extends \PHPUnit_Framework_TestCase
{
    private $event;

    public function setUp()
    {
        $this->event = $this->getMockBuilder(FilterControllerEvent::class)->disableOriginalConstructor()->getMock();
        $this->event->expects($this->any())->method('getController')->willReturn(array(null, 'TestController'));
    }

    public function testIsValidCrudListener()
    {
        $listener = $this->getMockBuilder(AbstractListener::class)->disableOriginalConstructor()->getMock();
        $listener->expects($this->any())->method('isValidCrudListener')->with($this->event)->willReturn(true);
        $listener->expects($this->any())->method('getController')->willReturn('TestController');

        $this->assertTrue($listener->isValidCrudListener($this->event));
        $this->assertEquals('TestController', $listener->getController());
    }

    public function tearDown()
    {
        unset($this->event);
    }
}