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
use Symfonian\Indonesia\AdminBundle\Annotation\Page;
use Symfonian\Indonesia\AdminBundle\Annotation\Util;
use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationInterface;
use Symfonian\Indonesia\AdminBundle\Extractor\ClassExtractor;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ClassExtractorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClassExtractor
     */
    private $extractor;

    public function setUp()
    {
        $this->extractor = new ClassExtractor(new AnnotationReader());
    }

    public function testUserControllerAnnotation()
    {
        $annotations = $this->extractor->extract(new \ReflectionClass(Stub::class));
        $this->assertTrue(is_array($annotations));
        $this->assertContainsOnlyInstancesOf(ConfigurationInterface::class, $annotations);
        $this->assertEquals(2, count($annotations));

        foreach ($annotations as $annotation) {
            if ($annotation instanceof Page) {
                $this->assertEquals('title', $annotation->getTitle());
                $this->assertEquals('description', $annotation->getDescription());
            }

            if ($annotation instanceof Util) {
                $this->assertTrue($annotation->isUseFileChooser());
                $this->assertEquals('uploadable', $annotation->getUploadableField());
            }
        }
    }
}