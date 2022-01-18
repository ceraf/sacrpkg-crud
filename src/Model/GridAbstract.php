<?php

/*
 * This file is part of the Sacrpkg CrudBundle package.
 *
 * (c) Oleg Bruyako <jonsm2184@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace sacrpkg\CrudBundle\Model;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\RouterInterface;
use sacrpkg\CrudBundle\Model\Reader\ReaderInterface;
use sacrpkg\CrudBundle\Model\Render\RenderIntarface;

/**
 * Abstract grid.
 */
abstract class GridAbstract implements GridInterface
{
    const SESSION_COUNT_ITEMS = 'numitems';
    const SESSION_SORT_BY = 'sort_by';
    const SESSION_SORT_TYPE = 'sort_type';
    const SESSION_FILTER = 'filter';
    
    /**
     * Field name for default sort.
     *
     * @var string
     */
	protected $sortfield_default = 'id';
    
    /**
     * Default sort type.
     *
     * @var string
     */
	protected $sorttype_default = 'DESC';
    
    /**
     * Default sort type.
     *
     * @var SessionInterface
     */
    protected $session;
    
    /**
     * Entity class name.
     *
     * @var string
     */    
    protected $entityname;
    
    /**
     * Rows from database. 
     *
     * @var array
     */    
    protected $collection;
    
    /**
     * Paginator object. 
     *
     * @var PaginatorInterface
     */       
    protected $paginator;
    
    /**
     * Paginator object. 
     *
     * @var array
     */  
    protected $fields;
    
    /**
     * Actions for each line.
     *
     * @var array
     */    
    protected $actions;
    
    /**
     * Buttons for grid.
     *
     * @var array
     */     
    protected $buttons;
    
    /**
     * Route name for grid actions.
     *
     * @var string
     */     
    protected $action_route;
    
    /**
     * Route name for add new line to grid.
     *
     * @var string
     */    
	protected $add_route;
    
    /**
     * Route name for grid list.
     *
     * @var string
     */ 
    protected $grid_route;
    
    /**
     * Grid title.
     *
     * @var string
     */     
    protected $title = 'Grid';
    
    /**
     * Request data.
     *
     * @var RequestStack
     */       
    protected $request;
    
    /**
     * Number line on page.
     *
     * @var int
     */     
    protected $itemsonpage = 50;
    
    /**
     * Options for the number of lines per page.
     *
     * @var array
     */  
    protected $optnumpages = [20,50,100,200];
    
    /**
     * Filter data for defaule.
     *
     * @var array
     */  
    protected $filterdata;
    
    /**
     * Custom settings for each line.
     *
     * @var array
     */ 
    protected $linestyles;
    
    /**
     * Custom grid parameters.
     *
     * @var array
     */    
	protected $params;
    
    /**
     * Path to template file.
     *
     * @var string
     */       
	protected $formview;
    
    /**
     * Breadcrumb array.
     *
     * @var array
     */      
	protected $breadcrumb;
    
    /**
     * Flag for use paginator.
     *
     * @var bool
     */     
	protected $use_paginator = true;
    
    /**
     * Flag to set actions icon for each line.
     *
     * @var bool
     */
	protected $edit_only = false;
    
    /**
     * Filter fields.
     *
     * @var array
     */    
    protected $filter_fields = null;
    
    /**
     * Flag to use checkbox befor each line.
     *
     * @var bool
     */     
    protected $use_checker = false;
    
    /**
     * History messages.
     *
     * @var array
     */     
    protected $messages;
    
    /**
     * Object for get data from database.
     *
     * @var ReaderInterface
     */      
    protected $reader;
    
    /**
     * @var RouterInterface
     */
    protected $router;
    
    /**
     * @var RenderIntarface
     */
    protected $render;

    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    public function __construct(RequestStack $requestStack, ReaderInterface $reader, ManagerRegistry $doctrine,
            PaginatorInterface $paginator, RouterInterface $router, FilterInterface $filter, RenderIntarface $render)
    { 
        $this->request = $requestStack->getCurrentRequest();
        $this->session = $this->request->getSession();
        $this->paginator = $paginator;
        $this->router = $router;
        $this->reader = $reader;
        $this->filter = $filter;
        $this->doctrine = $doctrine;
        
        $this->render = $render;
        
        $this->init();
        $this->filter->setGridRoute($this->grid_route);
        $this->paginator->setGrid($this)
            ->init($this->itemsonpage, $this->sortfield_default,
                                $this->sorttype_default, $this->grid_route);           
        $this->afterInit();
 
    }

