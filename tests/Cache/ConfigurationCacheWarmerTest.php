<?php

namespace Symfonian\Indonesia\AdminBundle\Tests\Cache;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Cache\ConfigurationCacheWarmer;
use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\Extractor\ExtractorFactory;
use Symfony\Component\Form\FormFactory;

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
}