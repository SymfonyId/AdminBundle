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
     * @var Configurator
     */
    private $configurator;

    /**
     * @var UploadHandler
     */
    private $uploader;

    public function __construct(Reader $reader, Configurator $configurator, UploadHandler $uploadHandler)
    {
        $this->reader = $reader;
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
            if ($annotation instanceof Crud) {
                $this->compileTemplate($annotation);
            }

            if ($annotation instanceof Grid) {
                $this->compileGrid($annotation);
            }

            if ($annotation instanceof Page) {
                $this->compilePage($annotation);
            }

            if ($annotation instanceof UtilAnnotationInterface) {
                $this->compileUtil($annotation);
            }
        }
    }

    private function compileTemplate(Crud $annotation)
    {
        if ($annotation->getAdd()) {
            $this->configurator->setNewTemplate($annotation->getAdd());
        }

        if ($annotation->getEdit()) {
            $this->configurator->setEditTemplate($annotation->getEdit());
        }

        if ($annotation->getShow()) {
            $this->configurator->setShowTemplate($annotation->getShow());
        }

        if ($annotation->getList()) {
            $this->configurator->setListTemplate($annotation->getList());
        }

        if ($annotation->getForm()) {
            $this->configurator->setFormClass($annotation->getForm());
        }

        if ($annotation->getEntity()) {
            $this->configurator->setEntityClass($annotation->getEntity());
        }

        if ($annotation->getShowFields()) {
            $this->configurator->setShowFields($annotation->getShowFields());
        }

        if ($annotation->getAjaxTemplate()) {
            $this->configurator->setAjaxTemplate($annotation->getAjaxTemplate());
        }
    }

    private function compileGrid(Grid $annotation)
    {
        if ($annotation->getFields()) {
            $this->configurator->setGridFields($annotation->getFields());
        }

        if ($annotation->getFilter()) {
            $this->configurator->setFilter($annotation->getFilter());
        }

        $this->configurator->setNormalizeFilter($annotation->isNormalizeFilter());
        $this->configurator->setFormatNumber($annotation->isFormatNumber());
    }

    private function compilePage(Page $annotation)
    {
        $this->configurator->setTitle($annotation->getTitle());
        $this->configurator->setDescription($annotation->getDescription());
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
