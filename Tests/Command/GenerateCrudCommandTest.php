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
use Symfony\Component\Filesystem\Filesystem;
use Tests\Symfonian\Indonesia\AdminBundle\TestCase;
use Tests\Symfonian\Indonesia\AdminBundle\TestHelper;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class GenerateCrudCommandTest extends TestCase
{
    use TestHelper;

    private $command;

    public function setUp()
    {
        $container = $this->getContainerMock();
        $container
            ->expects($this->any())
            ->method('get')
            ->with('filesystem')
            ->willReturn(
                $this->getMockBuilder(Filesystem::class)
                ->disableOriginalConstructor()
                ->getMock()
            )
        ;
        $this->command = $this->getMockBuilder(GenerateCrudCommand::class)
            ->disableOriginalConstructor()
            ->setMethods(array(
                'setContainer',
                'getContainer',
                'getSkeletonDirs',
                'getHelperSet',
                'configure',
            ))
            ->getMock()
        ;
        $this->command->expects($this->any())->method('getContainer')->willReturn($container);
    }

    public function testProtectedMethodsMustSuccess()
    {
        $this->invokeMethod($this->command, 'getControllerGenerator');
        $this->invokeMethod($this->command, 'getSkeletonDirs');
        $this->invokeMethod($this->command, 'createGenerator');
        $this->invokeMethod($this->command, 'configure');
    }

    public function tearDown()
    {
        unset($this->command);
    }
}
