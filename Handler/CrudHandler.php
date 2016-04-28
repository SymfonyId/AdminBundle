<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Handler;

use Doctrine\ORM\Query;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfonian\Indonesia\AdminBundle\Controller\CrudController;
use Symfonian\Indonesia\AdminBundle\Event\FilterEntityEvent;
use Symfonian\Indonesia\AdminBundle\Event\FilterQueryEvent;
use Symfonian\Indonesia\AdminBundle\SymfonianIndonesiaAdminConstants as Constants;
use Symfonian\Indonesia\AdminBundle\Util\MethodInvoker;
use Symfonian\Indonesia\AdminBundle\View\View;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\SoftDeletableInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class CrudHandler implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $manager;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $repository;

    /**
     * @var \Doctrine\ORM\Mapping\ClassMetadata
     */
    private $classMetadata;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $template;

    /**
     * @var string
     */
    private $errorMessage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->manager = $container->get('doctrine.orm.entity_manager');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->container->get('templating')->renderResponse($this->template, $this->container->get('symfonian_id.admin.view.view')->getParams());
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
        $this->classMetadata = $this->manager->getClassMetadata($class);
        $this->class = $this->classMetadata->getName();
    }

    /**
     * @param Request   $request
     * @param array     $gridFields
     * @param array     $actionAllowed
     * @param bool|true $allowCreate
     * @param bool|true $allowBulkDelete
     * @param bool|true $formatNumber
     */
    public function viewList(Request $request, array $gridFields, array $actionAllowed, $allowCreate = true, $allowBulkDelete = true, $formatNumber = true)
    {
        $page = $request->query->get('page', 1);
        $perPage = $this->container->getParameter('symfonian_id.admin.per_page');
        $pagination = $this->paginateResult($page, $perPage);
        $data = array();
        $identifier = array();
        /** @var \Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface $record */
        foreach ($pagination as $key => $record) {
            $temp = array();
            $identifier[$key] = $record->getId();

            foreach ($gridFields as $k => $property) {
                $field = $property;
                $numberFormat = array();
                if (is_array($property)) {
                    $field = $property['field'];
                    $numberFormat = $property['format'];
                }

                $result = MethodInvoker::invokeGet($record, $field);
                if (null !== $result) {
                    if (!empty($numberFormat)) {
                        $result = number_format($result, $numberFormat['decimal'], $numberFormat['decimal_point'], $numberFormat['thousand_separator']);
                    }
                } else {
                    $result = '';
                }

                array_push($temp, $result);
            }

            $data[$key] = $temp;
        }

        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        /** @var View $view */
        $view = $this->container->get('symfonian_id.admin.view.view');
        $view->setParam('pagination', $pagination);
        $view->setParam('start', ($page - 1) * $perPage);
        $view->setParam('menu', $this->container->getParameter('symfonian_id.admin.menu'));
        $view->setParam('header', array_map(function ($value) use ($translator, $translationDomain) {
            return array(
                'title' => $translator->trans(sprintf('entity.fields.%s', $value), array(), $translationDomain),
                'field' => $value,
                'sortable' => $value === 'action' ? false : true,
            );
        }, array_merge($gridFields, array('action'))));
        $view->setParam('action_method', $translator->trans('page.list', array(), $translationDomain));
        $view->setParam('identifier', $identifier);
        $view->setParam('action', $actionAllowed);
        $view->setParam('allow_create', $allowCreate);
        $view->setParam('allow_delete', $allowBulkDelete);
        $view->setParam('allow_download', $this->isAllowDownload());
        $view->setParam('number', $this->container->getParameter('symfonian_id.admin.number'));
        $view->setParam('formating_number', $formatNumber);
        $view->setParam('record', $data);
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
        $this->fireEvent(Constants::PRE_DELETE, $event);

        if ($event->getResponse()) {
            return $event->getResponse();
        }

        try {
            $this->delete($data);
        } catch (\Exception $ex) {
            $this->errorMessage = 'message.delete_failed';

            return false;
        }

        return true;
    }

    /**
     * @param Request         $request
     * @param EntityInterface $data
     * @param array           $showFields
     * @param bool            $allowDelete
     */
    public function showDetail(Request $request, EntityInterface $data, array $showFields, $allowDelete = true)
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
            if ($value = MethodInvoker::invokeGet($data, $property)) {
                array_push($output, array(
                    'name' => $property,
                    'value' => $value,
                ));
            }
        }

        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        /** @var View $view */
        $view = $this->container->get('symfonian_id.admin.view.view');
        $view->setParam('data', $output);
        $view->setParam('menu', $this->container->getParameter('symfonian_id.admin.menu'));
        $view->setParam('action_method', $translator->trans('page.show', array(), $translationDomain));
        $view->setParam('back', $referer);
        $view->setParam('action', $allowDelete);
        $view->setParam('number', $this->container->getParameter('symfonian_id.admin.number'));
        $view->setParam('upload_dir', $this->container->getParameter('symfonian_id.admin.upload_dir'));
    }

    /**
     * @param CrudController     $controller
     * @param Request            $request
     * @param EntityInterface    $data
     * @param FormInterface|null $form
     *
     * @return mixed
     */
    public function createNewOrUpdate(CrudController $controller, Request $request, EntityInterface $data, FormInterface $form)
    {
        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('symfonian_id.admin.translation_domain');

        $form->handleRequest($request);

        /** @var View $view */
        $view = $this->container->get('symfonian_id.admin.view.view');
        $view->setParam('form', $form->createView());
        $view->setParam('form_theme', $this->container->getParameter('symfonian_id.admin.themes.form_theme'));
        $view->setParam('menu', $this->container->getParameter('symfonian_id.admin.menu'));

        if ($request->isMethod('POST')) {
            if (!$form->isValid()) {
                $view->setParam('errors', true);
            } else {
                $this->save($form->getData());

                $view->setParam('success', $translator->trans('message.data_saved', array(), $translationDomain));
            }
        }
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @param EntityInterface $entity
     *
     * @return bool
     */
    public function save(EntityInterface $entity)
    {
        $preSaveEvent = new FilterEntityEvent();
        $preSaveEvent->setEntity($entity);
        $preSaveEvent->setEntityManager($this->manager);
        $this->fireEvent(Constants::PRE_SAVE, $preSaveEvent);

        try {
            $this->manager->persist($preSaveEvent->getEntity());
            $this->manager->flush();

            $postSaveEvent = new FilterEntityEvent();
            $postSaveEvent->setEntityManager($this->manager);
            $postSaveEvent->setEntity($entity);
            $this->fireEvent(Constants::POST_SAVE, $postSaveEvent);
        } catch (\Exception $ex) {
            $this->errorMessage = $ex->getTraceAsString();

            return false;
        }

        return true;
    }

    /**
     * @param array $columns
     *
     * @return array
     */
    public function exportData(array $columns)
    {
        $csvData = $this->getCsvData($columns);
        $response = new StreamedResponse(function () use ($csvData) {
            $resources = fopen('php://output', 'w');

            foreach ($csvData as $item) {
                fputcsv($resources, $item);
            }
        });

        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, sprintf('%s.csv', date('YmdHis')));

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @param array $columns
     *
     * @return array
     */
    private function getCsvData(array $columns)
    {
        $output = array(array_merge(array('id'), $columns));

        if (!$this->isAllowDownload()) {
            return $output;
        }

        /** @var EntityInterface $record */
        foreach ($this->repository->findAll() as $record) {
            $temp = array();
            foreach ($columns as $column) {
                $temp[] = MethodInvoker::invokeGet($record, $column);
            }

            $output[] = $temp;
        }

        return $output;
    }

    /**
     * @param int $page
     * @param int $perPage
     *
     * @return PaginationInterface
     */
    private function paginateResult($page, $perPage)
    {
        $queryBuilder = $this->repository->createQueryBuilder(Constants::ENTITY_ALIAS);

        $filterList = new FilterQueryEvent();
        $filterList->setQueryBuilder($queryBuilder);
        $filterList->setAlias(Constants::ENTITY_ALIAS);
        $filterList->setEntityClass($this->class);
        $this->fireEvent(Constants::FILTER_LIST, $filterList);

        $query = $queryBuilder->getQuery();
        $query->useQueryCache(true);
        $query->useResultCache(true, 1, serialize($query->getParameters()));

        $paginator = $this->container->get('knp_paginator');

        return $paginator->paginate($query, $page, $perPage);
    }

    /**
     * @param EntityInterface $entity
     */
    private function delete(EntityInterface $entity)
    {
        if ($entity instanceof SoftDeletableInterface) {
            $entity->isDeleted(true);
            $entity->setDeletedAt(new \DateTime());
            $entity->setDeletedBy($this->tokenStorage->getToken()->getUsername());

            $this->manager->persist($entity);
            $this->manager->flush();
        } else {
            $this->manager->remove($entity);
            $this->manager->flush();
        }
    }

    /**
     * @return bool
     */
    private function isAllowDownload()
    {
        $queryBuilder = $this->repository->createQueryBuilder(Constants::ENTITY_ALIAS);
        $queryBuilder->select(sprintf('COUNT(%s.id)', Constants::ENTITY_ALIAS));

        $totalResult = $queryBuilder->getQuery()->getSingleScalarResult();
        if ($this->container->getParameter('symfonian_id.admin.max_records') < $totalResult) {
            return false;
        }

        return true;
    }

    /**
     * Dispatch Event.
     *
     * @param string $name
     * @param string $handler
     */
    private function fireEvent($name, $handler)
    {
        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch($name, $handler);
    }
}
