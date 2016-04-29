<?php

namespace Symfonian\Indonesia\AdminBundle\Model;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
trait TimestampableEntity
{
    /**
     * @ORM\Column(name="created_at", type="datetime")
     *
     * @var DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="created_by", type="string", length=255)
     *
     * @var string
     */
    protected $createdBy;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     *
     * @var DateTime
     */
    protected $updatedAt;

    /**
     * @ORM\Column(name="updated_by", type="string", length=255)
     *
     * @var string
     */
    protected $updatedBy;

    /**
     * @param DateTime $date
     */
    public function setCreatedAt(DateTime $date)
    {
        $this->createdAt = $date;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $date
     */
    public function setUpdatedAt(DateTime $date)
    {
        $this->updatedAt = $date;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param string $username
     */
    public function setCreatedBy($username)
    {
        $this->createdBy = $username;
    }

    /**
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param string $username
     */
    public function setUpdatedBy($username)
    {
        $this->updatedBy = $username;
    }

    /**
     * @return string
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
}
