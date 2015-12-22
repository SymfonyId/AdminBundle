<?php

namespace Symfonian\Indonesia\AdminBundle\Form;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Form\DataTransformer\RoleToArrayTransformer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
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
            ->add('fullName', TextType::class, array(
                'label' => 'form.label.fullname',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('username', TextType::class, array(
                'label' => 'form.label.username',
                'attr' => array(
                    'class' => 'form-control username',
                ),
            ))
            ->add($builder->create('roles', ChoiceType::class, array(
                    'label' => 'form.label.role',
                    'choices' => $this->buildRoleList(),
                    'placeholder' => 'message.select_empty',
                    'attr' => array(
                        'class' => 'form-control',
                    ),
            ))->addModelTransformer(new RoleToArrayTransformer()))
            ->add('email', EmailType::class, array(
                'label' => 'form.label.email',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
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
            ->add('save', SubmitType::class, array(
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
            'validation_groups' => array('Default'),
            'intention' => $this->getName(),
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
        return 'user';
    }
}
