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
use Symfonian\Indonesia\AdminBundle\Contract\TimestampableInterface;

/**
 * @ORM\MappedSuperclass
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
abstract class UserTimestampable extends User implements TimestampableInterface
{
    use TimestampableEntity;

    public function __construct()
    {
        parent::__construct();
    }
}
