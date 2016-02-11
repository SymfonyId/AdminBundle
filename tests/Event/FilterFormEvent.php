<?php

namespace Tests\Symfonian\Indonesia\AdminBundle\Event;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Event\FilterFormEvent;
use Symfony\Component\EventDispatcher\Event;

class FilterFormEventTest extends \PHPUnit_Framework_TestCase
{
    public function testEvent()
    {
        $event = new FilterFormEvent();
        $this->assertTrue($event instanceof Event);
    }
}