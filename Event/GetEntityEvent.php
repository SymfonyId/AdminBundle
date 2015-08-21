<?php

namespace Symfonian\Indonesia\AdminBundle\Event;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Doctrine\Common\Persistence\ObjectManager;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface;
use Symfony\Component\EventDispatcher\Event;

class GetEntityEvent extends Event
{
    protected $entity;

    protected $entityManager;

    /**
     * @param ObjectManager $entityManager
     */
    public function setEntityMeneger(ObjectManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return ObjectManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param EntityInterface $entity
     */
    public function setEntity(EntityInterface $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }
}