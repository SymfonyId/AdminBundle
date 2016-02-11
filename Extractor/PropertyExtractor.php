<?php

namespace Symfonian\Indonesia\AdminBundle\Extractor;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Doctrine\Common\Annotations\Reader;

class PropertyExtractor implements ExtractorInterface
{
    /**
     * @var Reader
     */
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function extract(\Reflector $reflectionProperty)
    {
        if (!$reflectionProperty instanceof \ReflectionProperty) {
            throw new \InvalidArgumentException(sprintf('extract() need \ReflectionProperty method as parameter, got %s', get_class($reflectionProperty)));
        }

        $metadatas = array();
        foreach ($this->reader->getPropertyAnnotations($reflectionProperty) as $propertyAnnotation) {
            $metadatas[] = $propertyAnnotation;
        }

        return $metadatas;
    }
}
