<?php
namespace Symfonian\Indonesia\AdminBundle\Form\Ajax;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;

class XChoiceType extends JQueryAjaxType
{
    public function __construct(RouterInterface $router)
    {
        parent::__construct($router);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setRequired(array('target'));
        $resolver->setDefaults(array(
            'event' => 'onchange',
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'xchoice';
    }
}
