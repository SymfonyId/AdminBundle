<?php

namespace Symfonian\Indonesia\AdminBundle\Form;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Form\DataTransformer\RoleToArrayTransformer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    const FORM_NAME = 'user';

    protected $container;

    protected $roleHierarchy;

    public function __construct(ContainerInterface $container, array $roleHierarchy)
    {
        $this->container = $container;
        $this->roleHierarchy = array_keys($roleHierarchy);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', 'text', array(
                'label' => 'form.label.fullname',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('username', 'text', array(
                'label' => 'form.label.username',
                'attr' => array(
                    'class' => 'form-control username',
                ),
            ))
            ->add($builder->create('roles', 'choice', array(
                    'label' => 'form.label.role',
                    'choices' => $this->buildRoleList(),
                    'empty_value' => 'message.select_empty',
                    'attr' => array(
                        'class' => 'form-control',
                    ),
            ))->addModelTransformer(new RoleToArrayTransformer()))
            ->add('email', 'email', array(
                'label' => 'form.label.email',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'message.password_must_match',
                'options' => array(
                    'attr' => array(
                        'class' => 'form-control',
                    ),
                ),
                'required' => true,
                'first_options' => array(
                    'label' => 'form.label.password',
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
            'validation_groups' => array('Registration', 'Default'),
            'intention' => self::FORM_NAME,
        ));
    }

    protected function buildRoleList()
    {
        $roleList = array();
        foreach ($this->roleHierarchy as $key => $value) {
            $roleList[$value] = $value;
        }

        return $roleList;
    }

    public function getName()
    {
        return self::FORM_NAME;
    }
}
