<?php
namespace Symfonian\Indonesia\AdminBundle\EventListener;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Doctrine\Common\Annotations\Reader;

use Symfonian\Indonesia\AdminBundle\Controller\CrudController;

use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
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

final class AnnotationListener
{
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        if (! is_array($controller)) {

            return;
        }

        $controller = $controller[0];

        if (! $controller instanceof CrudController) {

            return;
        }

        $reflectionObject = new \ReflectionObject($controller);

        foreach ($this->reader->getClassAnnotations($reflectionObject) as $annotation) {
            if ($annotation instanceof Crud) {
                if ($annotation->entityClass) {
                    $controller->setEntityClass($annotation->entityClass);
                }

                if ($annotation->formClass) {
                    $controller->setFormClass($annotation->formClass);
                }

                if ($annotation->pageTitle) {
                    $controller->setPageTitle($annotation->pageTitle);
                }

                if ($annotation->pageDescription) {
                    $controller->setPageDescription($annotation->pageDescription);
                }

                if ($annotation->newActionTemplate) {
                    $controller->setNewActionTemplate($annotation->newActionTemplate);
                }

                if ($annotation->editActionTemplate) {
                    $controller->setEditActionTemplate($annotation->editActionTemplate);
                }

                if ($annotation->showActionTemplate) {
                    $controller->setShowActioinTemplate($annotation->showActionTemplate);
                }

                if ($annotation->listActionTemplate) {
                    $controller->setListActionTemplate($annotation->listActionTemplate);
                }

                if ('true' === strtolower($annotation->normalizeFilter)) {
                    $controller->normalizeFilter();
                }

                if (is_array($annotation->showFields)) {
                    $controller->setShowFields($annotation->showFields);
                }

                if (is_array($annotation->gridFields)) {
                    $controller->setGridFields($annotation->gridFields);
                }
            }

            if ($annotation instanceof EntityClass) {
                if ($annotation->value) {
                    $controller->setEntityClass($annotation->value);
                }
            }

            if ($annotation instanceof FormClass) {
                if ($annotation->value) {
                    $controller->setFormClass($annotation->value);
                }
            }

            if ($annotation instanceof PageTitle) {
                if ($annotation->value) {
                    $controller->setPageTitle($annotation->value);
                }
            }

            if ($annotation instanceof PageDescription) {
                if ($annotation->value) {
                    $controller->setPageDescription($annotation->value);
                }
            }

            if ($annotation instanceof NewActionTemplate) {
                if ($annotation->value) {
                    $controller->setNewActionTemplate($annotation->value);
                }
            }

            if ($annotation instanceof EditActionTemplate) {
                if ($annotation->value) {
                    $controller->setEditActionTemplate($annotation->value);
                }
            }

            if ($annotation instanceof ShowActionTemplate) {
                if ($annotation->value) {
                    $controller->setShowActioinTemplate($annotation->value);
                }
            }

            if ($annotation instanceof ListActionTemplate) {
                if ($annotation->value) {
                    $controller->setListActionTemplate($annotation->value);
                }
            }

            if ($annotation instanceof ShowFields) {
                if ($annotation->isValid()) {
                    $controller->setShowFields($annotation->value);
                }
            }

            if ($annotation instanceof GridFields) {
                if ($annotation->isValid()) {
                    $controller->setGridFields($annotation->value);
                }
            }

            if ($annotation instanceof NormalizeFilter) {
                $controller->normalizeFilter();
            }
        }
    }
}
