<?php
namespace Symfonian\Indonesia\AdminBundle\Form\Ajax;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfony\Component\Routing\RouterInterface;

class XTextType extends JQueryAjaxType
{
    public function __construct(RouterInterface $router)
    {
        parent::__construct($router);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'event' => 'onchange',
        ));
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'xtext';
    }
}
