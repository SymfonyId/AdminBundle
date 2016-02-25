<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfonian\Indonesia\AdminBundle\EventListener;

use Symfonian\Indonesia\AdminBundle\Event\FilterEntityEvent;
use Symfonian\Indonesia\AdminBundle\EventListener\DeleteUserListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class DeleteUserListenerTest extends \PHPUnit_Framework_TestCase
{
    private $event;

    private $tokenStorage;

    private $translation;

    private $response;

    public function setUp()
    {
        $user = $this->getMockBuilder(UserInterface::class)->disableOriginalConstructor()->getMock();
        $user->expects($this->any())->method('getUsername')->willReturn('ADMIN');
        $token = $this->getMockBuilder(TokenInterface::class)->disableOriginalConstructor()->getMock();
        $token->expects($this->any())->method('getUser')->willReturn($user);
        $this->tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)->disableOriginalConstructor()->getMock();
        $this->tokenStorage->expects($this->any())->method('getToken')->willReturn($token);

        $this->translation = $this->getMockBuilder(TranslatorInterface::class)->disableOriginalConstructor()->getMock();
        $this->translation->expects($this->any())->method('trans')->willReturn('can not delete');

        $this->response = $this->getMockBuilder(JsonResponse::class)->disableOriginalConstructor()->getMock();

        $this->event = $this->getMockBuilder(FilterEntityEvent::class)->disableOriginalConstructor()->getMock();
        $this->event->expects($this->any())->method('getEntity')->willReturn($user);
        $this->event->expects($this->any())->method('setResponse')->with($this->isInstanceOf(JsonResponse::class));
        $this->event->expects($this->any())->method('getResponse')->willReturn($this->response);
    }

    public function testCanNotDeleteItSelf()
    {
        $listener = new DeleteUserListener($this->tokenStorage, $this->translation, 'A');
        $listener->onDeleteUser($this->event);
        $this->assertInstanceOf(get_class($this->response), $this->event->getResponse());
    }

    public function tearDown()
    {
        unset($this->event);
        unset($this->tokenStorage);
        unset($this->translation);
    }
}
