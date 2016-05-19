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
use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\Contract\FieldsFilterInterface;
use Symfonian\Indonesia\AdminBundle\Extractor\ExtractorFactory;
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
     * @var Configurator
     */
    private $configurator;

    /**
     * @var string
     */
    private $dateTimeFormat;

    /**
     * @param ManagerFactory   $managerFactory
     * @param ExtractorFactory $extractor
     * @param Configurator     $configurator
     * @param string           $driver
     * @param string           $dateTimeFormat
     */
    public function __construct(ManagerFactory $managerFactory, ExtractorFactory $extractor, Configurator $configurator, $driver, $dateTimeFormat)
    {
        $this->managerFactory = $managerFactory;
        $this->configurator = $configurator;
        $this->dateTimeFormat = $dateTimeFormat;
        parent::__construct($extractor, $driver);
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $this->isValidCrudListener($event);

        $request = $event->getRequest();
        if (!$request->query->get('filter')) {
            return;
        }

        $driver = $this->getDriver();

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
        $filter->setExtractor($this->getExtractor());
        $filter->setConfigurator($this->configurator);
        $filter->setDateTimeFormat($this->dateTimeFormat);
        $filter->setParameter('filter', $keyword);
    }
}
