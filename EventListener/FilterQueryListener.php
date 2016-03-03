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
use Doctrine\ORM\QueryBuilder;
use Symfonian\Indonesia\AdminBundle\Annotation\Grid;
use Symfonian\Indonesia\AdminBundle\Configuration\ConfiguratorAwareTrait;
use Symfonian\Indonesia\AdminBundle\Event\FilterQueryEvent;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants as Constants;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class FilterQueryListener implements ContainerAwareInterface
{
    use ConfiguratorAwareTrait;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EntityManager
     */
    private $manager;

    private $filter;
    private static $ALIAS = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j');
    private static $ALIAS_USED = array(Constants::ENTITY_ALIAS);

    public function __construct(EntityManager $entityManager)
    {
        $this->manager = $entityManager;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->filter = $event->getRequest()->query->get('filter');
    }

    public function onFilterQuery(FilterQueryEvent $event)
    {
        $queryBuilder = $event->getQueryBuilder();
        $entityClass = $event->getEntityClass();
        $configurator = $this->getConfigurator($entityClass);
        /** @var Grid $grid */
        $grid = $configurator->getConfiguration(Grid::class);

        if (!$grid->getFilters()) {
            return;
        }
    }

    /**
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->container;
    }

    private function getMapping(array $fields)
    {
        $filters = array();
        foreach ($fields as $field) {
            $fieldName = $this->getFieldName($field);
            try {
                $filters[] = $this->getClassMeatadata()->getFieldMapping($fieldName);
            } catch (\Exception $ex) {
                $mapping = $this->getClassMeatadata()->getAssociationMapping($fieldName);
                $associationMatadata = $this->manager->getClassMetadata($mapping['targetEntity']);
                $associationFields = $associationMatadata->getFieldNames();
                $associationIdentifier = $associationMatadata->getIdentifierFieldNames();
                $associationFields = array_values(array_filter(
                    $associationFields,
                    function ($value) use ($associationIdentifier) {
                        return !in_array($value, $associationIdentifier);
                    }
                ));
                if ($associationFields) {
                    $filters[] = array_merge(array(
                        'join' => true,
                        'join_field' => $fieldName,
                        'join_alias' => $this->getAlias(),
                    ), $associationMatadata->getFieldMapping($associationFields[0]));
                }
            }
        }

        return $filters;
    }

    private function getFieldName($field)
    {
        return $this->getClassMeatadata()->getFieldName($field);
    }

    private function getAlias()
    {
        $available = array_values(array_diff(self::$ALIAS, self::$ALIAS_USED));
        $alias = $available[0];
        self::$ALIAS_USED[] = $alias;

        return $alias;
    }

    private function applyFilter(QueryBuilder $queryBuilder, array $filterFields, $filter)
    {
        foreach ($this->getMapping($filterFields) as $key => $value) {
            if (array_key_exists('join', $value)) {
                $queryBuilder->leftJoin(sprintf('%s.%s', Constants::ENTITY_ALIAS, $value['join_field']), $value['join_alias'], 'WITH');
                $this->buildFilter($queryBuilder, $value, $value['join_alias'], $key, $filter);
            } else {
                $this->buildFilter($queryBuilder, $value, Constants::ENTITY_ALIAS, $key, $filter);
            }
        }
    }

    private function buildFilter(QueryBuilder $queryBuilder, array $metadata, $alias, $parameter, $filter)
    {
        if (in_array($metadata['type'], array('date', 'datetime', 'time'))) {
            $date = \DateTime::createFromFormat($this->container->getParameter('symfonian_id.admin.date_time_format'), $filter);
            if ($date) {
                $queryBuilder->orWhere(sprintf('%s.%s = ?%d', $alias, $metadata['fieldName'], $parameter));
                $queryBuilder->setParameter($parameter, $date->format('Y-m-d'));
            }
        } else {
            $queryBuilder->orWhere(sprintf('%s.%s LIKE ?%d', $alias, $metadata['fieldName'], $parameter));
            $queryBuilder->setParameter($parameter, strtr('%filter%', array('filter' => $filter)));
        }
    }

    private function getClassMeatadata($entityClass)
    {
        return $this->manager->getClassMetadata($entityClass);
    }
}