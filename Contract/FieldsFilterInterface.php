<?php

namespace Symfonian\Indonesia\AdminBundle\Contract;

use Doctrine\Common\Annotations\Reader;

interface FieldsFilterInterface
{
    /**
     * @param Reader $reader
     */
    public function setAnnotationReader(Reader $reader);

    /**
     * @param string $dateTimeFormat
     */
    public function setDateTimeFormat($dateTimeFormat);
}
