<?php

namespace Symfonian\Indonesia\AdminBundle\EventListener;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use FOS\UserBundle\Model\User;
use Symfonian\Indonesia\AdminBundle\Event\FilterEntityEvent;

class UpdateUserListener
{
    private $autoEnable;

    /**
     * @param bool $autoEnable
     */
    public function __construct($autoEnable = false)
    {
        $this->autoEnable = $autoEnable;
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

        if (!$this->autoEnable) {
            return;
        }

        $entity->setEnabled(true);
    }
}
