<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Symfonian\Indonesia\AdminBundle\Annotation\Grid;
use Symfonian\Indonesia\AdminBundle\Event\FilterQueryEvent;
use Symfonian\Indonesia\AdminBundle\Grid\Filter;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants as Constants;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class FilterQueryListener extends AbstractQueryListener
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var string | null
     */
    private $filter;

    public function __construct(EntityManager $entityManager, Reader $reader)
    {
        parent::__construct($entityManager);
        $this->reader = $reader;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!$request->isMethod('GET')) {
            return;
        }

        $this->filter = $event->getRequest()->query->get('filter');
    }

    /**
     * @param FilterQueryEvent $event
     */
    public function onFilterQuery(FilterQueryEvent $event)
    {
        if (!$this->getController()) {
            return;
        }

        $queryBuilder = $event->getQueryBuilder();
        $entityClass = $event->getEntityClass();
        $configurator = $this->getConfigurator($entityClass);
        /** @var Grid $grid */
        $grid = $configurator->getConfiguration(Grid::class);

        $filters = $grid->getFilters();
        if (!$filters && !$this->filter) {
            return;
        }

        if (strpos($this->filter, ':')) {
            $splitBySpace = array_filter(explode(' ', $this->filter), function ($value) {
                return strpos($value, ':') ? true : false;
            });
            $splitBySpace = array_map(function ($value) {
                return explode(':', $value);
            }, $splitBySpace);
            $fieldFilters = array();
            $keywords = array();
            foreach ($splitBySpace as $value) {
                if (in_array($value[0], $filters)) {
                    $fieldFilters[] = $value[0];
                    $keywords[] = $grid->isNormalizeFilter() ? strtoupper($value[1]) : $value[1];
                }
            }

            $this->applyFilterGithub($this->getClassMetadata($entityClass), $queryBuilder, $fieldFilters, $keywords);
        } else {
            $this->applyFilter($this->getClassMetadata($entityClass), $queryBuilder, $filters, $grid->isNormalizeFilter() ? strtoupper($this->filter) : $this->filter);
        }
    }

    /**
     * @param ClassMetadata $metadata
     * @param array         $fields
     *
     * @return array
     *
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    protected function getMapping(ClassMetadata $metadata, array $fields)
    {
        $filters = array();
        foreach ($fields as $field) {
            $fieldName = $metadata->getFieldName($field);
            try {
                $filters[] = $metadata->getFieldMapping($fieldName);
            } catch (\Exception $ex) {
                $mapping = $metadata->getAssociationMapping($fieldName);
                $associationMatadata = $this->getClassMetadata($mapping['targetEntity']);
                if ($filter = $this->getFilterFromAnnotation($mapping['targetEntity'])) {
                    $filters[] = array_merge(array(
                        'join' => true,
                        'join_field' => $fieldName,
                        'join_alias' => $this->getAlias(),
                    ), $associationMatadata->getFieldMapping($filter[0]));
                }
            }
        }

        return $filters;
    }

    /**
     * @param ClassMetadata $metadata
     * @param QueryBuilder  $queryBuilder
     * @param array         $filterFields
     * @param $filter
     */
    private function applyFilter(ClassMetadata $metadata, QueryBuilder $queryBuilder, array $filterFields, $filter)
    {
        foreach ($this->getMapping($metadata, $filterFields) as $key => $value) {
            if (array_key_exists('join', $value)) {
                $queryBuilder->leftJoin(sprintf('%s.%s', Constants::ENTITY_ALIAS, $value['join_field']), $value['join_alias'], 'WITH');
                $this->buildFilter($queryBuilder, $value, $value['join_alias'], $filter);
            } else {
                $this->buildFilter($queryBuilder, $value, Constants::ENTITY_ALIAS, $filter);
            }
        }
    }

    /**
     * @param ClassMetadata $metadata
     * @param QueryBuilder  $queryBuilder
     * @param array         $filterFields
     * @param array         $filters
     */
    private function applyFilterGithub(ClassMetadata $metadata, QueryBuilder $queryBuilder, array $filterFields, array $filters)
    {
        foreach ($this->getMapping($metadata, $filterFields) as $key => $value) {
            if (array_key_exists('join', $value)) {
                $queryBuilder->leftJoin(sprintf('%s.%s', Constants::ENTITY_ALIAS, $value['join_field']), $value['join_alias'], 'WITH');
                $this->buildFilterGithub($queryBuilder, $value, $value['join_alias'], $filters[$key]);
            } else {
                $this->buildFilterGithub($queryBuilder, $value, Constants::ENTITY_ALIAS, $filters[$key]);
            }
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $metadata
     * @param $alias
     * @param $filter
     */
    private function buildFilter(QueryBuilder $queryBuilder, array $metadata, $alias, $filter)
    {
        if (in_array($metadata['type'], array('date', 'datetime', 'time'))) {
            $date = \DateTime::createFromFormat($this->getContainer()->getParameter('symfonian_id.admin.date_time_format'), $filter);
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
     * @param QueryBuilder $queryBuilder
     * @param array        $metadata
     * @param $alias
     * @param $filter
     */
    private function buildFilterGithub(QueryBuilder $queryBuilder, array $metadata, $alias, $filter)
    {
        if (in_array($metadata['type'], array('date', 'datetime', 'time'))) {
            $date = \DateTime::createFromFormat($this->getContainer()->getParameter('sir.date_format'), $filter);
            if ($date) {
                $queryBuilder->andWhere(sprintf('%s.%s = :%s', $alias, $metadata['fieldName'], $metadata['fieldName']));
                $queryBuilder->setParameter($metadata['fieldName'], $date->format('Y-m-d'));
            }
        } elseif ('array' === $metadata['type']) {
            $queryBuilder->orWhere(sprintf('%s.%s LIKE :%s', $alias, $metadata['fieldName'], $metadata['fieldName']));
            $queryBuilder->setParameter($metadata['fieldName'], strtr('%filter%', array('filter' => serialize(array($filter)))));
        } else {
            $queryBuilder->orWhere(sprintf('%s.%s = :%s', $alias, $metadata['fieldName'], $metadata['fieldName']));
            $queryBuilder->setParameter($metadata['fieldName'], $filter);
        }
    }

    private function getFilterFromAnnotation($class)
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
}
