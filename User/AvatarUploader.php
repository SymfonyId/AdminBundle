<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\User;

use Symfonian\Indonesia\AdminBundle\Annotation\Util;
use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\Event\FilterEntityEvent;
use Symfonian\Indonesia\AdminBundle\Handler\UploadHandler;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class AvatarUploader
{
    /**
     * @var Configurator
     */
    private $configuration;

    /**
     * @var UploadHandler
     */
    private $uploadHandler;

    private $uploadDir;

    public function __construct(Configurator $configurator, UploadHandler $uploadHandler, $uploadDir)
    {
        $this->configuration = $configurator;
        $this->uploadHandler = $uploadHandler;
        $this->uploadDir = $uploadDir;
    }

    public function setUploadField()
    {
        /** @var Util $util */
        $util = $this->configuration->getConfiguration(Util::class);
        $this->uploadHandler->setFields(array($util->getUploadableField()), array($util->getTargetField()));
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
