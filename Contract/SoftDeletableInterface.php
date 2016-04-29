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
interface SoftDeletableInterface
{
    /**
     * @param null $isDeleted
     *
     * @return bool
     */
    public function isDeleted($isDeleted = null);

    /**
     * @param DateTime $date
     */
    public function setDeletedAt(DateTime $date);

    /**
     * @return DateTime
     */
    public function getDeletedAt();

    /**
     * @param $username
     */
    public function setDeletedBy($username);

    /**
     * @return string
     */
    public function getDeletedBy();
}
