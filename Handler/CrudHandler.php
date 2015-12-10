<?php

namespace Symfonian\Indonesia\AdminBundle\Handler;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Controller\CrudController;
use Symfonian\Indonesia\AdminBundle\Event\FilterEntityEvent;
use Symfonian\Indonesia\AdminBundle\Event\FilterQueryEvent;
use Symfonian\Indonesia\AdminBundle\Event\FilterRequestEvent;
use Symfonian\Indonesia\AdminBundle\Event\FilterResponseEvent;
use Symfonian\Indonesia\AdminBundle\Event\FilterResultEvent;
use Symfonian\Indonesia\AdminBundle\Event\GetEntityEvent;
use Symfonian\Indonesia\AdminBundle\Event\GetFormEvent;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminEvents as Event;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface;
use Symfonian\Indonesia\CoreBundle\Toolkit\Util\StringUtil\CamelCasizer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class CrudHandler
{
    const ACTION_CREATE = 'ACTION_CREATE';

    const ACTION_UPDATE = 'ACTION_UPDATE';

    const ACTION_DELETE = 'ACTION_DELETE';

    const ACTION_READ = 'ACTION_READ';

    const GRID_ACTION_SHOW = 'GRID_ACTION_SHOW';

    const GRID_ACTION_EDIT = 'GRID_ACTION_EDIT';

    const GRID_ACTION_DELETE = 'GRID_ACTION_DELETE';

    const ENTITY_ALIAS = 'e';

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $manager;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $repository;

    protected $class;

    protected $template;

    protected $viewParams = array();

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->manager = $container->get('doctrine.orm.entity_manager');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->container->get('templating')->renderResponse($this->template, $this->viewParams);
    }

    /**
     * @param array $viewParams
     */
    public function setViewParams(array $viewParams)
    {
        $this->viewParams = array_merge($this->viewParams, $viewParams);
    }

    /**
     * @param $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @param $class
     */
    public function setEntity($class)
    {
        $this->repository = $this->manager->getRepository($class);
        $this->class = $this->manager->getClassMetadata($class)->getName();
    }

    /**
     * @param Request    $request
     * @param array      $gridFields
     * @param array      $filterFields
     * @param bool|false $normalizeFilter
     * @param bool|true $formatNumber
     */
    public function viewList(Request $request, array $gridFields, array $filterFields, $normalizeFilter = false, $formatNumber = true)
    {
        $queryBuilder = $this->repository->createQueryBuilder(self::ENTITY_ALIAS);
        $queryBuilder->addOrderBy(sprintf('%s.%s', self::ENTITY_ALIAS, $this->container->getParameter('symfonian_id.admin.identifier')), 'DESC');
        $filter = $normalizeFilter ? strtoupper($request->query->get('filter')) : $request->query->get('filter');

        if ($filter) {
            foreach ($filterFields as $key => $value) {
                $queryBuilder->orWhere(sprintf('%s.%s LIKE ?%d', self::ENTITY_ALIAS, $value, $key));
                $queryBuilder->setParameter($key, strtr('%filter%', array('filter' => $filter)));
            }
        }

        $filterList = new FilterQueryEvent();
        $filterList->setQueryBuilder($queryBuilder);
        $filterList->setAlias(self::ENTITY_ALIAS);
        $filterList->setEntity($this->class);
        $this->fireEvent(Event::FILTER_LIST, $filterList);

        $page = $request->query->get('page', 1);
        $perPage = $this->container->getParameter('symfonian_id.admin.per_page');

        $query = $queryBuilder->getQuery();
        $query->useQueryCache(true);
        $query->useResultCache(true, 1, serialize($query->getParameters()));

        $filterResult = new FilterResultEvent();
        $filterResult->setQuery($query);
        $filterResult->setEntity($this->class);
        $this->fireEvent(Event::FILTER_RESULT, $filterResult);

        $paginator = $this->container->get('knp_paginator');
        $pagination = $paginator->paginate($filterResult->getResult(), $page, $perPage);

        $data = array();
        $identifier = array();
        $header = array();
        foreach ($pagination as $key => $record) {
            /**
             * @var $record \Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface
             */
            $temp = array();
            $identifier[$key] = $record->getId();

            foreach ($gridFields as $k => $property) {
                $field = $property;
                $numberFormat = array();
                if (is_array($property)) {
                    $field = $property['field'];
                    $numberFormat = $property['format'];
                }

                if (0 === $key) {
                    array_push($header, $field);
                }

                $method = CamelCasizer::underScoretToCamelCase('get_'.$field);
                $result = null;
                if (method_exists($record, $method)) {
                    $result = call_user_func_array(array($record, $method), array());
                } else {
                    $method = CamelCasizer::underScoretToCamelCase('is_'.$field);

                    if (method_exists($record, $method)) {
                        $result = call_user_func_array(array($record, $method), array());
                    }
                }

                if ($result) {
                    if (!empty($numberFormat)) {
                        $result = number_format($result, $numberFormat['decimal'], $numberFormat['decimal_point'], $numberFormat['thousand_separator']);
                    }

                    array_push($temp, $result);
                }
            }

            $data[$key] = $temp;
        }

        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        $viewParams['pagination'] = $pagination;
        $viewParams['start'] = ($page - 1) * $perPage;
        $viewParams['menu'] = $this->container->getParameter('symfonian_id.admin.menu');
        $viewParams['header'] = array_merge($header, array('action'));
        $viewParams['action_method'] = $translator->trans('page.list', array(), $translationDomain);
        $viewParams['identifier'] = $identifier;
        $viewParams['action'] = $this->container->getParameter('symfonian_id.admin.grid_action');
        $viewParams['number'] = $this->container->getParameter('symfonian_id.admin.number');
        $viewParams['formating_number'] = $formatNumber;
        $viewParams['record'] = $data;
        $viewParams['filter'] = $filter;

        $this->viewParams = array_merge($this->viewParams, $viewParams);
    }

    /**
     * @param EntityInterface $data
     *
     * @return bool|\Symfony\Component\HttpFoundation\Response
     */
    public function remove(EntityInterface $data)
    {
        $event = new FilterEntityEvent();
        $event->setEntity($data);
        $event->setEntityManager($this->manager);
        $this->fireEvent(Event::PRE_DELETE, $event);

        if ($event->getResponse()) {
            return $event->getResponse();
        }

        $this->manager->remove($data);
        $this->manager->flush();

        return true;
    }

    /**
     * @param Request         $request
     * @param EntityInterface $data
     * @param array           $showFields
     */
    public function showDetail(Request $request, EntityInterface $data, array $showFields)
    {
        $session = $this->container->get('session');

        $referer = $session->get('referer');
        $refererHeader = $request->headers->get('referer');
        if ($refererHeader) {
            $referer = $refererHeader;
            $session->set('referer', $refererHeader);
        }

        $output = array();
        foreach ($showFields as $key => $property) {
            $method = 'get'.ucfirst($property);

            if (method_exists($data, $method)) {
                array_push($output, array(
                    'name' => $property,
                    'value' => call_user_func_array(array($data, $method), array()),
                ));
            } else {
                $method = 'is'.ucfirst($property);

                if (method_exists($data, $method)) {
                    array_push($output, array(
                        'name' => $property,
                        'value' => call_user_func_array(array($data, $method), array()),
                    ));
                }
            }
        }

        $event = new GetFormEvent();
        $event->setData($output);
        $this->fireEvent(Event::PRE_SHOW, $event);

        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        $viewParams['data'] = $output;
        $viewParams['menu'] = $this->container->getParameter('symfonian_id.admin.menu');
        $viewParams['action_method'] = $translator->trans('page.show', array(), $translationDomain);
        $viewParams['back'] = $referer;
        $viewParams['action'] = $this->container->getParameter('symfonian_id.admin.grid_action');
        $viewParams['number'] = $this->container->getParameter('symfonian_id.admin.number');
        $viewParams['upload_dir'] = $this->container->getParameter('symfonian_id.admin.upload_dir');

        $this->viewParams = array_merge($this->viewParams, $viewParams);
    }

    /**
     * @param CrudController     $controller
     * @param Request            $request
     * @param EntityInterface    $data
     * @param FormInterface|null $form
     *
     * @return mixed
     */
    public function createNewOrUpdate(CrudController $controller, Request $request, EntityInterface $data, FormInterface $form = null)
    {
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        $event = new FilterResponseEvent();
        $event->setController($controller);
        $event->setFormData($data);
        $event->setForm($form);
        $this->fireEvent(Event::PRE_FORM_SUBMIT, $event);

        $response = $event->getResponse();
        if ($response) {
            return $response;
        }

        $form->handleRequest($request);

        $viewParams['form'] = $form->createView();
        $viewParams['form_theme'] = $this->container->getParameter('symfonian_id.admin.themes.form_theme');
        $viewParams['menu'] = $this->container->getParameter('symfonian_id.admin.menu');

        if ($request->isMethod('POST')) {
            $preFormValidationEvent = new FilterResponseEvent();
            $preFormValidationEvent->setRequest($request);
            $preFormValidationEvent->setForm($form);
            $this->fireEvent(Event::PRE_FORM_VALIDATION, $preFormValidationEvent);

            $response = $preFormValidationEvent->getResponse();
            if ($response) {
                return $response;
            }

            if (!$form->isValid()) {
                $viewParams['errors'] = true;
            } else {
                $data = $form->getData();

                $preSaveEvent = new FilterRequestEvent();
                $preSaveEvent->setRequest($request);
                $preSaveEvent->setEntity($data);
                $preSaveEvent->setEntityManager($this->manager);
                $preSaveEvent->setForm($form);
                $this->fireEvent(Event::PRE_SAVE, $preSaveEvent);

                $this->manager->persist($data);
                $this->manager->flush();

                $postSaveEvent = new GetEntityEvent();
                $postSaveEvent->setEntityManager($this->manager);
                $postSaveEvent->setEntity($data);
                $this->fireEvent(Event::POST_SAVE, $postSaveEvent);

                $viewParams['success'] = $translator->trans('message.data_saved', array(), $translationDomain);
            }
        }

        $this->viewParams = array_merge($this->viewParams, $viewParams);
    }

    protected function fireEvent($name, $handler)
    {
        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch($name, $handler);
    }
}
