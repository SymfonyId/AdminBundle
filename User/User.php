<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\User;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
abstract class User extends BaseUser implements EntityInterface
{
    /**
     * @Assert\NotBlank(groups={"Registration"})
     * @ORM\Column(name="full_name", type="string", length=77, nullable=true)
     *
     * @var string
     */
    protected $fullName;

    /**
     * @Assert\File(
     *     maxSize = "1024k",
     *     mimeTypes = {"image/jpeg", "image/gif", "image/png", "image/tiff"},
     * )
     *
     * @ORM\Column(name="avatar", type="string", length=255, nullable=true)
     *
     * @var string
     */
    protected $avatar;

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
     * @return string
     */
    public function getRole()
    {
        $roles = $this->getRoles();

        return str_replace(array('ROLE_', '_'), array('', ' '), $roles[0]);
    }
}
