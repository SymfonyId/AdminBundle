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

use Symfonian\Indonesia\AdminBundle\Doctrine\Orm\Sorter\FieldsSorter;
use Symfonian\Indonesia\AdminBundle\Event\FilterQueryEvent;
use Symfonian\Indonesia\AdminBundle\Extractor\ExtractorFactory;
use Symfonian\Indonesia\AdminBundle\Manager\Driver;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants as Constants;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class EnableFieldsSorterListener extends AbstractListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var string
     */
    private $sortBy;

    /**
     * @param ExtractorFactory $extractor
     * @param string           $driver
     */
    public function __construct(ExtractorFactory $extractor, $driver)
    {
        parent::__construct($extractor, $driver);
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if (!$this->isValidCrudListener($event)) {
            return;
        }

        $request = $event->getRequest();
        if (!$this->sortBy = $request->query->get('sort_by')) {
            return;
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

        if (Driver::DOCTRINE_ORM === $this->getDriver()) {
            /** @var FieldsSorter $filter */
            $filter = $this->container->get('symfonian_id.admin.filter.orm.sort');
            $filter->sort($event->getEntityClass(), $event->getQueryBuilder(), $this->sortBy);
        }

        if (Driver::DOCTRINE_ODM === $this->getDriver()) {
            /** @var FieldsSorter $filter */
            $filter = $this->container->get('symfonian_id.admin.filter.odm.sort');
            $filter->sort($event->getEntityClass(), $event->getQueryBuilder(), $this->sortBy);
        }
    }
}
