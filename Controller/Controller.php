<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Controller;

use Symfonian\Indonesia\AdminBundle\Configuration\ConfiguratorAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as Base;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
abstract class Controller extends Base
{
    use ConfiguratorAwareTrait;

    /**
     * Get controller class name.
     *
     * @return string
     */
    abstract protected function getClassName();

    /**
     * Dispatch event.
     *
     * @param $name
     * @param $handler
     */
    protected function fireEvent($name, $handler)
    {
        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch($name, $handler);
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->container;
    }
}
