<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Filter;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\QueryBuilder;
use Symfonian\Indonesia\AdminBundle\Exception\MethodNotImplementedException;
use Symfonian\Indonesia\AdminBundle\Grid\Sortable;
use Symfonian\Indonesia\AdminBundle\Manager\Driver;
use Symfonian\Indonesia\AdminBundle\Manager\ManagerFactory;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants as Constants;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class SortFilter extends AbstractFilter
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @param ManagerFactory $managerFactory
     * @param Reader        $reader
     */
    public function __construct(ManagerFactory $managerFactory, Reader $reader)
    {
        parent::__construct($managerFactory);
        $this->reader = $reader;
    }

    /**
     * @param string $class
     *
     * @return array
     */
    protected function readAnnotation($class)
    {
        $sortable = array();
        $reflectionClass = new \ReflectionClass($class);
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            foreach ($this->reader->getPropertyAnnotations($reflectionProperty) as $annotation) {
                if ($annotation instanceof Sortable) {
                    $sortable[] = $reflectionProperty->getName();
                }
                if ($annotation instanceof Driver) {
                    $this->setDriver($annotation->getDriver());
                }
            }
        }

        return $sortable;
    }

    /**
     * @param string       $entityClass
     * @param QueryBuilder $queryBuilder
     * @param array        $filterFields
     * @param array        $filters
     */
    public function createFilter($entityClass, QueryBuilder $queryBuilder, array $filterFields, array $filters = array())
    {
        foreach ($this->getMapping($this->getClassMetadata($entityClass), $filterFields) as $key => $value) {
            if (array_key_exists('join', $value)) {
                $queryBuilder->addSelect($value['join_alias']);
                $queryBuilder->leftJoin(sprintf('%s.%s', Constants::ENTITY_ALIAS, $value['join_field']), $value['join_alias'], 'WITH');
                $queryBuilder->addOrderBy(sprintf('%s.%s', $value['join_alias'], $value['fieldName']));
            } else {
                $queryBuilder->addOrderBy(sprintf('%s.%s', Constants::ENTITY_ALIAS, $value['fieldName']));
            }
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $metadata
     * @param $alias
     * @param $filter
     *
     * @throws MethodNotImplementedException
     */
    protected function doFilter(QueryBuilder $queryBuilder, array $metadata, $alias, $filter = null)
    {
        throw new MethodNotImplementedException(sprintf('%s is not implemented', __METHOD__));
    }
}
