<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Doctrine\Orm\Sorter;

use Doctrine\Common\Annotations\Reader;
use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\Contract\SorterInterface;
use Symfonian\Indonesia\AdminBundle\Manager\Driver;
use Symfonian\Indonesia\AdminBundle\Manager\ManagerFactory;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants as Constants;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class FieldsSorter implements SorterInterface
{
    const DRIVER = Driver::DOCTRINE_ORM;

    /**
     * @var ManagerFactory
     */
    private $managerFactory;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var Configurator
     */
    private $configurator;

    public function __construct(ManagerFactory $managerFactory, Reader $reader, Configurator $configurator)
    {
        $this->managerFactory = $managerFactory;
        $this->reader = $reader;
        $this->configurator = $configurator;
    }

    /**
     * @param string                                                         $entityClass
     * @param \Doctrine\ORM\QueryBuilder|\Doctrine\ODM\MongoDB\Query\Builder $queryBuilder
     * @param string                                                         $sortBy
     */
    public function sort($entityClass, $queryBuilder, $sortBy)
    {
        $classMetadata = $this->getClassMetadata($entityClass);
        $metadata = $classMetadata->getFieldMapping($classMetadata->getFieldName($sortBy));
        $queryBuilder->addOrderBy(sprintf('%s.%s', Constants::ENTITY_ALIAS, $metadata['fieldName']));
    }

    /**
     * @param $entityClass
     *
     * @return \Doctrine\ODM\MongoDB\Mapping\ClassMetadata|\Doctrine\ORM\Mapping\ClassMetadata
     */
    private function getClassMetadata($entityClass)
    {
        return $this->managerFactory->getManager($this->getDriver())->getClassMetadata($entityClass);
    }

    /**
     * @return string
     */
    private function getDriver()
    {
        return $this->driver;
    }
}
