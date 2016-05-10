<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Annotation\Util;

use Symfonian\Indonesia\AdminBundle\Contract\ConfigurationInterface;

/**
 * @Annotation
 * @Target({"CLASS"})
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class DatePicker implements ConfigurationInterface
{
    /**
     * @link http://momentjs.com/docs/#/displaying/
     *
     * @var string
     */
    private $dateFormat = 'YYYY-MM-DD';

    /**
     * @var bool
     */
    private $flatten = false;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        if (isset($data['dateFormat'])) {
            $this->dateFormat = $data['dateFormat'];
        }

        if (isset($data['flatten'])) {
            $this->flatten = $data['flatten'];
        }

        unset($data);
    }

    /**
     * @return string
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * @return bool
     */
    public function isFlatten()
    {
        return $this->flatten;
    }

    /**
     * @param string $dateFormat
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;
    }

    /**
     * @param bool $flatten
     */
    public function setFlatten($flatten)
    {
        $this->flatten = $flatten;
    }
}
