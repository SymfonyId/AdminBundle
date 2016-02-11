<?php

namespace Symfonian\Indonesia\AdminBundle\Annotation;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationInterface;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Grid implements ConfigurationInterface
{
    protected $columns = array();

    protected $filters = array();

    protected $normalizeFilter = false;

    protected $formatNumber = true;

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

        if (isset($data['normalizeFilter'])) {
            $this->normalizeFilter = (bool) $data['normalizeFilter'];
        }

        if (isset($data['formatNumber'])) {
            $this->formatNumber = (bool) $data['formatNumber'];
        }

        unset($data);
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @param boolean $formatNumber
     */
    public function setFormatNumber($formatNumber)
    {
        $this->formatNumber = $formatNumber;
    }

    /**
     * @param boolean $normalizeFilter
     */
    public function setNormalizeFilter($normalizeFilter)
    {
        $this->normalizeFilter = $normalizeFilter;
    }

    public function isNormalizeFilter()
    {
        return $this->normalizeFilter;
    }

    public function isFormatNumber()
    {
        return $this->formatNumber;
    }
}
