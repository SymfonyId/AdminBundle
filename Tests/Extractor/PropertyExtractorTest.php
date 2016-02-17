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
use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationInterface;
use Symfonian\Indonesia\AdminBundle\Extractor\PropertyExtractor;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class PropertyExtractorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PropertyExtractor
     */
    private $extractor;

    public function setUp()
    {
        $this->extractor = new PropertyExtractor(new AnnotationReader());
    }

    public function testAnnotation()
    {
        $annotations = $this->extractor->extract(new \ReflectionProperty(Stub::class, 'stubProperty'));
        $this->assertTrue(is_array($annotations));
        $this->assertContainsOnlyInstancesOf(ConfigurationInterface::class, $annotations);
        $this->assertEquals(2, count($annotations));
    }
}
