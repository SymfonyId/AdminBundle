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

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Symfonian\Indonesia\AdminBundle\Annotation\Grid;
use Symfonian\Indonesia\AdminBundle\Event\FilterQueryEvent;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants as Constants;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class FilterQueryListener extends AbstractQueryListener
{
    /**
     * @var string | null
     */
    private $filter;

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager);
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
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

        if (!$filters = $grid->getFilters()) {
            return;
        }

        $this->applyFilter($this->getClassMeatadata($entityClass), $queryBuilder, $filters, $this->filter);
    }

    /**
     * @param ClassMetadata $metadata
     * @param array $fields
     * @return array
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
                $associationMatadata = $this->getClassMeatadata($mapping['targetEntity']);
                $associationConfigurator = $this->getConfigurator($mapping['targetEntity']);
                /** @var Grid $associationGrid */
                $associationGrid = $associationConfigurator->getConfiguration(Grid::class);
                if ($filter = $associationGrid->getFilters()) {
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
     * @param QueryBuilder $queryBuilder
     * @param array $filterFields
     * @param $filter
     */
    private function applyFilter(ClassMetadata $metadata, QueryBuilder $queryBuilder, array $filterFields, $filter)
    {
        foreach ($this->getMapping($metadata, $filterFields) as $key => $value) {
            if (array_key_exists('join', $value)) {
                $queryBuilder->leftJoin(sprintf('%s.%s', Constants::ENTITY_ALIAS, $value['join_field']), $value['join_alias'], 'WITH');
                $this->buildFilter($queryBuilder, $value, $value['join_alias'], $key, $filter);
            } else {
                $this->buildFilter($queryBuilder, $value, Constants::ENTITY_ALIAS, $key, $filter);
            }
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array $metadata
     * @param $alias
     * @param $parameter
     * @param $filter
     */
    private function buildFilter(QueryBuilder $queryBuilder, array $metadata, $alias, $parameter, $filter)
    {
        if (in_array($metadata['type'], array('date', 'datetime', 'time'))) {
            $date = \DateTime::createFromFormat($this->getContainer()->getParameter('symfonian_id.admin.date_time_format'), $filter);
            if ($date) {
                $queryBuilder->orWhere(sprintf('%s.%s = ?%d', $alias, $metadata['fieldName'], $parameter));
                $queryBuilder->setParameter($parameter, $date->format('Y-m-d'));
            }
        } else {
            $queryBuilder->orWhere(sprintf('%s.%s LIKE ?%d', $alias, $metadata['fieldName'], $parameter));
            $queryBuilder->setParameter($parameter, strtr('%filter%', array('filter' => $filter)));
        }
    }
}
