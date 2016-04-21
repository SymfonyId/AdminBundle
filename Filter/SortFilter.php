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
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfonian\Indonesia\AdminBundle\Grid\Sortable;
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
     * @param EntityManager $entityManager
     * @param Reader        $reader
     */
    public function __construct(EntityManager $entityManager, Reader $reader)
    {
        parent::__construct($entityManager);
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
     * @throws \Exception
     */
    protected function doFilter(QueryBuilder $queryBuilder, array $metadata, $alias, $filter = null)
    {
        throw new \Exception(sprintf('%s is not implemented', __METHOD__));
    }
}
