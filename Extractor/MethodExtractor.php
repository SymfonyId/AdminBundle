<?php

namespace Symfonian\Indonesia\AdminBundle\Extractor;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin.
 */

use Doctrine\Common\Annotations\Reader;

class MethodExtractor implements ExtractorInterface
{
    /**
     * @var Reader
     */
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function extract(\Reflector $reflectionMethod)
    {
        if (!$reflectionMethod instanceof \ReflectionMethod) {
            throw new \InvalidArgumentException(sprintf('extract() need \ReflectionMethod method as parameter, got %s', get_class($reflectionMethod)));
        }

        $metadatas = array();
        foreach ($this->reader->getMethodAnnotations($reflectionMethod) as $methodAnnotation) {
            $metadatas[] = $methodAnnotation;
        }

        return $metadatas;
    }
}
