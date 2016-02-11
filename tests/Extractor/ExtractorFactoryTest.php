<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfonian\Indonesia\AdminBundle\Extractor;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfonian\Indonesia\AdminBundle\Extractor\ClassExtractor;
use Symfonian\Indonesia\AdminBundle\Extractor\ExtractorFactory;
use Symfonian\Indonesia\AdminBundle\Extractor\MethodExtractor;
use Symfonian\Indonesia\AdminBundle\Extractor\PropertyExtractor;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
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
    public function testIsFreezeExtractorFactory()
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