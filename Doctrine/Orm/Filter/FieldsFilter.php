<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Doctrine\Orm\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Symfonian\Indonesia\AdminBundle\Contract\SoftDeletableInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class FieldsFilter extends SQLFilter
{
    /**
     * @var array
     */
    private $fields;

    /**
     * Gets the SQL query part to add to a query.
     *
     * @param ClassMetadata $targetEntity
     * @param string        $targetTableAlias
     *
     * @return string The constraint SQL if there is available, empty string otherwise.
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        //@todo: use class annotation to get fields
        if (!$targetEntity->getReflectionClass()->implementsInterface(SoftDeletableInterface::class)) {
            return '';
        }

        $filter = '';
        foreach ($this->fields as $field) {
            $filter .= sprintf('%s.%s LIKE %%%s% OR', $targetTableAlias, $field, $this->getParameter('filter'));
        }

        return rtrim($filter, ' OR');
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }
}
