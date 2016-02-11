<?php

namespace Tests\Symfonian\Indonesia\AdminBundle\Event;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Event\FilterQueryEvent;
use Symfony\Component\EventDispatcher\Event;

class FilterQueryEventTest extends \PHPUnit_Framework_TestCase
{
    public function testEvent()
    {
        $event = new FilterQueryEvent();
        $this->assertTrue($event instanceof Event);
    }
}