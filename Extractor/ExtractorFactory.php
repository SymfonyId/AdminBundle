<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Extractor;

use Symfonian\Indonesia\AdminBundle\Contract\ExtractorInterface;
use Symfonian\Indonesia\AdminBundle\Exception\ClassNotFoundException;
use Symfonian\Indonesia\AdminBundle\Exception\RuntimeException;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ExtractorFactory
{
    /**
     * @var \Reflector
     */
    private $object;

    /**
     * @var array
     */
    private $extractors = array();

    /**
     * @var bool
     */
    private $freeze = false;

    /**
     * @param ExtractorInterface $extractor
     *
     * @throws RuntimeException
     */
    public function addExtractor(ExtractorInterface $extractor)
    {
        if ($this->freeze) {
            throw new RuntimeException('Can\'t change any extractor during runtime');
        }

        $this->extractors[get_class($extractor)] = $extractor;
    }

    /**
     * @param \Reflector $reflector
     */
    public function extract(\Reflector $reflector)
    {
        $this->object = $reflector;
    }

    /**
     * @return array
     */
    public function getClassAnnotations()
    {
        $annotations = array();

        /** @var ClassExtractor $extractor */
        $extractor = $this->getExtractor(ClassExtractor::class);
        if ($this->object instanceof \ReflectionClass) {
            $annotations = $extractor->extract($this->object);
        }

        return $annotations;
    }

    /**
     * @return array
     */
    public function getMethodAnnotations()
    {
        $annotations = array();

        /** @var MethodExtractor $extractor */
        $extractor = $this->getExtractor(MethodExtractor::class);
        if ($this->object instanceof \ReflectionClass) {
            foreach ($this->object->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
                $annotations = array_merge($annotations, $extractor->extract($reflectionMethod));
            }
        }

        if ($this->object instanceof \ReflectionMethod) {
            $annotations = $extractor->extract($this->object);
        }

        return $annotations;
    }

    /**
     * @return array
     */
    public function getPropertyAnnotations()
    {
        $annotations = array();

        /** @var PropertyExtractor $extractor */
        $extractor = $this->getExtractor(PropertyExtractor::class);
        if ($this->object instanceof \ReflectionClass) {
            foreach ($this->object->getProperties(\ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PROTECTED) as $reflectionProperty) {
                $annotations = array_merge($annotations, $extractor->extract($reflectionProperty));
            }
        }

        if ($this->object instanceof \ReflectionProperty) {
            $annotations = $extractor->extract($this->object);
        }

        return $annotations;
    }

    public function freeze()
    {
        $this->freeze = true;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    private function getExtractor($name)
    {
        if (!array_key_exists($name, $this->extractors)) {
            throw new ClassNotFoundException(sprintf('Extrator for %s not found.', $name));
        }

        return $this->extractors[$name];
    }
}
