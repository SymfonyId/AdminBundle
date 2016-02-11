<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\EventListener;

use Symfonian\Indonesia\AdminBundle\Event\FilterFormEvent;
use Symfonian\Indonesia\AdminBundle\User\User;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class RemovePasswordFieldListener
{
    /**
     * @param FilterFormEvent $event
     */
    public function onPreCreateForm(FilterFormEvent $event)
    {
        $formData = $event->getData();
        if (!$formData instanceof User) {
            return;
        }

        /** @var \Symfony\Component\Form\FormInterface $form */
        $form = $event->getForm();
        if (!$formData->getId() || 'user' !== $form->getName()) {
            return;
        }

        $form->remove('plainPassword');
    }
}
