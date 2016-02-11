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

use FOS\UserBundle\Model\UserInterface;
use Symfonian\Indonesia\AdminBundle\Event\FilterEntityEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
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
