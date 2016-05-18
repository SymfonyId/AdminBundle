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

use Doctrine\Common\Annotations\Reader;
use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\AdminBundle\Manager\Driver;
use Symfonian\Indonesia\AdminBundle\Manager\ManagerFactory;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class EnableSoftDeletableFilterListener extends AbstractListener
{
    /**
     * @var ManagerFactory
     */
    private $managerFactory;

    /**
     * @param ManagerFactory $managerFactory
     * @param Reader         $reader
     * @param string         $driver
     */
    public function __construct(ManagerFactory $managerFactory, Reader $reader, $driver)
    {
        $this->managerFactory = $managerFactory;
        parent::__construct($reader, $driver);
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $this->isValidCrudListener($event);
        $driver = $this->getDriver();

        $manager = $this->managerFactory->getManager($driver);
        if (Driver::DOCTRINE_ORM === $driver) {
            $filter = $manager->getFilters()->enable('symfonian_id.admin.filter.orm.soft_deletable');
            $filter->setParameter('isDeleted', false);
        }

        if (Driver::DOCTRINE_ODM === $driver) {
            $filter = $manager->getFilterCollection()->enable('symfonian_id.admin.filter.odm.soft_deletable');
            $filter->setParameter('isDeleted', false);
        }
    }
}
