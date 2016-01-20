<?php

namespace Symfonian\Indonesia\AdminBundle\Annotation\Schema\Util;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin.
 */

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Upload implements UtilAnnotationInterface
{
    private $fields;

    public function __construct(array $data)
    {
        if (isset($data['value'])) {
            $this->fields = (array) $data['value'];
        }

        if (isset($data['fields'])) {
            $this->fields = (array) $data['fields'];
        }
    }

    public function getFields()
    {
        return $this->fields;
    }
}
