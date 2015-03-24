<?php
namespace Symfonian\Indonesia\AdminBundle\Routing;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class HomeRouteLoader implements LoaderInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    private $loaded = false;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "extra" loader twice');
        }

        $routes = new RouteCollection();

        $path = $this->container->getParameter('symfonian_id.admin.home.route_path');
        $defaults = array(
            '_controller' => $this->container->getParameter('symfonian_id.admin.home.controller'),
        );
        $route = new Route($path, $defaults);
        $route->setMethods('GET');

        $routes->add('home', $route);
        $this->loaded = true;

        return $routes;
    }

    public function supports($resource, $type = null)
    {
        return 'siab' === $type;
    }

    public function setResolver(LoaderResolverInterface $resolver)
    {
        //Useless for this case
    }

    public function getResolver()
    {
        //Useless for this case
    }
}
