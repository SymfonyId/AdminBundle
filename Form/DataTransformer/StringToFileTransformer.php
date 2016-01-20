<?php

namespace Symfonian\Indonesia\AdminBundle\Form\DataTransformer;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;

class StringToFileTransformer implements DataTransformerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function reverseTransform($file)
    {
        if (!$file instanceof File) {
            return $file;
        }

        return $file->getFilename();
    }

    public function transform($filename)
    {
        $uploadDir = $this->container->getParameter('symfonian_id.admin.upload_dir');

        return new File($uploadDir['server_path'].'/'.$filename);
    }
}