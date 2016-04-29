<?php

namespace Symfonian\Indonesia\AdminBundle\Model;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
trait SoftDeletableEntity
{
    /**
     * @ORM\Column(name="is_deleted", type="boolean")
     *
     * @var bool
     */
    protected $isDeleted = false;

    /**
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     *
     * @var DateTime
     */
    protected $deletedAt;

    /**
     * @ORM\Column(name="deleted_by", type="string", length=255, nullable=true)
     *
     * @var string
     */
    protected $deletedBy;

    public function isDeleted($isDeleted = null)
    {
        if (null !== $isDeleted) {
            $this->isDeleted = $isDeleted;
        }

        return $this->isDeleted;
    }

    /**
     * @return DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param DateTime $deletedAt
     */
    public function setDeletedAt(DateTime $deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @return string
     */
    public function getDeletedBy()
    {
        return $this->deletedBy;
    }

    /**
     * @param string $deletedBy
     */
    public function setDeletedBy($deletedBy)
    {
        $this->deletedBy = $deletedBy;
    }
}
