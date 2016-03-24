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

use Symfonian\Indonesia\AdminBundle\Event\FilterQueryEvent;
use Symfonian\Indonesia\AdminBundle\Filter\SortFilter;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants as Constants;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class SortQueryListener extends AbstractQueryListener
{
    /**
     * @var SortFilter
     */
    private $sortFilter;

    /**
     * @var string | null
     */
    private $sort;

    public function __construct(SortFilter $sortFilter)
    {
        $this->sortFilter = $sortFilter;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!$request->isMethod('GET')) {
            return;
        }

        $this->sort = trim($request->query->get('sort_by'));
    }

    /**
     * @param FilterQueryEvent $event
     */
    public function onFilterQuery(FilterQueryEvent $event)
    {
        if (!$this->getController()) {
            return;
        }

        $queryBuilder = $event->getQueryBuilder();
        $entityClass = $event->getEntityClass();

        $session = $this->getContainer()->get('session');
        if (!$this->sort) {
            $session->set(Constants::SESSION_SORTED_NAME, null);

            return;
        }

        $session->set(Constants::SESSION_SORTED_NAME, $this->sort);
        $this->sortFilter->createFilter($entityClass, $queryBuilder, array($this->sort));
    }
}
