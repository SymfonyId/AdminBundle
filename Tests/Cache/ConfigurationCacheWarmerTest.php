<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfonian\Indonesia\AdminBundle\Cache;

use Symfonian\Indonesia\AdminBundle\Cache\ConfigurationCacheWarmer;
use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\Extractor\ExtractorFactory;
use Symfony\Component\Form\FormFactory;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ConfigurationCacheWarmerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConfigurationCacheWarmer
     */
    private $cacheWarmer;

    public function setUp()
    {
        /** @var Configurator $configurator Mock */
        $configurator = $this->getMockBuilder(Configurator::class)->disableOriginalConstructor()->getMock();
        /** @var ExtractorFactory $extractor Mock */
        $extractor = $this->getMockBuilder(ExtractorFactory::class)->disableOriginalConstructor()->getMock();
        /** @var FormFactory $formFactory Mock */
        $formFactory = $this->getMockBuilder(FormFactory::class)->disableOriginalConstructor()->getMock();

        $this->cacheWarmer = new ConfigurationCacheWarmer($configurator, $extractor, $formFactory);
    }

    public function testIsOptional()
    {
        $this->assertNotTrue($this->cacheWarmer->isOptional());
    }

    public function tearDown()
    {
        unset($this->cacheWarmer);
    }
}
