<?php

namespace Symfonian\Indonesia\AdminBundle\EventListener;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Event\FilterEntityEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;
use FOS\UserBundle\Model\UserInterface;

final class DeleteUserListener
{
    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container, TokenStorageInterface $tokenStorage, TranslatorInterface $translator)
    {
        $token = $tokenStorage->getToken();
        if ($token) {
            $this->user = $token->getUser();
        }
        $this->translator = $translator;
        $this->container = $container;
    }

    public function onDeleteUser(FilterEntityEvent $event)
    {
        $entity = $event->getEntity();

        if (!$entity instanceof UserInterface) {
            return;
        }

        if ($this->user->getUsername() === $entity->getUsername()) {
            $response = new JsonResponse(array(
                'status' => false,
                'message' => $this->translator->trans('message.cant_delete_your_self', array(), $this->container->getParameter('symfonian_id.admin.translation_domain')),
            ));

            $event->setResponse($response);
        }
    }
}
