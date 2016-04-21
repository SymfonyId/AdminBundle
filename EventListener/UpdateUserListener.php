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

use FOS\UserBundle\Model\User;
use Symfonian\Indonesia\AdminBundle\Event\FilterEntityEvent;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
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
        if (!$this->autoEnable) {
            return;
        }

        $entity = $event->getEntity();

        if (!$entity instanceof User) {
            return;
        }

        if ($entity->getId() || $entity->isEnabled()) {
            return;
        }

        $entity->setEnabled(true);
    }
}
