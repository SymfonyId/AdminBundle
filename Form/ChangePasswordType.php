<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ChangePasswordType extends AbstractType
{
    private $userClass;
    private $translationDomain;

    /**
     * @param string $userClass
     * @param string $translationDomain
     */
    public function __construct($userClass, $translationDomain)
    {
        $this->userClass = $userClass;
        $this->translationDomain = $translationDomain;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('current_password', PasswordType::class, array(
                'mapped' => false,
                'label' => 'form.label.current_password',
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'label' => 'form.label.new_password',
                'type' => PasswordType::class,
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
            'data_class' => $this->userClass,
            'translation_domain' => $this->translationDomain,
            'intention' => 'change_password',
        ));
    }
}
