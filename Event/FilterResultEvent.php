<?php

namespace Symfonian\Indonesia\AdminBundle\Event;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Doctrine\ORM\Query;
use Symfony\Component\EventDispatcher\Event;

class FilterResultEvent extends Event
{
    /**
     * @var Query
     */
    protected $query;

    protected $result;

    protected $entity;

    public function setQuery(Query $query)
    {
        $this->query = $query;
    }

    public function setResult(array $array)
    {
        $this->result = $array;
    }

    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return Query | array
     */
    public function getResult()
    {
        if (!$this->result) {
            return $this->query;
        }

        return $this->result;
    }

    public function getEntity()
    {
        return $this->entity;
    }
}