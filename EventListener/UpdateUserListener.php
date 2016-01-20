<?php

namespace Symfonian\Indonesia\AdminBundle\EventListener;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use FOS\UserBundle\Model\User;
use Symfonian\Indonesia\AdminBundle\Event\FilterEntityEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class UpdateUserListener
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * UpdateUserListener constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param FilterEntityEvent $event
     */
    public function onPreSaveUser(FilterEntityEvent $event)
    {
        $entity = $event->getEntity();

        if (!$entity instanceof User) {
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
