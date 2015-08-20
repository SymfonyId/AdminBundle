<?php

namespace Symfonian\Indonesia\AdminBundle\Security\Model;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface;

/**
 * @ORM\MappedSuperclass
 */
abstract class User extends BaseUser implements EntityInterface
{
    /**
     * @ORM\Column(name="avatar", type="string", length=255, nullable=true)
     */
    protected $avatar;

    /**
     * @ORM\Column(name="full_name", type="string", length=77, nullable=true)
     * @Assert\NotBlank(groups={"Registration"})
     */
    protected $fullName;

    public function __construct()
    {
        parent::__construct();
    }

    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getFullName()
    {
        return $this->fullName;
    }

    public function getRole()
    {
        $roles = $this->getRoles();

        return str_replace(array('ROLE_', '_'), array('', ' '), $roles[0]);
    }
}
