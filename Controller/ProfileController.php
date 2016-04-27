<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Controller;

use FOS\UserBundle\Model\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\AdminBundle\Configuration\Configurator;
use Symfonian\Indonesia\AdminBundle\Event\FilterEntityEvent;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants as Constants;
use Symfonian\Indonesia\AdminBundle\Util\MethodInvoker;
use Symfonian\Indonesia\AdminBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ProfileController extends Controller
{
    private $viewParams = array();

    /**
     * @Route("/profile/")
     * @Method({"GET"})
     */
    public function profileAction(Request $request)
    {
        $entity = $this->getUser();
        $data = array();

        /** @var Configurator $configuration */
        $configuration = $this->getConfigurator($this->getClassName());
        /** @var Crud $crud */
        $crud = $configuration->getConfiguration(Crud::class);

        foreach ($crud->getShowFields() as $key => $property) {
            if ($value = MethodInvoker::invokeGet($entity, $property)) {
                array_push($data, array(
                    'name' => $property,
                    'value' => $value,
                ));
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
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException($translator->trans('message.access_denied', array(), $translationDomain));
        }

        /** @var Configurator $configuration */
        $configuration = $this->getConfigurator($this->getClassName());
        /** @var Crud $crud */
        $crud = $configuration->getConfiguration(Crud::class);

        $form = $crud->getForm($user);
        $form->handleRequest($request);

        /** @var View $view */
        $view = $this->get('symfonian_id.admin.view.view');
        $view->setParam('page_title', $translator->trans('page.change_password.title', array(), $translationDomain));
        $view->setParam('page_description', $translator->trans('page.change_password.description', array(), $translationDomain));
        $view->setParam('form', $form->createView());
        $view->setParam('form_theme', $this->container->getParameter('symfonian_id.admin.themes.form_theme'));
        $view->setParam('menu', $this->container->getParameter('symfonian_id.admin.menu'));

        if ($request->isMethod('POST')) {
            if (!$form->isValid()) {
                $view->setParam('errors', true);
            } elseif ($form->isValid()) {
                /** @var \Symfony\Component\Security\Core\Encoder\EncoderFactory $encoderFactory */
                $encoderFactory = $this->container->get('security.encoder_factory');

                /** @var \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface $encoder */
                $encoder = $encoderFactory->getEncoder($user);
                $password = $encoder->encodePassword($form->get('current_password')->getData(), $user->getSalt());

                if ($password !== $user->getPassword()) {
                    $view->setParam('current_password_invalid', true);

                    return $this->render('SymfonianIndonesiaAdminBundle:Index:change_password.html.twig', $this->viewParams);
                }

                $userManager = $this->container->get('fos_user.user_manager');
                $entity = $form->getData();

                /** @var \Doctrine\ORM\EntityManager $entityManager */
                $entityManager = $this->container->get('doctrine.orm.entity_manager');

                $event = new FilterEntityEvent();
                $event->setEntityManager($entityManager);
                $event->setEntity($entity);

                $userManager->updateUser($entity);
                $this->fireEvent(Constants::POST_SAVE, $event);

                $view->setParam('success', $translator->trans('message.data_saved', array(), $translationDomain));
            }
        }

        return $this->render($this->container->getParameter('symfonian_id.admin.themes.change_password'), $view->getParams());
    }

    /**
     * @return string
     */
    protected function getClassName()
    {
        return __CLASS__;
    }
}
