<?php

namespace Symfonian\Indonesia\AdminBundle\Handler;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface;
use Symfonian\Indonesia\CoreBundle\Toolkit\Util\StringUtil\CamelCasizer;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadHandler
{
    private $dirPath;

    private $method;

    public function setUpdateDir($dirPath)
    {
        $this->dirPath = $dirPath;
    }

    public function setField($field)
    {
        $this->method = CamelCasizer::underScoretToCamelCase('get_'.$field);
    }

    public function upload(EntityInterface $entity)
    {
        $file = null;
        if (method_exists($entity, $this->method)) {
            /** @var UploadedFile $file */
            $file = call_user_func_array(array($entity, $this->method), array());
        }

        if ($file instanceof UploadedFile) {
            $fileName = $file->getClientOriginalName().'.'.$file->getClientOriginalExtension();

            if (!$file->isExecutable()) {
                $file->move($this->dirPath, $fileName);
            }
        }
    }
}