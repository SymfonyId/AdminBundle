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

final class DeleteUserListener
{
    /**
     * @var FOS\UserBundle\Model\UserInterface
     */
    protected $user;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container, TokenStorageInterface $tokenStorage, TranslatorInterface $translator)
    {
        $this->user = $tokenStorage->getToken()->getUser();
        $this->translator = $translator;
        $this->container = $container;
    }

    public function onDeleteUser(GetEntityResponseEvent $event)
    {
        $entity = $event->getEntity();

        if (! $entity instanceof UserInterface) {
            return ;
        }

        if ($this->user->getUsername() === $entity->getUsername()) {
            $response = new JsonResponse(array(
                'status' => false,
                'message' => $this->translator->trans('message.cant_delete_your_self', array(), $this->container->getParameter('symfonian_id.admin.translation_domain'))
            ));

            $event->setResponse($response);
        }
    }
}