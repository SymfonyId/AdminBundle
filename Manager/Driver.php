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

/**
 * @Annotation
 * @Target({"CLASS"})
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class Driver implements ConfigurationInterface
{
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
