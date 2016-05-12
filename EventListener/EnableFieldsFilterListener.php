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
use Doctrine\ORM\Query\Filter\SQLFilter;
use Symfonian\Indonesia\AdminBundle\Contract\FieldsFilterInterface;
use Symfonian\Indonesia\AdminBundle\Manager\ManagerFactory;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class EnableFieldsFilterListener
{
    /**
     * @var EntityManager|DocumentManager
     */
    private $manager;

    /**
     * @var Reader
     */
    private $reader;

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
     * @param string         $driver
     */
    public function __construct(ManagerFactory $managerFactory, Reader $reader, $driver, $dateTimeFormat)
    {
        $this->manager = $managerFactory->getManager($driver);
        $this->reader = $reader;
        $this->driver = $driver;
        $this->dateTimeFormat = $dateTimeFormat;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->query->get('filter')) {
            return;
        }

        if (ManagerFactory::DOCTRINE_ORM === $this->driver) {
            $filter = $this->manager->getFilters()->enable('symfonian_id.admin.filter.orm.fields');
            $this->applyFilter($filter, $request->query->get('filter'));
        }

        if (ManagerFactory::DOCTRINE_ODM === $this->driver) {
            $filter = $this->manager->getFilters()->enable('symfonian_id.admin.filter.odm.fields');
            $this->applyFilter($filter, $request->query->get('filter'));
        }
    }

    /**
     * @param FieldsFilterInterface|SQLFilter $filter
     * @param string                          $keyword
     */
    private function applyFilter(FieldsFilterInterface $filter, $keyword)
    {
        $filter->setAnnotationReader($this->reader);
        $filter->setDateTimeFormat($this->dateTimeFormat);
        $filter->setParameter('keyword', $keyword);
    }
}
