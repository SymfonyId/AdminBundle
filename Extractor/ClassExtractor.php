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

use Doctrine\Common\Annotations\Reader;
use Symfonian\Indonesia\AdminBundle\Exception\InvalidArgumentException;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ClassExtractor implements ExtractorInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param \Reflector $reflectionClass
     *
     * @throws InvalidArgumentException
     *
     * @return array
     */
    public function extract(\Reflector $reflectionClass)
    {
        if (!$reflectionClass instanceof \ReflectionClass) {
            throw new InvalidArgumentException(sprintf('extract() need \ReflectionClass method as parameter, got %s', get_class($reflectionClass)));
        }

        return $this->reader->getClassAnnotations($reflectionClass);
    }
}
