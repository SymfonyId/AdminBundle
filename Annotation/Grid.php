<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Annotation;

use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationInterface;

/**
 * @Annotation
 * @Target({"CLASS"})
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class Grid implements ConfigurationInterface
{
    /**
     * @var array
     */
    private $columns = array();

    /**
     * @var array
     */
    private $filters = array();

    /**
     * @var array
     */
    private $sortable = array();

    /**
     * @var bool
     */
    private $normalizeFilter = false;

    /**
     * @var bool
     */
    private $formatNumber = true;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        if (isset($data['value'])) {
            $this->setColumns((array) $data['value']);
        }

        if (isset($data['columns'])) {
            $this->setColumns((array) $data['columns']);
        }

        if (isset($data['filters'])) {
            $this->setFilters((array) $data['filters']);
        }

        if (isset($data['sortable'])) {
            $this->setSortable((array) $data['sortable']);
        }

        if (isset($data['normalizeFilter'])) {
            $this->normalizeFilter = (bool) $data['normalizeFilter'];
        }

        if (isset($data['formatNumber'])) {
            $this->formatNumber = (bool) $data['formatNumber'];
        }

        unset($data);
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param array $columns
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param array $filters
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return array
     */
    public function getSortable()
    {
        return $this->sortable;
    }

    /**
     * @param array $sortable
     */
    public function setSortable($sortable)
    {
        $this->sortable = $sortable;
    }

    /**
     * @param bool $formatNumber
     */
    public function setFormatNumber($formatNumber)
    {
        $this->formatNumber = $formatNumber;
    }

    /**
     * @param bool $normalizeFilter
     */
    public function setNormalizeFilter($normalizeFilter)
    {
        $this->normalizeFilter = $normalizeFilter;
    }

    /**
     * @return bool
     */
    public function isNormalizeFilter()
    {
        return $this->normalizeFilter;
    }

    /**
     * @return bool
     */
    public function isFormatNumber()
    {
        return $this->formatNumber;
    }
}
