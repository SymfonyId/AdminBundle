<?php

namespace Symfonian\Indonesia\AdminBundle\Extractor;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin.
 */

use Doctrine\Common\Annotations\Reader;

class ClassExtractor implements ExtractorInterface
{
    /**
     * @var Reader
     */
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function extract(\Reflector $reflectionClass)
    {
        if (!$reflectionClass instanceof \ReflectionClass) {
            throw new \InvalidArgumentException(sprintf('extract() need \ReflectionClass method as parameter, got %s', get_class($reflectionClass)));
        }

        $metadatas = array();
        foreach ($this->reader->getClassAnnotations($reflectionClass) as $classAnnotation) {
            $metadatas[] = $classAnnotation;
        }

        return $metadatas;
    }
}
