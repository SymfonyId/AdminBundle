<?php

namespace Tests\Symfonian\Indonesia\AdminBundle\Extractor;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Doctrine\Common\Annotations\AnnotationReader;
use Symfonian\Indonesia\AdminBundle\Extractor\ClassExtractor;
use Symfonian\Indonesia\AdminBundle\Extractor\ExtractorFactory;
use Symfonian\Indonesia\AdminBundle\Extractor\MethodExtractor;
use Symfonian\Indonesia\AdminBundle\Extractor\PropertyExtractor;

class ExtractorFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExtractorFactory
     */
    private $extractor;

    public function setUp()
    {
        $this->extractor = new ExtractorFactory();
    }

    /**
     * @expectedException \Exception
     */
    public function testIsFreeze()
    {
        $this->extractor->freeze();
        $this->setDefaultExtractor();
    }

    private function setDefaultExtractor()
    {
        /** @var AnnotationReader $reader Mock */
        $reader = $this->getMockBuilder(AnnotationReader::class)->disableOriginalConstructor()->getMock();

        $this->extractor->addExtractor(new ClassExtractor($reader));
        $this->extractor->addExtractor(new MethodExtractor($reader));
        $this->extractor->addExtractor(new PropertyExtractor($reader));
    }
}