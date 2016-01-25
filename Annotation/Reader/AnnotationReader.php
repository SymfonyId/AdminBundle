<?php

namespace Symfonian\Indonesia\AdminBundle\Annotation\Reader;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Doctrine\Common\Annotations\Reader;
use ReflectionObject;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Crud;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Grid;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Page;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Util\AutoComplete;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Util\DatePicker;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Util\Editor;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Util\FileChooser;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Util\IncludeJavascript;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Util\Upload;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Util\UtilAnnotationInterface;
use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationFactory;
use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\Controller\CrudController;
use Symfonian\Indonesia\AdminBundle\Handler\UploadHandler;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

final class AnnotationReader
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var ConfigurationFactory
     */
    private $configurationFactory;

    /**
     * @var Configurator
     */
    private $configurator;

    /**
     * @var UploadHandler
     */
    private $uploader;

    public function __construct(Reader $reader, ConfigurationFactory $configurationFactory, Configurator $configurator, UploadHandler $uploadHandler)
    {
        $this->reader = $reader;
        $this->configurationFactory = $configurationFactory;
        $this->configurator = $configurator;
        $this->uploader = $uploadHandler;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (!is_array($controller)) {
            return;
        }

        $controller = $controller[0];
        if (!$controller instanceof CrudController) {
            return;
        }

        $reflectionObject = new ReflectionObject($controller);
        unset($controller);
        foreach ($this->reader->getClassAnnotations($reflectionObject) as $annotation) {
            $this->configurationFactory->addConfiguration($annotation);
            if ($annotation instanceof UtilAnnotationInterface) {
                $this->compileUtil($annotation);
            }
        }
    }

    private function compileUtil(UtilAnnotationInterface $annotation)
    {
        if ($annotation instanceof AutoComplete) {
            $this->configurator->setAutoComplete($annotation->getRoute(), $annotation->getTargetSelector());
        }

        if ($annotation instanceof DatePicker) {
            $this->configurator->setUseDatePicker(true);
        }

        if ($annotation instanceof Editor) {
            $this->configurator->setUseEditor(true);
        }

        if ($annotation instanceof FileChooser) {
            $this->configurator->setUseFileStyle(true);
        }

        if ($annotation instanceof IncludeJavascript) {
            $this->configurator->setJavascript($annotation->getFile(), $annotation->getIncludeRoute());
        }

        if ($annotation instanceof Upload) {
            $this->uploader->setFields($annotation->getFields());
        }
    }
}
