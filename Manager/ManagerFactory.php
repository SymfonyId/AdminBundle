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
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;
use Symfonian\Indonesia\AdminBundle\Exception\InvalidArgumentException;
use Symfonian\Indonesia\AdminBundle\Exception\KeyNotMatchException;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ManagerFactory
{
    /**
     * @var EntityManager|DocumentManager[]
     */
    private $manager = array();

    /**
     * @param string          $name
     * @param ManagerRegistry $objectManager
     */
    public function addManager($name, ManagerRegistry $objectManager)
    {
        if (!in_array($name, array(Driver::DOCTRINE_ORM, Driver::DOCTRINE_ODM))) {
            throw new KeyNotMatchException(sprintf('%s is not valid object managerFactory', $name));
        }

        $this->manager[$name] = $objectManager->getManager();
    }

    /**
     * @param null|string $driver
     *
     * @return EntityManager|DocumentManager
     *
     * @throws InvalidArgumentException
     */
    public function getManager($driver)
    {
        if ($driver) {
            return $this->manager[$driver];
        }

        throw new InvalidArgumentException(sprintf('%s driver not found'));
    }
}
