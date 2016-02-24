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

use Symfonian\Indonesia\AdminBundle\Compiler\PaginationTemplateCompiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tests\Symfonian\Indonesia\AdminBundle\TestCase;
use Tests\Symfonian\Indonesia\AdminBundle\TestHelper;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class PaginationTemplateCompilerTest extends TestCase
{
    use TestHelper;

    /** @var \PHPUnit_Framework_MockObject_MockObject  */
    private $container;

    public function setUp()
    {
        $this->container = $this->getMockBuilder(ContainerBuilder::class)->disableOriginalConstructor()->getMock();
    }

    public function testServiceIsNotExist()
    {
        $this->container
            ->expects($this->any())
            ->method('hasParameter')
            ->with('knp_paginator.template.pagination')
            ->willReturn(false)
        ;

        $compiler = new PaginationTemplateCompiler();
        $compiler->process($this->container);
    }

    public function testCompileService()
    {
        $this->container
            ->expects($this->any())
            ->method('hasParameter')
            ->with('knp_paginator.template.pagination')
            ->willReturn(true)
        ;
        $this->container
            ->expects($this->any())
            ->method('getParameter')
            ->with('symfonian_id.admin.themes.pagination')
            ->willReturn(true)
        ;
        $this->container
            ->expects($this->any())
            ->method('setParameter')
            ->with('knp_paginator.template.pagination', $this->container->getParameter('symfonian_id.admin.themes.pagination'))
        ;

        $compiler = new PaginationTemplateCompiler();
        $compiler->process($this->container);
    }

    public function tearDown()
    {
        unset($this->container);
    }
}
