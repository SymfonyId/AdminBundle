<?php
namespace Symfonian\Indonesia\AdminBundle\Controller;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 *
 */

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\UserBundle\Model\UserInterface;

use Symfonian\Indonesia\AdminBundle\Event\PostSaveEvent;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminEvents as Event;

class ProfileController extends Controller
{
    /**
     * @Route("/profile/")
     * @Method({"GET"})
     */
    public function profileAction(Request $request)
    {
        $entity = $this->getUser();
        $data = array();

        foreach ($this->showFields() as $key => $property) {
            $method = 'get'.ucfirst($property);

            if (method_exists($entity, $method)) {
                array_push($data, array(
                    'name' => $property,
                    'value' => call_user_func_array(array($entity, $method), array()),
                ));
            } else {
                $method = 'is'.ucfirst($property);

                if (method_exists($entity, $method)) {
                    array_push($data, array(
                        'name' => $property,
                        'value' => call_user_func_array(array($entity, $method), array()),
                    ));
                }
            }
        }

        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        return $this->render($this->container->getParameter('symfonian_id.admin.themes.profile'), array(
            'data' => $data,
            'menu' => $this->container->getParameter('symfonian_id.admin.menu'),
            'page_title' => $translator->trans('page.profile.title', array(), $translationDomain),
            'page_description' => $translator->trans('page.profile.description', array(), $translationDomain),
        ));
    }

    /**
     * @Route("/change_password/")
     * @Method({"GET", "POST"})
     */
    public function changePasswordAction(Request $request)
    {
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        $user = $this->getUser();
        if (! is_object($user) || ! $user instanceof UserInterface) {

            throw new AccessDeniedException($translator->trans('message.access_denied', array(), $translationDomain));
        }

        $form = $this->getForm($user);
        $form->handleRequest($request);

        $this->outputParameter['page_title'] = $translator->trans('page.change_password.title', array(), $translationDomain);
        $this->outputParameter['page_description'] = $translator->trans('page.change_password.description', array(), $translationDomain);
        $this->outputParameter['form'] = $form->createView();
        $this->outputParameter['form_theme'] = $this->container->getParameter('symfonian_id.admin.themes.form_theme');
        $this->outputParameter['menu'] = $this->container->getParameter('symfonian_id.admin.menu');

        if ($request->isMethod('POST')) {
            if (! $form->isValid()) {

                $this->outputParameter['errors'] = true;
            } else if ($form->isValid()) {
                $encoderFactory = $this->container->get('security.encoder_factory');
                $encoder = $encoderFactory->getEncoder($user);
                $password = $encoder->encodePassword($form->get('current_password')->getData(), $user->getSalt());

                if ($password !== $user->getPassword()) {
                    $this->outputParameter['current_password_invalid'] = true;

                    return $this->render('SymfonianIndonesiaAdminBundle:Index:change_password.html.twig', $this->outputParameter);
                }

                $userManager = $this->container->get('fos_user.user_manager');

                $entity = $form->getData();
                $entityManager = $this->getDoctrine()->getManager();
                $dispatcher = $this->container->get('event_dispatcher');

                $event = new PostSaveEvent();
                $event->setEntityMeneger($entityManager);
                $event->setEntity($entity);

                $userManager->updateUser($entity);

                $dispatcher->dispatch(Event::POST_SAVE_EVENT, $event);

                $this->outputParameter['success'] = $translator->trans('message.data_saved', array(), $translationDomain);
            }
        }

        return $this->render($this->container->getParameter('symfonian_id.admin.themes.change_password'), $this->outputParameter);
    }
}