<?php

namespace Symfonian\Indonesia\AdminBundle\Annotation\Reader;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use ReflectionObject;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfonian\Indonesia\AdminBundle\Controller\CrudController;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\CrudTemplate;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Entity;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Grid;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Page;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Util\UtilAnnotationInterface;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Util\AutoComplete;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Util\DatePicker;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Util\Editor;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Util\FileChooser;
use Symfonian\Indonesia\AdminBundle\Annotation\Schema\Util\IncludeJavascript;

final class AnnotationReader
{
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
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
        foreach ($this->reader->getClassAnnotations($reflectionObject) as $annotation) {
            if ($annotation instanceof CrudTemplate) {
                $this->compileTemplate($annotation);
            }

            if ($annotation instanceof Entity) {
                $this->compileEntity($annotation);
            }

            if ($annotation instanceof Grid) {
                $this->compileGrid($annotation);
            }

            if ($annotation instanceof Page) {
                $this->compilePage($annotation);
            }

            if ($annotation instanceof UtilAnnotationInterface) {
                if ($annotation instanceof AutoComplete) {
                    $this->compileAutoComplete($annotation);
                }

                if ($annotation instanceof DatePicker) {
                    //@todo
                }

                if ($annotation instanceof Editor) {
                    //@todo
                }

                if ($annotation instanceof FileChooser) {
                    //@todo
                }

                if ($annotation instanceof IncludeJavascript) {
                    $this->compileJavascript($annotation);
                }
            }
        }
    }

    private function compileTemplate(CrudTemplate $annotation)
    {
    }

    private function compileEntity(Entity $annotation)
    {
    }

    private function compileGrid(Grid $annotation)
    {
    }

    private function compilePage(Page $annotation)
    {
    }

    private function compileAutoComplete(AutoComplete $annotation)
    {
    }

    private function compileJavascript(IncludeJavascript $annotation)
    {
    }
}
