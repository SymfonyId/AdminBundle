<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Contract;

use Doctrine\Common\Annotations\Reader;
use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
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
