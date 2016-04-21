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

use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationInterface;

/**
 * @Annotation
 * @Target({"CLASS"})
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ExternalJavascript implements ConfigurationInterface
{
    /**
     * Ex: @ExternalJavascript(files={"@AppBundle/Resources/views/ajax/ajax.js.twig", "@AppBundle/Resources/views/ajax/second-ajax.js.twig"}).
     *
     * @var array
     */
    private $files = array();

    /**
     * Ex: @ExternalJavascript(routes={"routeName1", "routeName2"}).
     *
     * @var array
     */
    private $routes = array();

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        if (isset($data['files'])) {
            $this->files = $data['files'];
        }

        if (isset($data['routes'])) {
            $this->routes = $data['routes'];
        }

        unset($data);
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param array $files
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * @param array $routes
     */
    public function setRoutes($routes)
    {
        $this->routes = $routes;
    }
}
