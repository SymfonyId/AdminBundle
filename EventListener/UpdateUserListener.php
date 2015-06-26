<?php

namespace Symfonian\Indonesia\AdminBundle\EventListener;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Event\GetEntityResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfonian\Indonesia\AdminBundle\Model\UserInterface;

class UpdateUserListener
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onPreSaveUser(GetEntityResponseEvent $event)
    {
        $entity = $event->getEntity();

        if (!$entity instanceof UserInterface) {
            return;
        }

        if ($entity->getId() || $entity->isEnabled()) {
            return;
        }

        if (!$this->container->getParameter('symfonian_id.admin.security.auto_enable')) {
            return;
        }

        $entity->setEnabled(true);
    }
}
