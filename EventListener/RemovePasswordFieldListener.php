<?php

namespace Symfonian\Indonesia\AdminBundle\EventListener;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Event\FilterResponseEvent;
use Symfonian\Indonesia\AdminBundle\Security\Model\User;

class RemovePasswordFieldListener
{
    public function onPreCreateForm(FilterResponseEvent $event)
    {
        $formData = $event->getFormData();
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