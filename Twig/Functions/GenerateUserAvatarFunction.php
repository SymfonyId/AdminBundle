<?php

namespace Symfonian\Indonesia\AdminBundle\Twig\Functions;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin.
 */

use Symfonian\Indonesia\AdminBundle\User\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\HttpUtils;
use Twig_Extension;
use Twig_SimpleFunction;

class GenerateUserAvatarFunction extends Twig_Extension
{
    /**
     * @var string
     */
    protected $uploadDir;

    /**
     * @param string $uploadDir
     */
    public function __construct($uploadDir)
    {
        $this->uploadDir = $uploadDir;
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
            return $this->uploadDir['web_path'].$user->getAvatar();
        } else {
            $http = new HttpUtils();

            return $http->generateUri(new Request(), 'bundles/symfonianindonesiaadmin/img/apple-icon-114x114.png');
        }
    }

    public function getName()
    {
        return 'generate_avatar';
    }
}