    /**
     * {@inheritdoc}
     */
    public function getFields(): Array
    {
        return $this->fields;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setEntity(string $entity): GridInterface
    {
        $this->entityname = $entity;
        return $this;
    }    
    
    /**
     * {@inheritdoc}
     */
	public function setFilter(Array $data): GridInterface
	{
		$this->filterdata = $data;
		return $this;
	}    
    
    /**
     * {@inheritdoc}
     */
    public function setParam(string $name, $value): GridInterface
    {
        $this->params[$name] = $value;
        return $this;
    }	
    
    /**
     * {@inheritdoc}
     */
	public function setFormView(string $formview): GridInterface
	{
		$this->formview = $formview;
		return $this;
	}
    
    /**
     * {@inheritdoc}
     */
    public function getResponse(): Response
    {
        if (!$this->collection)
            $this->fetch();
            
        $formview = $this->formview ?: '@SacrpkgCrud/grid/grid.html.twig';
        
        return $this->render->setCollection($this->collection)
                    ->setActions($this->actions)
                    ->setFields($this->fields)
                    ->setButtons($this->buttons)
                    ->setPaginator($this->paginator)
                    ->setFilter($this->filter)
                    ->setParams([
                            'title' => $this->title,
                            'linestyles' => $this->linestyles,
                            'params' => $this->params,
                            'breadcrumb' => $this->breadcrumb,
                            'edit_only' => $this->edit_only,
                            'action_route' => $this->action_route,
                            'use_checker' => $this->use_checker,
                    ])
                    ->getResponse($formview);
    }
    
    /**
     * After init grid event.
     *
     * @return $this
     */
    protected function afterInit(): GridInterface
    {
        return $this;
    }

    /**
     * Get rows from database.
     *
     * @return array
     */
    protected function getCollection(): ?array
    {
        if (is_null($this->collection))
            $this->fetch();
            
        return $this->collection;
    }
    
    /**
     * Initialization grid.
     *
     * @return void
     */
    protected function init(): void
    {
        $this->usefilter = false;
        $this->actions['edit'] = [
                'title' => 'Редактировать',
                'route' => $this->action_route,
                'action' => 'edit',
                'field_id' => 'id',
                'icon' => 'icon-gear',
                'btntype' => 'btn-success',
                'onclick' => ''
        ];

        $this->buttons['add'] = [
            'title' => 'Добавить',
            'route' => $this->add_route,
            'action' => '',
            'btnstyle' => 'btn bg-success',
			'icon' => 'icon-plus-circle2 mr-2'
        ];
    }

    /**
     * Get grid data from session.
     *
     * @param string $name Parameter name
     *
     * @return mixed
     */
    protected function getGridSession(string $name)
    {
        return $this->session->get($this->getSessionPrefix().'_'.$name);
    }
    
    /**
     * Set grid data to session.
     *
     * @param string $name Parameter name
     * @param string $value Parameter value
     *
     * @return GridInterface
     */
    protected function setGridSession($name, $value): GridInterface
    {
        $this->session->set($this->getSessionPrefix().'_'.$name, $value);
        return $this;
    }

    /**
     * Get grid session prefix.
     *
     * @return string
     */
    protected function getSessionPrefix(): string
    { 
        $url = $this->request->server->get('REQUEST_URI');
        if (strpos($url, '?') !== false) {
            $url = substr($url, 0, strpos($url, '?'));
        }
        return str_replace('/', '', $url).'_'.(new \ReflectionClass($this))->getShortName();
    }

    /**
     * Before get rows from database.
     *
     * @return void
     */
    protected function beforeGetCollection(): void
    {
    }

    /**
     * After get rows from database.
     *
     * @return void
     */
    protected function afterGetCollection(): void
    {
    }
    
    /**
     * Fetch data from database.
     *
     * @return $this
     */
    protected function fetch(): GridInterface
    {
        $this->filter->init($this->filter_fields, $this->filterdata);
        try {     
            $this->beforeGetCollection();
            $this->reader->fetch($this->paginator, $this->filter, $this->entityname);
            $this->collection = $this->reader->getCollection();
            $this->afterGetCollection(); 
        } catch (\Exception $e) {
            $this->collection = null;
            $this->flashMessage('error', $e->getMessage());
        }

        return $this;
    }

    /**
     * Add message to session flash.
     *
     * @param string $type Type message
     * @param string $mes Message text
     *
     * @return $this
     */
    protected function flashMessage(string $type, string $mes): GridInterface
    {
        $messages[] = ['type' => $type, 'mes' => $mes];
        $this->session->getFlashBag()->add($type, $mes);
        
        return $this;
    }
}
