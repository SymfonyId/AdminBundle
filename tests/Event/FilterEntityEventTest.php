<?php

namespace Symfonian\Indonesia\AdminBundle\Tests\Event;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Event\FilterEntityEvent;
use Symfony\Component\EventDispatcher\Event;

class FilterEntityEventTest extends \PHPUnit_Framework_TestCase
{
    public function testEvent()
    {
        $event = new FilterEntityEvent();
        $this->assertTrue($event instanceof Event);
    }
}