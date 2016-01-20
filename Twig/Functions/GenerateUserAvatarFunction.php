<?php

namespace Symfonian\Indonesia\AdminBundle\Twig\DateExtension;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin.
 */

use Symfonian\Indonesia\AdminBundle\Security\Model\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_Extension;
use Twig_SimpleFunction;

class GenerateUserAvatarFunction extends Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction('generate_avatar', array($this, 'generateAvatar')),
        );
    }

    public function generateAvatar(User $user)
    {
        if ($user->getAvatar()) {
            $uploadDir = $this->container->getParameter('symfonian_id.admin.upload_dir');

            return $uploadDir['web_path'].$user->getAvatar();
        } else {
            return 'bundles/symfonianindonesiaadmin/img/apple-icon-114x114.png';
        }
    }

    public function getName()
    {
        return 'generate_avatar';
    }
}
