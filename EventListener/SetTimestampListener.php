<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfonian\Indonesia\AdminBundle\Event\FilterEntityEvent;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\TimestampableInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class SetTimestampListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param FilterEntityEvent $event
     */
    public function onPreSaveUser(FilterEntityEvent $event)
    {
        $entity = $event->getEntity();
        if (!$entity instanceof TimestampableInterface) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return;
        }

        $now = new \DateTime();
        $username = $token->getUsername();

        if (!$entity->getId()) {
            $entity->setCreatedAt($now);
            $entity->setCreatedBy($username);
        }

        $entity->setUpdatedAt($now);
        $entity->setUpdatedBy($username);
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        $entity = $event->getObject();
        if (!$entity instanceof TimestampableInterface) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return;
        }

        $now = new \DateTime();
        $username = $token->getUsername();

        if (!$entity->getId()) {
            $entity->setCreatedAt($now);
            $entity->setCreatedBy($username);
        }

        $entity->setUpdatedAt($now);
        $entity->setUpdatedBy($username);
    }
}
