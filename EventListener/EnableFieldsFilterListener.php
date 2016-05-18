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
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;
use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\Contract\FieldsFilterInterface;
use Symfonian\Indonesia\AdminBundle\Manager\Driver;
use Symfonian\Indonesia\AdminBundle\Manager\ManagerFactory;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class EnableFieldsFilterListener extends AbstractListener
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
     * @var Configurator
     */
    private $configurator;

    /**
     * @var string
     */
    private $driver;

    /**
     * @var string
     */
    private $dateTimeFormat;

    /**
     * @param ManagerFactory $managerFactory
     * @param Reader         $reader
     * @param Configurator   $configurator
     * @param string         $driver
     * @param string         $dateTimeFormat
     */
    public function __construct(ManagerFactory $managerFactory, Reader $reader, Configurator $configurator, $driver, $dateTimeFormat)
    {
        $this->managerFactory = $managerFactory;
        $this->reader = $reader;
        $this->configurator = $configurator;
        $this->driver = $driver;
        $this->dateTimeFormat = $dateTimeFormat;
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
            /* @var EntityManager $manager */
            /* @var FieldsFilterInterface $filter */
            $filter = $manager->getFilters()->enable('symfonian_id.admin.filter.orm.fields');
            $this->applyFilter($filter, $request->query->get('filter'));
        }

        if (Driver::DOCTRINE_ODM === $driver) {
            /* @var DocumentManager $manager */
            /* @var FieldsFilterInterface $filter */
            $filter = $manager->getFilterCollection()->enable('symfonian_id.admin.filter.odm.fields');
            $this->applyFilter($filter, $request->query->get('filter'));
        }
    }

    /**
     * @param FieldsFilterInterface $filter
     * @param string                $keyword
     */
    private function applyFilter(FieldsFilterInterface $filter, $keyword)
    {
        $filter->setAnnotationReader($this->reader);
        $filter->setConfigurator($this->configurator);
        $filter->setDateTimeFormat($this->dateTimeFormat);
        $filter->setParameter('filter', $keyword);
    }
}
