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
use Symfonian\Indonesia\AdminBundle\Grid\Filter;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class FieldFilter extends AbstractFilter
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var string
     */
    private $dateTimeFormat;

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
     * @param string $format
     */
    public function setDateTimeFormat($format)
    {
        $this->dateTimeFormat = $format;
    }

    /**
     * @param string $class
     *
     * @return array
     */
    protected function readAnnotation($class)
    {
        $filters = array();
        $reflectionClass = new \ReflectionClass($class);
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            foreach ($this->reader->getPropertyAnnotations($reflectionProperty) as $annotation) {
                if ($annotation instanceof Filter) {
                    $filters[] = $reflectionProperty->getName();
                }
            }
        }

        return $filters;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $metadata
     * @param string       $alias
     * @param string       $filter
     */
    protected function doFilter(QueryBuilder $queryBuilder, array $metadata, $alias, $filter = null)
    {
        if (in_array($metadata['type'], array('date', 'datetime', 'time'))) {
            $date = \DateTime::createFromFormat($this->getDateTimeFormat(), $filter);
            if ($date) {
                $queryBuilder->andWhere(sprintf('%s.%s = :%s', $alias, $metadata['fieldName'], $metadata['fieldName']));
                $queryBuilder->setParameter($metadata['fieldName'], $date->format('Y-m-d'));
            }
        } else {
            $queryBuilder->orWhere(sprintf('%s.%s LIKE :%s', $alias, $metadata['fieldName'], $metadata['fieldName']));
            if ('array' === $metadata['type']) {
                $queryBuilder->setParameter($metadata['fieldName'], strtr('%filter%', array('filter' => serialize(array($filter)))));
            } else {
                $queryBuilder->setParameter($metadata['fieldName'], strtr('%filter%', array('filter' => $filter)));
            }
        }
    }

    /**
     * @return string
     */
    protected function getDateTimeFormat()
    {
        return $this->dateTimeFormat;
    }
}
