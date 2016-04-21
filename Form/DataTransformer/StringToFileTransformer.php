<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class StringToFileTransformer implements DataTransformerInterface
{
    /**
     * @var array
     */
    private $uploadDir;

    /**
     * @param array $uploadDir
     */
    public function __construct(array $uploadDir)
    {
        $this->uploadDir = $uploadDir;
    }

    /**
     * @param string $file
     *
     * @return string|File
     */
    public function reverseTransform($file)
    {
        if (!$file instanceof File) {
            return $file;
        }

        return $file;
    }

    /**
     * @param string $filename
     *
     * @return string|File
     */
    public function transform($filename)
    {
        if (!$filename) {
            return $filename;
        }

        return new File($this->uploadDir['server_path'].'/'.$filename);
    }
}
