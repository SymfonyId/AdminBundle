<?php

namespace Symfonian\Indonesia\AdminBundle\Contract;

use Doctrine\Common\Annotations\Reader;
use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;

interface FieldsFilterInterface
{
    /**
     * @param Reader $reader
     */
    public function setAnnotationReader(Reader $reader);

    /**
     * @param Configurator $configurator
     */
    public function setConfigurator(Configurator $configurator);

    /**
     * @param string $dateTimeFormat
     */
    public function setDateTimeFormat($dateTimeFormat);
}
