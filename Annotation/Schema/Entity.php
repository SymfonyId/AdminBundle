<?php

namespace Symfonian\Indonesia\AdminBundle\Annotation\Schema;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin.
 */

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Entity
{
    private $class;

    private $manager;

    public function setClass($class)
    {
        if (!is_subclass_of($class, 'Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface')) {
            throw new \InvalidArgumentException(sprintf('Class %s must implement Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface', $class));
        }

        $this->class = $class;
    }

    public function getClass()
    {
        return $this->class;
    }
}
