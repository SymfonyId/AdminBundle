<?php

namespace Symfonian\Indonesia\AdminBundle\Route;

use Doctrine\Common\Annotations\Reader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfonian\Indonesia\AdminBundle\Controller\CrudController;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;
use Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Route as SymfonyRoute;
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

        $collection = new RouteCollection();
        foreach ($resources as $resource) {
            $controllers = $this->findAllControllerFromDir($this->getControllerDir($resource));
            /** @var \ReflectionClass $controller */
            foreach ($controllers as $controller) {
                if ($controller->isSubclassOf(CrudController::class)) {
                    $this->registerRoute($collection, $controller);
                }
            }
        }

        $this->loaded = true;

        return $collection;
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

    private function registerRoute(RouteCollection $collection, \ReflectionClass $controller)
    {
        foreach ($controller->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (strpos(strtolower($method), 'action')) {
                $prefixName = str_replace('\\', '_', $controller->getName());
                $collection->addCollection($this->compileRoute($prefixName, $this->parseController($controller), $controller, $method));
            }
        }
    }

    private function parseController(\ReflectionClass $reflectionClass)
    {
        $classAnnotations = $this->reader->getClassAnnotations($reflectionClass);
        foreach ($classAnnotations as $annotation) {
            if ($annotation instanceof Route) {
                return $annotation;
            }
        }

        return new Route(array('path' => ''));
    }

    private function compileRoute($prefixName, Route $route, \ReflectionClass $class, \ReflectionMethod $method)
    {
        $collection = new RouteCollection();
        if ($path = $route->getPath()) {
            $collection->addPrefix($path, $route->getDefaults());
        }

        $annoations = $this->reader->getMethodAnnotations($method);
        $routeAnnotations = array();
        $methodAnnotaion = null;
        foreach ($annoations as $key => $annoation) {
            if ($annoation instanceof Route) {
                $routeAnnotations[] = $annoation;
            }
            if ($annoation instanceof Method) {
                $methodAnnotaion = $annoation;
            }
        }

        if (empty($routeAnnotations)) {
            $this->addRoute($method, $collection, strtolower($prefixName.'_'.$method->getName()), null, null);
        } else {
            foreach ($routeAnnotations as $routeAnnotation) {
                $this->addRoute($method, $collection, strtolower($prefixName.'_'.$method->getName()), $routeAnnotation, $methodAnnotaion);
            }
        }

        return $collection;
    }

    private function addRoute(\ReflectionMethod $reflectionMethod, RouteCollection $collection, $name, Route $route = null, Method $method = null)
    {
        $loop = true;
        $index = 0;
        While ($loop) {
            $methodName = str_replace('action', '', strtolower($reflectionMethod->getName()));
            if ('list' === $methodName && 0 === $index) {
                $loop = true;
                $index++;
            } else {
                $loop = false;
            }
            if (!$route || 'list' === $methodName) {
                $route = $this->generateRoute($methodName, $loop);
                $method = $this->generateMethod($methodName);
            }

            $symfonyRoute = new SymfonyRoute(
                $route->getPath(),
                $route->getDefaults(),
                $route->getRequirements(),
                array_merge($route->getOptions(), array('expose' => true)),
                $route->getHost(),
                $route->getSchemes(),
                $route->getMethods()?: $method->getMethods(),
                $route->getCondition()
            );
            $routeName = str_replace(array('bundle', 'controller', 'action', '__'), array('', '', '', '_'), $name);
            $collection->add($this->getUniqueRouteName($collection, $routeName), $symfonyRoute);
        }
    }

    private function getUniqueRouteName(RouteCollection $collection, $name)
    {
        $flag = false;
        $index = 1;
        while ($flag === false) {
            if ($collection->get($name)) {
                $name = $name.'_'.$index++;
            } else {
                $flag = true;
            }
        }

        return $name;
    }

    private function generateMethod($methodName)
    {
        switch ($methodName) {
            case 'new':
            case 'edit':
                return new Method(array(
                    'methods' => array('GET', 'POST'),
                ));
                break;
            case 'show':
            case 'list':
                return new Method(array(
                    'methods' => array('GET'),
                ));
                break;
            case 'delete':
                return new Method(array(
                    'methods' => array('DELETE'),
                ));
                break;
        }

        return new Method(array(
            'methods' => array(),
        ));
    }

    private function generateRoute($methodName, $flag = false)
    {
        switch ($methodName) {
            case 'new':
            case 'list':
                VarDumper::dump($flag);
                if (!$flag) {
                    return new Route(array(
                            'path' => '/'.$methodName.'/')
                    );
                } else {
                    return new Route(array(
                            'path' => '/')
                    );
                }
                break;
            case 'edit':
            case 'show':
            case 'delete':
                return new Route(array(
                    'path' => '/{id}/'.$methodName.'/'
                ));
                break;
        }

        return new Route(array('path' => '/'));
    }
}