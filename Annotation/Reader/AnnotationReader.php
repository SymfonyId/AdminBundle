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
use Symfonian\Indonesia\AdminBundle\Controller\CrudController;
use Symfonian\Indonesia\AdminBundle\Handler\ConfigurationHandler;
use Symfonian\Indonesia\AdminBundle\Handler\UploadHandler;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

final class AnnotationReader
{
    private $reader;

    private $configuration;

    private $uploader;

    public function __construct(Reader $reader, ConfigurationHandler $configuration, UploadHandler $uploadHandler)
    {
        $this->reader = $reader;
        $this->configuration = $configuration;
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
                if ($annotation instanceof AutoComplete) {
                    $this->configuration->setAutoComplete($annotation->getRoute(), $annotation->getTargetSelector());
                }

                if ($annotation instanceof DatePicker) {
                    $this->configuration->setUseDatePicker(true);
                }

                if ($annotation instanceof Editor) {
                    $this->configuration->setUseEditor(true);
                }

                if ($annotation instanceof FileChooser) {
                    $this->configuration->setUseFileStyle(true);
                }

                if ($annotation instanceof IncludeJavascript) {
                    $this->configuration->setJavascript($annotation->getFile(), $annotation->getIncludeRoute());
                }

                if ($annotation instanceof Upload) {
                    $this->uploader->setFields($annotation->getFields());
                }
            }
        }
    }

    private function compileTemplate(Crud $annotation)
    {
        if ($annotation->getAdd()) {
            $this->configuration->setNewTemplate($annotation->getAdd());
        }

        if ($annotation->getEdit()) {
            $this->configuration->setEditTemplate($annotation->getEdit());
        }

        if ($annotation->getShow()) {
            $this->configuration->setShowTemplate($annotation->getShow());
        }

        if ($annotation->getList()) {
            $this->configuration->setListTemplate($annotation->getList());
        }

        if ($annotation->getForm()) {
            $this->configuration->setFormClass($annotation->getForm());
        }

        if ($annotation->getEntity()) {
            $this->configuration->setEntityClass($annotation->getEntity());
        }

        if ($annotation->getShowFields()) {
            $this->configuration->setShowFields($annotation->getShowFields());
        }

        if ($annotation->getAjaxTemplate()) {
            $this->configuration->setAjaxTemplate($annotation->getAjaxTemplate());
        }
    }

    private function compileGrid(Grid $annotation)
    {
        if ($annotation->getFields()) {
            $this->configuration->setGridFields($annotation->getFields());
        }

        if ($annotation->getFilter()) {
            $this->configuration->setFilter($annotation->getFilter());
        }

        $this->configuration->setNormalizeFilter($annotation->isNormalizeFilter());
        $this->configuration->setFormatNumber($annotation->isFormatNumber());
    }

    private function compilePage(Page $annotation)
    {
        $this->configuration->setTitle($annotation->getTitle());
        $this->configuration->setDescription($annotation->getDescription());
    }
}
