<?php

namespace Symfonian\Indonesia\AdminBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;

class CacheListener
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage
     */
    private $tokenStorage;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->tokenStorage = $container->get('security.token_storage');
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$this->container->getParameter('symfonian_id.admin.use_micro_cache')) {
            return;
        }

        $request = $event->getRequest();
        if ($request->isMethod('GET')) {
            $response = new Response();

            if ($response->isNotModified($request)) {
                $event->setResponse($response);
            }
        }
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$this->container->getParameter('symfonian_id.admin.use_micro_cache')) {
            return;
        }

        if ($this->tokenStorage->getToken()->getUser()) {
            return;
        }

        if ($event->getRequest()->isMethod('GET')) {
            $response = $event->getResponse();

            $response->setPublic();
            $response->setMaxAge(3);
            $response->setSharedMaxAge(3);
            $response->setETag(md5($response->getContent()));
        }
    }
}