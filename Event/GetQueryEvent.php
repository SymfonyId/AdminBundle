<?php

namespace Symfonian\Indonesia\AdminBundle\Event;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfony\Component\EventDispatcher\Event;
use Doctrine\ORM\QueryBuilder;

class GetQueryEvent extends Event
{
    protected $queryBuilder;

    protected $entityAlias;

    protected $entityClass;

    public function setQueryBuilder(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;

        return $this;
    }

    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    public function getEntityClass()
    {
        return $this->entityClass;
    }

    public function setEntityAlias($entityAlias)
    {
        $this->entityAlias = $entityAlias;

        return $this;
    }

    public function getEntityAlias()
    {
        return $this->entityAlias;
    }
}
