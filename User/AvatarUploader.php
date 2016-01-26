<?php

namespace Symfonian\Indonesia\AdminBundle\User;

use Symfonian\Indonesia\AdminBundle\Annotation\Util;
use Symfonian\Indonesia\AdminBundle\Configuration\ConfigurationFactory;
use Symfonian\Indonesia\AdminBundle\Event\FilterEntityEvent;
use Symfonian\Indonesia\AdminBundle\Handler\UploadHandler;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin.
 */
class AvatarUploader
{
    protected $configurationFactory;

    protected $uploadHandler;

    protected $uploadDir;

    public function __construct(ConfigurationFactory $configurationFactory, UploadHandler $uploadHandler, $uploadDir)
    {
        $this->configurationFactory = $configurationFactory;
        $this->uploadHandler = $uploadHandler;
        $this->uploadDir = $uploadDir;
    }

    public function setUploadField()
    {
        /** @var Util $util */
        $util = $this->configurationFactory->getConfiguration('util');

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
