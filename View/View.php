<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\View;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class View
{
    /**
     * @var array
     */
    private $params = array();

    /**
     * @param string $key
     * @param string $value
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
    }

    /**
     * @param string $key
     * @return null|string
     */
    public function getParam($key)
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        foreach ($params as $key => $param) {
            $this->setParam($key, $param);
        }
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}