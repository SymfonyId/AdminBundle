<?php
namespace Symfonian\Indonesia\AdminBundle\Model;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

interface UserInterface extends EntityInterface
{
    public function getAvatar();

    public function getFullName();

    public function getRole();
}
