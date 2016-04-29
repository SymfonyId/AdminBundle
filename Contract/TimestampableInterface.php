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

use DateTime;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
interface TimestampableInterface
{
    /**
     * @param DateTime $date
     */
    public function setCreatedAt(DateTime $date);

    /**
     * @return DateTime
     */
    public function getCreatedAt();

    /**
     * @param DateTime $date
     */
    public function setUpdatedAt(DateTime $date);

    /**
     * @return DateTime
     */
    public function getUpdatedAt();

    /**
     * @param string $username
     */
    public function setCreatedBy($username);

    /**
     * @return string
     */
    public function getCreatedBy();

    /**
     * @param string $username
     */
    public function setUpdatedBy($username);

    /**
     * @return string
     */
    public function getUpdatedBy();
}
