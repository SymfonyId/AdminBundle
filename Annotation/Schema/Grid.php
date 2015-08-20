<?php

namespace Symfonian\Indonesia\AdminBundle\Annotation\Schema;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin.
 */

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Grid
{
    private $fields;

    private $filter;

    private $normalizeFilter = false;

    public function setValue($fields)
    {
        $this->setFields($fields);
    }

    public function setFields($fields)
    {
        if (!is_array($fields)) {
            $fields = (array) $fields;
        }

        $this->fields = $fields;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function setFilter($filter)
    {
        if (!is_array($filter)) {
            $filter = (array) $filter;
        }

        $this->filter = $filter;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function setNormalizeFilter($normalizeFilter)
    {
        $this->normalizeFilter = $normalizeFilter;
    }

    public function getNormalizeFilter()
    {
        return $this->normalizeFilter;
    }
}
