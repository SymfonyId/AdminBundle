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

    public function __construct(array $data)
    {
        if (isset($data['value'])) {
            $this->fields = $data['value'];
        }

        if (isset($data['fields'])) {
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

        unset($data);
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function getNormalizeFilter()
    {
        return $this->normalizeFilter;
    }
}
