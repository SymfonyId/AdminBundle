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

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ExtractorFactory
{

    /**
     * @var \Reflector
     */
    private $object;

    private $extractors = array();
    private $freeze = false;

    public function addExtractor(ExtractorInterface $extractor)
    {
        if ($this->freeze) {
            throw new \Exception('Can\'t change any extractor during runtime');
        }

        $this->extractors[get_class($extractor)] = $extractor;
    }

    public function extract(\Reflector $reflector)
    {
        $this->object = $reflector;
    }

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

    public function getPropertyAnnotations()
    {
        $annotations = array();

        /** @var PropertyExtractor $extractor */
        $extractor = $this->getExtractor(PropertyExtractor::class);
        if ($this->object instanceof \ReflectionClass) {
            foreach ($this->object->getProperties(\ReflectionProperty::IS_PRIVATE) as $reflectionProperty) {
                $annotations = array_merge($annotations, $extractor->extract($reflectionProperty));
            }
            foreach ($this->object->getProperties(\ReflectionProperty::IS_PROTECTED) as $reflectionProperty) {
                $annotations = array_merge($annotations, $extractor->extract($reflectionProperty));
            }
        }

        return $annotations;
    }

    public function freeze()
    {
        $this->freeze = true;
    }

    private function getExtractor($name)
    {
        if (!array_key_exists($name, $this->extractors)) {
            throw new \InvalidArgumentException(sprintf('Extrator for %s not found.', $name));
        }

        return $this->extractors[$name];
    }
}