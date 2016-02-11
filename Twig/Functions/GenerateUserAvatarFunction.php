<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Twig\Functions;

use Symfonian\Indonesia\AdminBundle\User\User;
use Twig_Extension;
use Twig_SimpleFunction;

class GenerateUserAvatarFunction extends Twig_Extension
{
    private $uploadDir;

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
            return 'bundles/symfonianindonesiaadmin/img/apple-icon-114x114.png';
        }
    }

    public function getName()
    {
        return 'generate_avatar';
    }
}
