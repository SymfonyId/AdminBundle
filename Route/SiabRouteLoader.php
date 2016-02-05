<?php

namespace Symfonian\Indonesia\AdminBundle\Route;

use Doctrine\Common\Annotations\Reader;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;
use Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class SiabRouteLoader extends DelegatingLoader
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var KernelInterface
     */
    private $kernel;

    private $loaded = false;

    public function __construct(ControllerNameParser $parser, LoaderResolverInterface $resolver, Reader $reader, KernelInterface $kernel)
    {
        $this->reader = $reader;
        $this->kernel = $kernel;
        parent::__construct($parser, $resolver);
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

        $this->loaded = true;
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
}