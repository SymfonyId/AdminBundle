<?php

namespace Symfonian\Indonesia\AdminBundle\EventListener;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Event\FilterFormEvent;
use Symfonian\Indonesia\AdminBundle\User\User;

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
