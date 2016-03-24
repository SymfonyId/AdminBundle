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

use Symfonian\Indonesia\AdminBundle\Annotation\Grid;
use Symfonian\Indonesia\AdminBundle\Event\FilterQueryEvent;
use Symfonian\Indonesia\AdminBundle\Filter\FieldFilter;
use Symfonian\Indonesia\AdminBundle\Filter\GithubStyleFilter;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class FilterQueryListener extends AbstractQueryListener
{
    /**
     * @var FieldFilter
     */
    private $fieldFilter;

    /**
     * @var GithubStyleFilter
     */
    private $githubStyleFilter;

    /**
     * @var string | null
     */
    private $filter;

    public function __construct(FieldFilter $fieldFilter, GithubStyleFilter $githubStyleFilter)
    {
        $this->fieldFilter = $fieldFilter;
        $this->githubStyleFilter = $githubStyleFilter;
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

        $this->filter = trim($event->getRequest()->query->get('filter'));
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
        $configurator = $this->getConfigurator($entityClass);
        /** @var Grid $grid */
        $grid = $configurator->getConfiguration(Grid::class);

        $filters = $grid->getFilters();
        if (!$filters && !$this->filter) {
            return;
        }

        if (strpos($this->filter, ':')) {
            $splitBySpace = array_filter(explode(' ', $this->filter), function ($value) {
                return strpos($value, ':') ? true : false;
            });
            $splitBySpace = array_map(function ($value) {
                return explode(':', $value);
            }, $splitBySpace);
            $fieldFilters = array();
            $keywords = array();
            foreach ($splitBySpace as $value) {
                if (in_array($value[0], $filters)) {
                    $fieldFilters[] = $value[0];
                    $keywords[] = $grid->isNormalizeFilter() ? strtoupper($value[1]) : $value[1];
                }
            }

            $this->fieldFilter->setDateTimeFormat($this->getContainer()->getParameter('symfonian_id.admin.date_time_format'));
            $this->githubStyleFilter->createFilter($entityClass, $queryBuilder, $fieldFilters, $keywords);
        } else {
            $this->githubStyleFilter->setDateTimeFormat($this->getContainer()->getParameter('symfonian_id.admin.date_time_format'));
            $this->fieldFilter->createFilter($entityClass, $queryBuilder, $filters, array($grid->isNormalizeFilter() ? strtoupper($this->filter) : $this->filter));
        }
    }
}
