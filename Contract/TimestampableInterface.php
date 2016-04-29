<?php

namespace Symfonian\Indonesia\AdminBundle\Contract;

use DateTime;

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
