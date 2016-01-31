<?php

namespace Symfonian\Indonesia\AdminBundle\Annotation;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin.
 */
use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationInterface;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Grid implements ConfigurationInterface
{
    protected $columns = array();

    protected $filter = array();

    protected $normalizeFilter = false;

    protected $formatNumber = true;

    public function __construct(array $data = array())
    {
        if (isset($data['value'])) {
            $this->columns = $data['value'];
        }

        if (isset($data['columns'])) {
            if (!is_array($data['columns'])) {
                $data['columns'] = (array) $data['columns'];
            }

            $this->columns = $data['columns'];
        }

        if (isset($data['filter'])) {
            if (!is_array($data['filter'])) {
                $data['filter'] = (array) $data['filter'];
            }

            $this->filter = $data['filter'];
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
        return $this->filter;
    }

    public function setFilters(array $filters)
    {
        $this->filter = $filters;
    }

    public function isNormalizeFilter()
    {
        return $this->normalizeFilter;
    }

    public function isFormatNumber()
    {
        return $this->formatNumber;
    }

    public function getName()
    {
        return 'grid';
    }
}
