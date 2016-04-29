<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Manager;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Symfonian\Indonesia\AdminBundle\Exception\KeyNotMatchException;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ManagerFactory
{
    const DOCTRINE_ORM = 'orm';

    const DOCTRINE_ODM = 'odm';

    static $DRIVERS = array(
        self::DOCTRINE_ORM => 'doctrine',
        self::DOCTRINE_ODM => 'doctrine_mongodb',
    );

    /**
     * @var ObjectManager[]
     */
    private $manager = array();

    /**
     * @var string
     */
    private $default;

    /**
     * @param string $name
     * @param ManagerRegistry $objectManager
     */
    public function addManager($name, ManagerRegistry $objectManager)
    {
        if (!in_array($name, array(self::DOCTRINE_ORM, self::DOCTRINE_ODM))) {
            throw new KeyNotMatchException(sprintf('%s is not valid object manager', $name));
        }

        $this->manager[$name] = $objectManager->getManager();
    }

    /**
     * @param string $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * @param null|string $name
     *
     * @return ObjectManager
     */
    public function getManager($name = null)
    {
        if ($name) {
            return $this->manager[$name];
        }

        if ($this->default) {
            return $this->manager[$this->default];
        }

        return array_pop($this->manager);
    }
}
