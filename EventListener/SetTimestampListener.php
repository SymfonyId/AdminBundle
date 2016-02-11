<?php

namespace Symfonian\Indonesia\AdminBundle\EventListener;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Event\FilterEntityEvent;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\TimestampableInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
}
