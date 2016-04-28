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

use Symfonian\Indonesia\AdminBundle\Event\FilterQueryEvent;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\SoftDeletableInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class FilterSoftDeletableListener extends AbstractQueryListener
{
    /**
     * @param FilterQueryEvent $event
     */
    public function onFilterQuery(FilterQueryEvent $event)
    {
        if (!$this->getController()) {
            return;
        }

        $entityClass = $event->getEntityClass();
        $alias = $event->getAlias();
        $queryBuilder = $event->getQueryBuilder();

        $reflection = new \ReflectionClass($entityClass);
        foreach ($reflection->getInterfaces() as $reflectionClass) {
            if ($reflectionClass->getName() === SoftDeletableInterface::class) {
                $queryBuilder->andWhere($alias.'.isDeleted = false');

                break;
            }
        }
    }
}
