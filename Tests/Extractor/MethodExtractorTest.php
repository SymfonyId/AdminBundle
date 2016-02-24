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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfonian\Indonesia\AdminBundle\Extractor\MethodExtractor;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class MethodExtractorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MethodExtractor
     */
    private $extractor;

    public function setUp()
    {
        $this->extractor = new MethodExtractor(new AnnotationReader());
    }

    public function testAnnotation()
    {
        $annotations = $this->extractor->extract(new \ReflectionMethod(Stub::class, 'stubAction'));
        $this->assertTrue(is_array($annotations));
        $this->assertContainsOnlyInstancesOf(Route::class, $annotations);
        $this->assertEquals(1, count($annotations));

        foreach ($annotations as $annotation) {
            if ($annotation instanceof Route) {
                $this->assertEquals('/', $annotation->getPath());
                $this->assertEquals('stub_action', $annotation->getName());
            }
        }
    }

    public function tearDown()
    {
        unset($this->extractor);
    }
}
