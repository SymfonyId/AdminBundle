<?php

namespace Symfonian\Indonesia\AdminBundle\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Symfonian\Indonesia\AdminBundle\Manager\ManagerFactory;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants as Constants;

abstract class AbstractFilter
{
    /**
     * @var ManagerFactory
     */
    private $managerFactory;

    /**
     * @var array
     */
    private static $ALIAS = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j');

    /**
     * @var array
     */
    private static $ALIAS_USED = array(Constants::ENTITY_ALIAS);

    /**
     * @var string DB Driver
     */
    private $driver;

    /**
     * @param string $class
     */
    abstract protected function readAnnotation($class);

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $metadata
     * @param string       $alias
     * @param string       $filter
     */
    abstract protected function doFilter(QueryBuilder $queryBuilder, array $metadata, $alias, $filter = null);

    /**
     * @param ManagerFactory $managerFactory
     */
    public function __construct(ManagerFactory $managerFactory)
    {
        $this->managerFactory = $managerFactory;
    }

    /**
     * @param $entityClass
     * @param QueryBuilder $queryBuilder
     * @param array        $filterFields
     * @param array        $filters
     */
    public function createFilter($entityClass, QueryBuilder $queryBuilder, array $filterFields, array $filters = array())
    {
        $filter = null;
        $useKey = false;
        if (1 === count($filters)) {
            $filter = $filters[0];
        }
        if (count($filters) === count($filterFields)) {
            $useKey = true;
        }
        foreach ($this->getMapping($this->getClassMetadata($entityClass), $filterFields) as $key => $value) {
            if (array_key_exists('join', $value)) {
                $queryBuilder->leftJoin(sprintf('%s.%s', Constants::ENTITY_ALIAS, $value['join_field']), $value['join_alias'], 'WITH');
                $this->doFilter($queryBuilder, $value, $value['join_alias'], $useKey ? $filters[$key] : $filter);
            } else {
                $this->doFilter($queryBuilder, $value, Constants::ENTITY_ALIAS, $useKey ? $filters[$key] : $filter);
            }
        }
    }

    /**
     * @return string
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param string $driver
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;
    }

    /**
     * @return string
     */
    protected function getAlias()
    {
        $available = array_values(array_diff(self::$ALIAS, self::$ALIAS_USED));
        $alias = $available[0];
        self::$ALIAS_USED[] = $alias;

        return $alias;
    }

    /**
     * @param $entityClass
     *
     * @return ClassMetadata
     */
    protected function getClassMetadata($entityClass)
    {
        return $this->managerFactory->getManager($this->getDriver())->getClassMetadata($entityClass);
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
                if ($filter = $this->readAnnotation($mapping['targetEntity'])) {
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
}
