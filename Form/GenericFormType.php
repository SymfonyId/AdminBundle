<?php

namespace Symfonian\Indonesia\AdminBundle\Form;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GenericFormType extends AbstractType
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    protected $entity;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function setEntity($entity)
    {
        if (is_object($entity)) {
            $entity = get_class($entity);
        }

        $this->entity = $entity;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['fields'] as $key => $value) {
            if ('id' === $value) {
                continue;
            }

            $builder->add($value, null, array(
                'attr' => array(
                    'class' => 'form-control',
                ),
            ));
        }

        $builder->add('save', SubmitType::class, array(
            'label' => 'action.submit',
            'attr' => array(
                'class' => 'btn btn-primary',
            ),
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->entity,
            'translation_domain' => $this->container->getParameter('symfonian_id.admin.translation_domain'),
            'intention' => $this->getName(),
        ));

        $resolver->setRequired(array('fields'));
    }

    public function getName()
    {
        return 'generic';
    }
}
