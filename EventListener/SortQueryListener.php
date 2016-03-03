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
class SortQueryListener extends AbstractQueryListener
{
    /**
     * @var string | null
     */
    private $sort;

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

        $this->sort = $event->getRequest()->query->get('sort_by');
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

        if (!$grid->getFilters()) {
            return;
        }

        $this->applySort($this->getClassMeatadata($entityClass), $queryBuilder, array($this->sort));
    }

    /**
     * @param ClassMetadata $metadata
     * @param array $fields
     * @return array
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    protected function getMapping(ClassMetadata $metadata, array $fields)
    {
        $sorts = array();
        foreach ($fields as $field) {
            $fieldName = $metadata->getFieldName($field);
            try {
                $sorts[] = $metadata->getFieldMapping($fieldName);
            } catch (\Exception $ex) {
                $mapping = $metadata->getAssociationMapping($fieldName);
                $associationMatadata = $this->getClassMeatadata($mapping['targetEntity']);
                $associationConfigurator = $this->getConfigurator($mapping['targetEntity']);
                /** @var Grid $associationGrid */
                $associationGrid = $associationConfigurator->getConfiguration(Grid::class);
                if ($sort = $associationGrid->getSortable()) {
                    $sorts[] = array_merge(array(
                        'join' => true,
                        'join_field' => $fieldName,
                        'join_alias' => $this->getAlias(),
                    ), $associationMatadata->getFieldMapping($sort[0]));
                }
            }
        }

        return $sorts;
    }

    /**
     * @param ClassMetadata $metadata
     * @param QueryBuilder $queryBuilder
     * @param array $fields
     */
    private function applySort(ClassMetadata $metadata, QueryBuilder $queryBuilder, array $fields)
    {
        foreach ($this->getMapping($metadata, $fields) as $key => $value) {
            if (array_key_exists('join', $value)) {
                $queryBuilder->leftJoin(sprintf('%s.%s', Constants::ENTITY_ALIAS, $value['join_field']), $value['join_alias'], 'WITH');
                $queryBuilder->addOrderBy(sprintf('%s.%s', $value['join_alias'], $value['join_field']));
            }
        }
    }
}
