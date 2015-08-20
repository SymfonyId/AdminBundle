<?php

namespace Symfonian\Indonesia\AdminBundle\Annotation\Schema;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin.
 */

use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Manager;

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
        if (!is_subclass_of($class, EntityInterface)) {
            throw new \InvalidArgumentException(sprintf('Class %s must implement Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface', $class));
        }

        $this->class = $class;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function setManager($manager)
    {
        if (!is_subclass_of($manager, Manager)) {
            throw new \InvalidArgumentException(sprintf('Class %s must extends Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Manager', $class));
        }

        $this->manager = $manager;
    }

    public function getManager()
    {
        return $this->manager;
    }
}
