<?php
namespace Symfonian\Indonesia\AdminBundle\EventListener;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfonian\Indonesia\AdminBundle\Event\GetEntityResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;
use FOS\UserBundle\Model\UserInterface;

class UpdateUserListener
{
    public function onPreSaveUser(GetEntityResponseEvent $event)
    {
        $entity = $event->getEntity();

        if (! $entity instanceof UserInterface) {
            return ;
        }

        if ($entity->getId() || $entity->isEnabled()) {
            return ;
        }

        if (! $this->container->getParameter('symfonian_id.admin.security.auto_enable')) {
            return ;
        }

        $entity->setEnabled(true);
    }
}
