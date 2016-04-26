<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\SoftDeletableEntity;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\SoftDeletableInterface;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\TimestampableEntity;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\TimestampableInterface;

/**
 * @ORM\MappedSuperclass
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
abstract class AdvanceUser extends User implements SoftDeletableInterface, TimestampableInterface
{
    use SoftDeletableEntity;

    use TimestampableEntity;

    public function __construct()
    {
        parent::__construct();
    }
}
