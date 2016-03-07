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
use Symfonian\Indonesia\AdminBundle\Configuration\ConfiguratorAwareTrait;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants as Constants;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
abstract class AbstractQueryListener extends AbstractListener implements ContainerAwareInterface
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

    private static $ALIAS = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j');
    private static $ALIAS_USED = array(Constants::ENTITY_ALIAS);

    /**
     * @param ClassMetadata $metadata
     * @param array $fields
     * @return array
     */
    abstract protected function getMapping(ClassMetadata $metadata, array $fields);

    public function __construct(EntityManager $entityManager)
    {
        $this->manager = $entityManager;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $this->isValidCrudListener($event);
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
     * @return ClassMetadata
     */
    protected function getClassMetadata($entityClass)
    {
        return $this->manager->getClassMetadata($entityClass);
    }
}