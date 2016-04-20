<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfonian\Indonesia\AdminBundle\Extractor;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfonian\Indonesia\AdminBundle\Annotation\Page;
use Symfonian\Indonesia\AdminBundle\Annotation\Plugins;
use Symfonian\Indonesia\AdminBundle\Annotation\Util\Upload;
use Symfonian\Indonesia\AdminBundle\Grid\Column;
use Symfonian\Indonesia\AdminBundle\Grid\Filter;

/**
 * @Page(title="title", description="description")
 * @Plugins(fileChooser=true)
 * @Upload(uploadable="file")
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class Stub
{
    /**
     * @Column()
     * @Filter()
     */
    private $stubProperty;

    /**
     * @Route("/", name="stub_action")
     */
    public function stubAction()
    {
    }
}
