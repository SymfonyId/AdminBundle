<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfonian\Indonesia\AdminBundle\Compiler;

use Symfonian\Indonesia\AdminBundle\Compiler\ConfigurationCompiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Tests\Symfonian\Indonesia\AdminBundle\TestCase;
use Tests\Symfonian\Indonesia\AdminBundle\TestHelper;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ConfigurationCompilerTest extends TestCase
{
    use TestHelper;

    private $container;

    public function setUp()
    {
        $this->container = $this->getMockBuilder(ContainerBuilder::class)->disableOriginalConstructor()->getMock();
    }

    public function testServiceIsNotExist()
    {
        $this->container
            ->expects($this->any())
            ->method('has')
            ->with('symfonian_id.admin.congiration.configurator')
            ->willReturn(false)
        ;

        $compiler = new ConfigurationCompiler();
        $compiler->process($this->container);
    }

    public function testCompileService()
    {
        $this->container
            ->expects($this->any())
            ->method('has')
            ->with('symfonian_id.admin.congiration.configurator')
            ->willReturn(true)
        ;

        //Definition
        $definition = $this->getMockBuilder(Definition::class)->disableOriginalConstructor()->getMock();
        $this->container
            ->expects($this->any())
            ->method('findDefinition')
            ->with('symfonian_id.admin.congiration.configurator')
            ->willReturn($definition)
        ;
        $this->container
            ->expects($this->any())
            ->method('findTaggedServiceIds')
            ->with('siab.config')
            ->willReturn(array())
        ;

        $compiler = new ConfigurationCompiler();
        $compiler->process($this->container);
    }

    public function tearDown()
    {
        unset($this->container);
    }
}