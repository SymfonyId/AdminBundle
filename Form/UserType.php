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

use Symfonian\Indonesia\AdminBundle\EventListener\RemovePasswordFieldListener;
use Symfonian\Indonesia\AdminBundle\Form\DataTransformer\RoleToArrayTransformer;
use Symfonian\Indonesia\AdminBundle\Form\DataTransformer\StringToFileTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class UserType extends AbstractType
{
    /**
     * @var string
     */
    private $userClass;

    /**
     * @var string
     */
    private $translationDomain;

    /**
     * @var array
     */
    private $roleHierarchy;

    /**
     * @var string
     */
    private $uploadDir;

    /**
     * @param string $userClass
     * @param string $translationDomain
     * @param array  $roleHierarchy
     * @param string $uploadDir
     */
    public function __construct($userClass, $translationDomain, array $roleHierarchy, $uploadDir)
    {
        $this->userClass = $userClass;
        $this->translationDomain = $translationDomain;
        $this->roleHierarchy = array_keys($roleHierarchy);
        $this->uploadDir = $uploadDir;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
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
            ->add($builder->create('file', FileType::class, array(
                'label' => 'form.label.avatar',
                'required' => false,
                'attr' => array(
                    'accept' => 'image/*',
                    'class' => 'form-control',
                ),
            ))->addModelTransformer(new StringToFileTransformer($this->uploadDir)))
        ;

        $builder->addEventSubscriber(new RemovePasswordFieldListener());
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->userClass,
            'translation_domain' => $this->translationDomain,
            'validation_groups' => array('Default'),
            'intention' => 'user',
        ));
    }

    /**
     * @return array
     */
    private function buildRoleList()
    {
        $roleList = array();
        foreach ($this->roleHierarchy as $key => $value) {
            $roleList[$value] = $value;
        }

        return $roleList;
    }
}
