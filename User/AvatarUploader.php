<?php

namespace Symfonian\Indonesia\AdminBundle\User;

use Symfonian\Indonesia\AdminBundle\Annotation\Util;
use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\Event\FilterEntityEvent;
use Symfonian\Indonesia\AdminBundle\Handler\UploadHandler;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin.
 */
class AvatarUploader
{
    protected $configuration;

    protected $uploadHandler;

    protected $uploadDir;

    public function __construct(Configurator $configurator, UploadHandler $uploadHandler, $uploadDir)
    {
        $this->configuration = $configurator;
        $this->uploadHandler = $uploadHandler;
        $this->uploadDir = $uploadDir;
    }

    public function setUploadField()
    {
        /** @var Util $util */
        $util = $this->configuration->getConfiguration('util');

        $this->uploadHandler->setFields(array($util->getUploadableField()));
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
