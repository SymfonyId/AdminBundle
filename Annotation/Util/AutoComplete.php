<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Annotation\Util;

use Symfonian\Indonesia\AdminBundle\Contract\ConfigurationInterface;

/**
 * @Annotation
 * @Target({"CLASS"})
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class AutoComplete implements ConfigurationInterface
{
    /**
     * Route to search action.
     *
     * @var string
     */
    private $routeStore;

    /**
     * Route to get id action.
     *
     * @var string
     */
    private $routeCallback;

    /**
     * jQuery selector to store value.
     *
     * @var string
     */
    private $targetSelector;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        if (isset($data['routeStore'])) {
            $this->routeStore = $data['routeStore'];
        }

        if (isset($data['routeCallback'])) {
            $this->routeCallback = $data['routeCallback'];
        }

        if (isset($data['targetSelector'])) {
            $this->targetSelector = $data['targetSelector'];
        }

        unset($data);
    }

    /**
     * @return string
     */
    public function getRouteStore()
    {
        return $this->routeStore;
    }

    /**
     * @return string
     */
    public function getRouteCallback()
    {
        return $this->routeCallback;
    }

    /**
     * @return string
     */
    public function getTargetSelector()
    {
        return $this->targetSelector;
    }

    /**
     * @param string $routeStore
     */
    public function setRouteStore($routeStore)
    {
        $this->routeStore = $routeStore;
    }

    /**
     * @param string $routeCallback
     */
    public function setRouteCallback($routeCallback)
    {
        $this->routeCallback = $routeCallback;
    }

    /**
     * @param string $targetSelector
     */
    public function setTargetSelector($targetSelector)
    {
        $this->targetSelector = $targetSelector;
    }
}
