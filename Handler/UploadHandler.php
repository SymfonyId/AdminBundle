<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Handler;

use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface;
use Symfonian\Indonesia\CoreBundle\Toolkit\Util\StringUtil\CamelCasizer;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class UploadHandler
{
    /**
     * @var string
     */
    private $dirPath;

    /**
     * @var array
     */
    private $fields = array();

    /**
     * @var array
     */
    private $targetFields = array();

    /**
     * @param string $dirPath
     */
    public function setUploadDir($dirPath)
    {
        $this->dirPath = $dirPath;
    }

    /**
     * @param array $fields
     * @param array $targetFields
     */
    public function setFields(array $fields, array $targetFields)
    {
        if (count($fields) !== count($targetFields)) {
            throw new \InvalidArgumentException('Fields dan Target Fields harus sama jumlahnya.');
        }
        $this->fields = array_values($fields);
        $this->targetFields = array_values($targetFields);
    }

    /**
     * @return bool
     */
    public function isUploadable()
    {
        if (empty($this->fields)) {
            return false;
        }

        return true;
    }

    /**
     * @param EntityInterface $entity
     */
    public function upload(EntityInterface $entity)
    {
        if (!is_dir($this->dirPath)) {
            mkdir($this->dirPath);
        }

        $file = null;
        foreach ($this->fields as $key => $field) {
            $getter = CamelCasizer::underScoretToCamelCase('get_'.$field);
            if (method_exists($entity, $getter)) {
                /** @var UploadedFile $file */
                $file = call_user_func_array(array($entity, $getter), array());
            }

            if ($file instanceof UploadedFile) {
                $fileName = sha1(uniqid('SIAB_', true)).'.'.$file->getClientOriginalExtension();

                if (!$file->isExecutable()) {
                    $file->move($this->dirPath, $fileName);
                }

                $setter = CamelCasizer::underScoretToCamelCase('set_'.$this->targetFields[$key]);
                if (method_exists($entity, $setter)) {
                    call_user_func_array(array($entity, $setter), array($fileName));
                }
            }
        }
    }
}
