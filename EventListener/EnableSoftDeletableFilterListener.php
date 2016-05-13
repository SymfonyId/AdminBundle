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
     * @var Reader
     */
    private $reader;

    /**
     * @var string
     */
    private $driver;

    /**
     * @param ManagerFactory $managerFactory
     * @param Reader         $reader
     * @param string         $driver
     */
    public function __construct(ManagerFactory $managerFactory, Reader $reader, $driver)
    {
        $this->managerFactory = $managerFactory;
        $this->reader = $reader;
        $this->driver = $driver;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $this->isValidCrudListener($event);

        $request = $event->getRequest();
        if (!$request->query->get('filter')) {
            return;
        }

        $driver = $this->driver;

        /*
         * Override default driver
         */
        $entityClass = null;
        $reflectionController = new \ReflectionObject($this->getController());
        $annotations = $this->reader->getClassAnnotations($reflectionController);
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Crud) {
                $entityClass = $annotation->getEntityClass();

                break;
            }
        }

        if ($entityClass) {
            $reflectionEntity = new \ReflectionClass($entityClass);
            $annotations = $this->reader->getClassAnnotations($reflectionEntity);
            foreach ($annotations as $annotation) {
                if ($annotation instanceof Driver) {
                    $driver = $annotation->getDriver();

                    break;
                }
            }
        }

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
