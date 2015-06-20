<?php
namespace Symfonian\Indonesia\AdminBundle\Form;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfonian\Indonesia\AdminBundle\Controller\Controller;

class GenericFormType extends AbstractType
{
    const FORM_NAME = 'generic';

    /**
     * @var Controller
     */
    protected $controller;

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(Controller $controller, ContainerInterface $container)
    {
        $this->controller = $controller;
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->controller->entityProperties() as $key => $value) {

            if ('id' === $value) {

                continue;
            }

            $builder->add($value, null, array(
                'attr' => array(
                    'class' => 'form-control',
                )
            ));
        }

        $builder->add('save', 'submit', array(
            'label' => 'action.submit',
            'attr' => array(
                'class' => 'btn btn-primary',
            )
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->controller->getEntityClass(),
            'translation_domain' => $this->container->getParameter('symfonian_id.admin.translation_domain'),
            'intention'  => self::FORM_NAME,
        ));
    }

    public function getName()
    {
        return self::FORM_NAME;
    }
}
