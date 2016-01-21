<?php

namespace Symfonian\Indonesia\AdminBundle\Route;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class HomeRouteLoader implements LoaderInterface
{
    private $routePath;

    private $controller;

    private $loaded = false;

    public function __construct($routePath, $controller)
    {
        $this->routePath = $routePath;
        $this->controller = $controller;
    }

    /**
     * @param string $resource
     * @param null   $type
     *
     * @return RouteCollection
     */
    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "siab" loader twice');
        }

        $routes = new RouteCollection();

        $defaults = array(
            '_controller' => $this->controller,
        );
        $route = new Route($this->routePath, $defaults, array(), array('expose' => true));
        $route->setMethods('GET');

        $routes->add('home', $route);
        $this->loaded = true;

        return $routes;
    }

    /**
     * @param string $resource
     * @param null   $type
     *
     * @return bool
     */
    public function supports($resource, $type = null)
    {
        return 'siab' === $type;
    }

    public function setResolver(LoaderResolverInterface $resolver)
    {
    }

    public function getResolver()
    {
    }
}
