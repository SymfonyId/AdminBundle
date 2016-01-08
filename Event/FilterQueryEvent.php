<?php

namespace Symfonian\Indonesia\AdminBundle\Event;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\EventDispatcher\Event;

final class FilterQueryEvent extends Event
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    protected $entity;

    protected $alias;

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function setQueryBuilder(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * @param string $entity
     */
    public function setEntityClass($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entity;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }
}
