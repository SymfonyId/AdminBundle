<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfonian\Indonesia\AdminBundle\Event;

use Symfonian\Indonesia\AdminBundle\Event\FilterFormEvent;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class FilterFormEventTest extends \PHPUnit_Framework_TestCase
{
    public function testEvent()
    {
        $event = new FilterFormEvent();
        $this->assertInstanceOf(Event::class, $event);
    }
}