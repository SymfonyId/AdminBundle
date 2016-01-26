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
    protected $fields = array();

    protected $filter = array();

    protected $normalizeFilter = false;

    protected $formatNumber = true;

    public function __construct(array $data = array())
    {
        if (isset($data['value'])) {
            $this->fields = $data['value'];
        }

        if (isset($data['fields'])) {
            if (!is_array($data['fields'])) {
                $data['fields'] = (array) $data['fields'];
            }

            $this->fields = $data['fields'];
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

    public function getFields()
    {
        return $this->fields;
    }

    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    public function getGridFilter()
    {
        return $this->filter;
    }

    public function setGridFilters(array $filters)
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
