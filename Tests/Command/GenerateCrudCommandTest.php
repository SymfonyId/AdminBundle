<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfonian\Indonesia\AdminBundle\Command;

use Symfonian\Indonesia\AdminBundle\Command\GenerateCrudCommand;
use Symfonian\Indonesia\AdminBundle\Generator\ControllerGenerator;
use Tests\Symfonian\Indonesia\AdminBundle\TestHelper;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class GenerateCrudCommandTest extends \PHPUnit_Framework_TestCase
{
    use TestHelper;

    private $command;

    public function setUp()
    {
        $this->command = $this->getMockBuilder(GenerateCrudCommand::class)->disableOriginalConstructor()->getMock();
        $this->command->expects($this->once())->method('getSkeletonDirs')->willReturn($this->returnValue(true));
    }

    public function testMustReturnGeneratorObject()
    {
        $this->assertTrue($this->invokeMethod($this->command, 'getSkeletonDirs'));
    }
}
