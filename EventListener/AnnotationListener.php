<?php
namespace Symfonian\Indonesia\AdminBundle\EventListener;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Doctrine\Common\Annotations\Reader;

use Symfonian\Indonesia\AdminBundle\Controller\CrudController;

use Symfonian\Indonesia\AdminBundle\Annotation\FormClass;
use Symfonian\Indonesia\AdminBundle\Annotation\EntityClass;
use Symfonian\Indonesia\AdminBundle\Annotation\NormalizeFilter;
use Symfonian\Indonesia\AdminBundle\Annotation\PageDescription;
use Symfonian\Indonesia\AdminBundle\Annotation\PageTitle;
use Symfonian\Indonesia\AdminBundle\Annotation\GridFields;
use Symfonian\Indonesia\AdminBundle\Annotation\ShowFields;
use Symfonian\Indonesia\AdminBundle\Annotation\NewActionTemplate;
use Symfonian\Indonesia\AdminBundle\Annotation\EditActionTemplate;
use Symfonian\Indonesia\AdminBundle\Annotation\ShowActionTemplate;
use Symfonian\Indonesia\AdminBundle\Annotation\ListActionTemplate;
use Symfonian\Indonesia\AdminBundle\Annotation\IncludeJavascript;
use Symfonian\Indonesia\AdminBundle\Annotation\FilterFields;

final class AnnotationListener
{
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function onKernelController(FilterControllerEvent $event)
    {        $controller = $event->getController();

        if (! is_array($controller)) {

            return;
        }

        $controller = $controller[0];

        if (! $controller instanceof CrudController) {

            return;
        }

        $reflectionObject = new \ReflectionObject($controller);

        foreach ($this->reader->getClassAnnotations($reflectionObject) as $annotation) {
            if ($annotation instanceof EntityClass && $annotation->value) {
                $controller->setEntityClass($annotation->value);
            }

            if ($annotation instanceof FormClass && $annotation->value) {
                $controller->setFormClass($annotation->value);
            }

            if ($annotation instanceof PageTitle && $annotation->value) {
                $controller->setPageTitle($annotation->value);
            }

            if ($annotation instanceof PageDescription && $annotation->value) {
                $controller->setPageDescription($annotation->value);
            }

            if ($annotation instanceof NewActionTemplate && $annotation->value) {
                $controller->setNewTemplate($annotation->value);
            }

            if ($annotation instanceof EditActionTemplate && $annotation->value) {
                $controller->setEditTemplate($annotation->value);
            }

            if ($annotation instanceof ShowActionTemplate && $annotation->value) {
                $controller->setShowTemplate($annotation->value);
            }

            if ($annotation instanceof ListActionTemplate) {
                if ($annotation->value) {
                    $controller->setListTemplate($annotation->value);
                }

                if ($annotation->listAjaxTemplate) {
                    $controller->setListAjaxTemplate($annotation->listAjaxTemplate);
                }
            }

            if ($annotation instanceof FilterFields && is_array($annotation->value)) {
                $controller->setFilterFields($annotation->value);
            }

            if ($annotation instanceof IncludeJavascript && $annotation->value) {
                $controller->includeJavascript($annotation->value, (array) $annotation->includeRoute);
            }

            if ($annotation instanceof ShowFields && $annotation->value) {
                $controller->setShowFields($annotation->value);
            }

            if ($annotation instanceof GridFields && $annotation->isValid()) {
                $controller->setGridFields($annotation->value);
            }

            if ($annotation instanceof NormalizeFilter) {
                $controller->normalizeFilter();
            }
        }
    }
}
