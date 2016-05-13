<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Manager;

use Symfonian\Indonesia\AdminBundle\Contract\ConfigurationInterface;
use Symfonian\Indonesia\AdminBundle\Exception\KeyNotMatchException;

/**
 * @Annotation
 * @Target({"CLASS"})
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class Driver implements ConfigurationInterface
{
    const DOCTRINE_ORM = 'orm';

    const DOCTRINE_ODM = 'odm';

    public static $DRIVERS = array(
        self::DOCTRINE_ORM => 'doctrine',
        self::DOCTRINE_ODM => 'doctrine_mongodb',
    );

    /**
     * @var string
     */
    private $value;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        if (isset($data['value']) && is_string($data['value'])) {
            if (!in_array($data['value'], array(self::DOCTRINE_ORM, self::DOCTRINE_ODM))) {
                throw new KeyNotMatchException(sprintf('%s is not valid object managerFactory', $data['value']));
            }

            $this->value = $data['value'];
        }
    }

    /**
     * @return string
     */
    public function getDriver()
    {
        return $this->value;
    }
}
