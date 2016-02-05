<?php

namespace Symfonian\Indonesia\AdminBundle\Route;

use Doctrine\Common\Annotations\Reader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfonian\Indonesia\AdminBundle\Controller\CrudController;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;
use Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\VarDumper\VarDumper;

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
     * @param string $resources
     * @param null   $type
     *
     * @return RouteCollection
     */
    public function load($resources, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "siab" loader twice');
        }

        if (!is_array($resources)) {
            $resources = (array) $resources;
        }

        $collections = array();
        foreach ($resources as $resource) {
            $controllers = $this->findAllControllerFromDir($this->getControllerDir($resource));
            /** @var \ReflectionClass $controller */
            foreach ($controllers as $controller) {
                if ($controller->isSubclassOf(CrudController::class)) { //&& !$this->isUseRouteAnnotation($controller)) {
                    $collections[] = $this->registerRoute($controller);
                } else {
                    $collections[] = parent::load($resource, null);
                }
            }
        }

        exit();

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

    private function getControllerDir($resource)
    {
        return $this->kernel->locateResource($resource);
    }

    private function findAllControllerFromDir($dir)
    {
        $finder = new Finder();
        $finder->name('*Controller.php')->in($dir);


        $controllers = array();
        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $controllers[] = $this->getReflectionClass($file);
        }

        return $controllers;
    }

    private function getReflectionClass(SplFileInfo $file)
    {
        $namespace = null;
        if (preg_match('#^namespace\s+(.+?);$#sm', $file->getContents(), $matches)) {
            $namespace = $matches[1];
        }

        $controller = null;
        if ($namespace) {
            $name = substr($file->getRelativePathname(), 0, -4);
            $controller = sprintf('%s\\%s', $namespace, $name);
        }

        if ($controller) {
            return new \ReflectionClass($controller);
        }

        return null;
    }

    private function isUseRouteAnnotation(\ReflectionClass $reflectionClass)
    {
        foreach ($this->reader->getClassAnnotations($reflectionClass) as $annotation) {
            if ($annotation instanceof Route) {
                return true;
            }
        }

        foreach ($reflectionClass->getMethods() as $method) {
            foreach ($this->reader->getMethodAnnotations($method) as $annotation) {
                if ($annotation instanceof Route) {
                    return true;
                }
            }
        }

        return false;
    }

    private function registerRoute(\ReflectionClass $controller)
    {
        VarDumper::dump($controller);
    }
}