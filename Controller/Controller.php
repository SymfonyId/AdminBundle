<?php
namespace Symfonian\Indonesia\AdminBundle\Controller;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfonian\Indonesia\AdminBundle\Form\GenericFormType;

abstract class Controller extends BaseController
{
    protected $pageTitle = 'SymfonianIndonesiaAdminBundle';

    protected $pageDescription = 'Provide Admin Generator with KISS Principle';

    protected $showFields = array();

    protected $entityClass;

    protected $formClass;

    public function entityProperties()
    {
        $fields = array();
        $reflection = new \ReflectionClass($this->entityClass);
        $reflection->getProperties();

        foreach ($reflection->getProperties() as $key => $property) {
            $fields[$key] = $property->getName();
        }

        return $fields;
    }

    protected function showFields()
    {
        if (! empty($this->showFields)) {

            return $this->showFields;
        }

        return $this->entityProperties();
    }

    /**
     * @param array $fields
     * @return \Symfonian\Indonesia\AdminBundle\Controller\AbstractController
     */
    public function setShowFields(array $fields)
    {
        $this->showFields = $fields;

        return $this;
    }

    /**
     * @param string $pageTitle
     * @return \Symfonian\Indonesia\AdminBundle\Controller\AbstractController
     */
    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;

        return $this;
    }

    /**
     * @param string $pageDescription
     * @return \Symfonian\Indonesia\AdminBundle\Controller\AbstractController
     */
    public function setPageDescription($pageDescription)
    {
        $this->pageDescription = $pageDescription;

        return $this;
    }

    protected function getForm($data = null)
    {
        try {
            $formObject = $this->container->get($this->formClass);
        } catch (\Exception $ex) {
            if ($this->formClass) {
                $formObject = new $this->formClass();
            } else {
                $formObject = new GenericFormType($this, $this->container);
            }
        }

        $form = $this->createForm($formObject);
        $form->setData($data);

        return $form;
    }

    /**
     * @param string $formClass
     * @return \Symfonian\Indonesia\AdminBundle\Controller\AbstractController
     */
    public function setFormClass($formClass)
    {
        $this->formClass = $formClass;

        return $this;
    }

    /**
     * @param string $entityClass
     * @return \Symfonian\Indonesia\AdminBundle\Controller\CrudController
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }
}
