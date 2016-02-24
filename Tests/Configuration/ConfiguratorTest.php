<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfonian\Indonesia\AdminBundle\Configuration;

use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\AdminBundle\Annotation\Grid;
use Symfonian\Indonesia\AdminBundle\Annotation\Page;
use Symfonian\Indonesia\AdminBundle\Annotation\Util;
use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\Extractor\ExtractorFactory;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ConfiguratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Configurator
     */
    private $configurator;

    public function setUp()
    {
        /** @var Kernel $kernel Mock */
        $kernel = $this->getMockBuilder(Kernel::class)->disableOriginalConstructor()->getMock();
        /** @var ExtractorFactory $extractor Mock */
        $extractor = $this->getMockBuilder(ExtractorFactory::class)->disableOriginalConstructor()->getMock();
        /** @var FormFactory $formFactory Mock */
        $formFactory = $this->getMockBuilder(FormFactory::class)->disableOriginalConstructor()->getMock();

        $this->configurator = new Configurator($kernel, $extractor, $formFactory);
    }

    /**
     * @expectedException \Exception
     */
    public function testIsFreezeConfiguration()
    {
        $this->configurator->freeze();
        $this->configurator->addConfiguration(new Crud());
    }

    public function testGetConfiguration()
    {
        $this->defaultConfiguration();

        $this->assertInstanceOf(Crud::class, $this->configurator->getConfiguration(Crud::class));
        $this->assertInstanceOf(Grid::class, $this->configurator->getConfiguration(Grid::class));
        $this->assertInstanceOf(Page::class, $this->configurator->getConfiguration(Page::class));
        $this->assertInstanceOf(Util::class, $this->configurator->getConfiguration(Util::class));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotFoundConfiguration()
    {
        $this->configurator->addConfiguration(new Crud());
        $this->configurator->getConfiguration(Grid::class);
    }

    public function testGetAllConfigurations()
    {
        $this->defaultConfiguration();

        $configurations = $this->configurator->getAllConfigurations();
        $this->assertArrayHasKey(Crud::class, $configurations);
        $this->assertArrayHasKey(Grid::class, $configurations);
        $this->assertArrayHasKey(Page::class, $configurations);
        $this->assertArrayHasKey(Util::class, $configurations);
    }

    public function tearDown()
    {
        unset($this->configurator);
    }

    private function defaultConfiguration()
    {
        $this->configurator->addConfiguration(new Crud());
        $this->configurator->addConfiguration(new Grid());
        $this->configurator->addConfiguration(new Page());
        $this->configurator->addConfiguration(new Util());
    }
}
