<?php

namespace Symfonian\Indonesia\AdminBundle\User;

use Symfonian\Indonesia\AdminBundle\Event\FilterEntityEvent;
use Symfonian\Indonesia\AdminBundle\Handler\UploadHandler;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin.
 */
class AvatarUploader
{
    private $uploadHandler;

    private $uploadDir;

    public function __construct(UploadHandler $uploadHandler, $uploadDir)
    {
        $this->uploadHandler = $uploadHandler;
        $this->uploadDir = $uploadDir;
    }

    public function onPreSave(FilterEntityEvent $event)
    {
        $entity = $event->getEntity();

        if ($this->uploadHandler->isUploadable() && $entity instanceof User) {
            $this->uploadHandler->setUploadDir($this->uploadDir['server_path']);
            $this->uploadHandler->upload($entity);
        }
    }
}
