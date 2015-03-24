<?php
namespace Symfonian\Indonesia\AdminBundle\Form\Ajax;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfony\Component\Routing\RouterInterface;

class XEntityType extends XChoiceType
{
    public function __construct(RouterInterface $router)
    {
        parent::__construct($router);
    }

    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'xentity';
    }
}
