<?php

namespace Symfonian\Indonesia\AdminBundle\Controller;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 *
 */

use FOS\UserBundle\Model\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfonian\Indonesia\AdminBundle\Event\FilterEntityEvent;
use Symfonian\Indonesia\AdminBundle\Handler\ConfigurationHandler;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminEvents as Event;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileController extends Controller
{
    protected $viewParams = array();

    /**
     * @Route("/profile/")
     * @Method({"GET"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function profileAction(Request $request)
    {
        $entity = $this->getUser();
        $data = array();

        /** @var ConfigurationHandler $configuration */
        $configuration = $this->container->get('symfonian_id.admin.handler.configuration');

        foreach ($configuration->getShowFields() as $key => $property) {
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
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changePasswordAction(Request $request)
    {
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException($translator->trans('message.access_denied', array(), $translationDomain));
        }

        /** @var ConfigurationHandler $configuration */
        $configuration = $this->container->get('symfonian_id.admin.handler.configuration');

        $form = $configuration->getForm($user);
        $form->handleRequest($request);

        $this->viewParams['page_title'] = $translator->trans('page.change_password.title', array(), $translationDomain);
        $this->viewParams['page_description'] = $translator->trans('page.change_password.description', array(), $translationDomain);
        $this->viewParams['form'] = $form->createView();
        $this->viewParams['form_theme'] = $this->container->getParameter('symfonian_id.admin.themes.form_theme');
        $this->viewParams['menu'] = $this->container->getParameter('symfonian_id.admin.menu');

        if ($request->isMethod('POST')) {
            if (!$form->isValid()) {
                $this->viewParams['errors'] = true;
            } elseif ($form->isValid()) {
                /** @var \Symfony\Component\Security\Core\Encoder\EncoderFactory $encoderFactory */
                $encoderFactory = $this->container->get('security.encoder_factory');

                /** @var \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface $encoder */
                $encoder = $encoderFactory->getEncoder($user);
                $password = $encoder->encodePassword($form->get('current_password')->getData(), $user->getSalt());

                if ($password !== $user->getPassword()) {
                    $this->viewParams['current_password_invalid'] = true;

                    return $this->render('SymfonianIndonesiaAdminBundle:Index:change_password.html.twig', $this->viewParams);
                }

                $userManager = $this->container->get('fos_user.user_manager');
                $entity = $form->getData();

                /** @var \Doctrine\ORM\EntityManager $entityManager */
                $entityManager = $this->container->get('doctrine.orm.entity_manager');
                $dispatcher = $this->container->get('event_dispatcher');

                $event = new FilterEntityEvent();
                $event->setEntityManager($entityManager);
                $event->setEntity($entity);

                $userManager->updateUser($entity);
                $dispatcher->dispatch(Event::POST_SAVE, $event);

                $this->viewParams['success'] = $translator->trans('message.data_saved', array(), $translationDomain);
            }
        }

        return $this->render($this->container->getParameter('symfonian_id.admin.themes.change_password'), $this->viewParams);
    }
}
