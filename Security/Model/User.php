<?php

namespace Symfonian\Indonesia\AdminBundle\Security\Model;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\MappedSuperclass
 */
abstract class User extends BaseUser implements EntityInterface
{
    /**
     * @ORM\Column(name="full_name", type="string", length=77, nullable=true)
     * @Assert\NotBlank(groups={"Registration"})
     *
     * @var string
     */
    protected $fullName;

    /**
     * @ORM\Column(name="avatar", type="string", length=255, nullable=true)
     *
     * @var string
     */
    protected $avatar;

    /**
     * @var File
     */
    protected $image;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param string $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param string $avatar
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param File $image
     */
    public function setImage(File $image)
    {
        $this->image = $image;
    }

    /**
     * @return File
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        $roles = $this->getRoles();

        return str_replace(array('ROLE_', '_'), array('', ' '), $roles[0]);
    }
}
