<?php

namespace Symfonian\Indonesia\AdminBundle\Form;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('current_password', 'password', array(
                'mapped' => false,
                'label' => 'form.label.current_password',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('plainPassword', 'repeated', array(
                'label' => 'form.label.new_password',
                'type' => 'password',
                'invalid_message' => 'message.password_must_match',
                'options' => array(
                    'attr' => array(
                        'class' => 'form-control',
                    ),
                ),
                'required' => true,
                'first_options' => array(
                    'label' => 'form.label.new_password',
                ),
                'second_options' => array(
                    'label' => 'form.label.repeat_password',
                ),
            ))
            ->add('save', 'submit', array(
                'label' => 'action.submit',
                'attr' => array(
                    'class' => 'btn btn-primary',
                ),
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->container->getParameter('symfonian_id.admin.security.user_entity'),
            'translation_domain' => $this->container->getParameter('symfonian_id.admin.translation_domain'),
            'intention' => $this->getName(),
        ));
    }

    public function getName()
    {
        return 'change_password';
    }
}
