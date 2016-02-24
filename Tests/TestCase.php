<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfonian\Indonesia\AdminBundle;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    public function getContainerMock()
    {
        return $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(array(
                'set',
                'get',
                'has',
                'initialized',
                'setParameter',
                'getParameter',
                'hasParameter',
            ))
            ->getMock()
        ;
    }
}
