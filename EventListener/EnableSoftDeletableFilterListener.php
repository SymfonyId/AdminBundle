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

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;
use Symfonian\Indonesia\AdminBundle\Manager\ManagerFactory;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class EnableSoftDeletableFilterListener
{
    /**
     * @var EntityManager|DocumentManager
     */
    private $manager;

    /**
     * @param ManagerFactory $managerFactory
     * @param string $driver
     */
    public function __construct(ManagerFactory $managerFactory, $driver)
    {
        $this->manager = $managerFactory->getManager($driver);
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $filter = $this->manager->getFilters()->enable('symfonian_id.admin.filter.soft_deletable');
        $filter->setParameter('isDeleted', false);
    }
}