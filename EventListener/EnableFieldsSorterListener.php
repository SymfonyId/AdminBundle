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
use Symfonian\Indonesia\AdminBundle\Doctrine\Orm\Sorter\FieldsSorter;
use Symfonian\Indonesia\AdminBundle\Event\FilterQueryEvent;
use Symfonian\Indonesia\AdminBundle\Manager\Driver;
use Symfonian\Indonesia\AdminBundle\Manager\ManagerFactory;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants as Constants;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class EnableFieldsSorterListener extends AbstractListener implements ContainerAwareInterface
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
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $driver;

    /**
     * @var string
     */
    private $sortBy;

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
        if (!$this->sortBy = $request->query->get('sort_by')) {
            return;
        }

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
        $reflectionEntity = new \ReflectionClass($entityClass);
        $annotations = $this->reader->getClassAnnotations($reflectionEntity);
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Driver) {
                $this->driver = $annotation->getDriver();

                break;
            }
        }
    }

    public function onFilterQuery(FilterQueryEvent $event)
    {
        if (!$this->sortBy) {
            return;
        }

        $session = $this->container->get('session');
        if (!$this->sortBy) {
            $session->set(Constants::SESSION_SORTED_NAME, null);
            return;
        }
        $session->set(Constants::SESSION_SORTED_NAME, $this->sortBy);

        if (Driver::DOCTRINE_ORM === $this->driver) {
            /** @var FieldsSorter $filter */
            $filter = $this->container->get('symfonian_id.admin.filter.orm.sort');
            $filter->sort($event->getEntityClass(), $event->getQueryBuilder(), $this->sortBy);
        }

        if (Driver::DOCTRINE_ODM === $this->driver) {
            /** @var FieldsSorter $filter */
            $filter = $this->container->get('symfonian_id.admin.filter.odm.sort');
            $filter->sort($event->getEntityClass(), $event->getQueryBuilder(), $this->sortBy);
        }
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
