<?php

namespace Symfonian\Indonesia\AdminBundle\Form\DataTransformer;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;

class StringToFileTransformer implements DataTransformerInterface
{
    /**
     * @var array
     */
    protected $uploadDir;

    public function __construct(array $uploadDir)
    {
        $this->uploadDir = $uploadDir;
    }

    public function reverseTransform($file)
    {
        if (!$file instanceof File) {
            return $file;
        }

        return $file;
    }

    public function transform($filename)
    {
        if (!$filename) {
            return $filename;
        }

        return new File($this->uploadDir['server_path'].'/'.$filename);
    }
}
