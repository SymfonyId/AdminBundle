<?php

namespace Symfonian\Indonesia\AdminBundle\EventListener;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use FOS\UserBundle\Model\UserInterface;
use Symfonian\Indonesia\AdminBundle\Event\FilterEntityEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Translation\TranslatorInterface;

class DeleteUserListener
{
    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    private $translationDomain;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param TranslatorInterface   $translator
     * @param string                $translationDomain
     */
    public function __construct(TokenStorageInterface $tokenStorage, TranslatorInterface $translator, $translationDomain)
    {
        $token = $tokenStorage->getToken();
        if ($token) {
            $this->user = $token->getUser();
        }
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
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
                'message' => $this->translator->trans('message.cant_delete_your_self', array(), $this->translationDomain),
            ));

            $event->setResponse($response);
        }
    }
}
