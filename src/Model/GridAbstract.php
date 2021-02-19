<?php

namespace sacrpkg\CrudBundle\Model;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\RouterInterface;
use sacrpkg\CrudBundle\Model\Reader\ReaderInterface;
use sacrpkg\CrudBundle\Model\Render\RenderIntarface;

abstract class GridAbstract implements GridInterface
{
    const SESSION_COUNT_ITEMS = 'numitems';
    const SESSION_SORT_BY = 'sort_by';
    const SESSION_SORT_TYPE = 'sort_type';
    const SESSION_FILTER = 'filter';
    
	protected $sortfield_default = 'id';
	protected $sorttype_default = 'DESC';
    private $session;
    protected $controller;
    protected $entityname;
    protected $collection;
    protected $paginator;
    protected $fields;
    protected $actions;
    protected $buttons;
    protected $action_route;
	protected $add_route;
    protected $grid_route;
    protected $title = 'Grid';
    protected $request;
    protected $itemsonpage = 50;
    protected $optnumpages = [20,50,100,200];
    protected $sortby;
    protected $sorttype;
    protected $search;
    protected $filterdata;
    protected $linestyles;
	protected $params;
	protected $formview;
	protected $breadcrumb;
	protected $use_paginator = true;
	protected $edit_only = false;
    protected $use_filter = false;
    protected $filter_fields = null;
    protected $use_checker = false;
    protected $messages;
    protected $em;
    protected $reader;
    protected $router;
    protected $render;

    public function __construct(RequestStack $requestStack, ManagerRegistry $doctrine, ReaderInterface $reader,
            PaginatorInterface $paginator, RouterInterface $router, FilterInterface $filter, RenderIntarface $render)
    { 
        $this->request = $requestStack->getCurrentRequest();
        $this->em = $doctrine->getManager();
        $this->session = $this->request->getSession();
        $this->paginator = $paginator;
        $this->router = $router;
        $this->reader = $reader;
        $this->filter = $filter;
        
        $this->render = $render;
        
        $this->init();
        $this->filter->setGridRoute($this->grid_route);
        $this->paginator->setGrid($this)
            ->init($this->itemsonpage, $this->sortfield_default,
                                $this->sorttype_default, $this->grid_route);           
        $this->afterInit();
 
    }
    
    public function setController(AbstractController $controller): self
    {
        $this->controller = $controller;
        
        return $this;
    }
    
    public function getFields(): Array
    {
        return $this->fields;
    }
    
    protected function afterInit(): self
    {
        return $this;
    }
    
    public function setEntity($entity)
    {
        $this->entityname = $entity;
        return $this;
    }
    
	public function setFilter($data)
	{
		$this->filterdata = $data;
		return $this;
	}
	
    public function getResponse()
    {
        if (!$this->collection)
            $this->fetch();
            
        $formview = $this->formview ?: 'Admin/grid/grid.html.twig';
        
        return $this->render->setCollection($this->collection)
                    ->setActions($this->actions)
                    ->setFields($this->fields)
                    ->setButtons($this->buttons)
                    ->setPaginator($this->paginator)
                    ->setFilter($this->filter)
                    ->setParams([
                       // 'grid_route' => $this->grid_route,

                            'title' => $this->title,
                       //     'sortby' => $this->sortby,
                       //     'sorttype' => strtolower($this->sorttype),
                       //     'search' => $this->search,
                       
                            
                            'linestyles' => $this->linestyles,
                            'params' => $this->params,
                            'breadcrumb' => $this->breadcrumb,
                        //    'use_paginator' => $this->use_paginator,
                            'edit_only' => $this->edit_only,
                            'action_route' => $this->action_route,
                            'use_checker' => $this->use_checker,
                      //     'usefilter' => $this->usefilter,      
                      //      'use_filter' => $this->use_filter,  
                      //      'filter_fields' => $this->filter_fields, 
                    ])
                    ->getReResponse($formview);
            /*

            
        return new Response( $this->twig->render($formview,
            [
                'rows' => $this->getCollection(),
                'paginator' => $this->paginator,
                'grid_route' => $this->grid_route,
                'fields' => $this->fields,
                'actions' => $this->actions,
                'buttons' => $this->buttons,
                'title' => $this->title,
                'sortby' => $this->sortby,
                'sorttype' => strtolower($this->sorttype),
                'search' => $this->search,
                'usefilter' => $this->usefilter,
                'filter' => $this->filter->getData(),
                'linestyles' => $this->linestyles,
				'params' => $this->params,
				'breadcrumb' => $this->breadcrumb,
				'use_paginator' => $this->use_paginator,
				'edit_only' => $this->edit_only,
				'action_route' => $this->action_route,
                'use_checker' => $this->use_checker,
                'use_filter' => $this->use_filter,  
                'filter_fields' => $this->filter_fields, 
            ]));
            */
    }

    public function setParam(string $name, $value)
    {
        $this->params[$name] = $value;
        return $this;
    }	
	
    protected function getCollection()
    {
        if (is_null($this->collection))
            $this->fetch();
            
        return $this->collection;
    }
    
    protected function init()
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
    
	public function setFormView($formview)
	{
		$this->formview = $formview;
		return $this;
	}
	
    protected function getGridSession($name)
    {
        return $this->session->get((new \ReflectionClass($this))->getShortName().'_'.$name);
    }
    
    protected function setGridSession($name, $value)
    {
        $this->session->set((new \ReflectionClass($this))->getShortName().'_'.$name, $value);
        return $this;
    }
    
    protected function getPaginator($total, Request $request)
    {
        $p = $request->get('p') ?? '0';
        $count = ($request->get('page_count')) ?? null;
        if ($count && in_array($count, $this->optnumpages)) {
            $this->setGridSession(self::SESSION_COUNT_ITEMS, $count); //$this->session->set((new \ReflectionClass($this))->getShortName().'_'.self::SESSION_COUNT_ITEMS, $count);
            $this->itemsonpage = $count;
        }
        
        if (!$total)
            return null;
        
        $paginator = new \stdClass;
        $paginator->total = $total;
        $paginator->optnumpages = $this->optnumpages;
        $paginator->currpage = $p;
        $paginator->numpages = ceil($paginator->total/$this->itemsonpage);
        $paginator->itemsonpage = $this->itemsonpage;
        $paginator->route = $this->grid_route;

        return $paginator;        
    }
    
    protected function initSort(): void
    {
        $this->sortby = ($this->getGridSession('sort_by')) ?? $this->sortfield_default;
        $this->sorttype = ($this->getGridSession('sort_type')) ?? $this->sorttype_default;
        if ($this->request->get('sort_by') &&
                in_array($this->request->get('sort_by'), array_keys(array_filter($this->fields, 
                    function($item){
                        return $item['sortable'] ?? false;}
                    ))
                )) {
            if ($this->sortby == $this->request->get('sort_by'))
                $this->sorttype = ($this->sorttype == 'ASC') ? 'DESC' : 'ASC';       
            $this->sortby = $this->request->get('sort_by');
            $this->setGridSession(self::SESSION_SORT_BY, $this->sortby);
            $this->setGridSession(self::SESSION_SORT_TYPE, $this->sorttype);
        }  
    }

    protected function initSearch(): void
    {
		$currfilter = $this->filterdata;
        $this->search = strtolower($this->request->get('search') ?? null);
        if ($this->request->get('filter_apply', null)) {
            $filter = $this->request->get('filter');
            $this->setGridSession(self::SESSION_FILTER, $filter);
        } elseif ($this->request->get('filter_reset', null)) {
            $filter = null;
            $this->setGridSession(self::SESSION_FILTER, $filter);
        } else
            $filter = ($this->getGridSession(self::SESSION_FILTER)) ?? null;
        $this->filterdata = $filter;
		if ($currfilter) {
			if (is_array($this->filterdata))
				$this->filterdata = array_merge($this->filterdata, $currfilter);
			else
				$this->filterdata = $currfilter;
		}
    }

    protected function beforeGetCollection()
    {
    }

    protected function afterGetCollection()
    {
    }
    
    protected function fetch()
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
        /*
        $this->initSearch();


            $repository = $this->em->getRepository($this->entityname);
                            
            if (method_exists($repository, 'getByPage')) {
                $this->beforeGetCollection();

                $items = $repository->getGridCollection((clone $this->paginator)->incPage(), $this->filter ?? []);
                $total = ($this->paginator->getCurrPage()+1)*$this->paginator->getItemsOnPage() + count($items ?? []);

                $this->paginator->setTotal($total);

                $this->collection = $repository->getGridCollection($this->paginator, $this->filter ?? []);
                $this->afterGetCollection(); 
            } else {
                $this->collection = $repository->findBy([]);
                $this->paginator = null;
            }
        } catch (Exception $e) {
            $this->collection = null;
            $this->flashMessage('error', $e->getMessage());
        }
        */
        
        
        
        
        return $this;
    }
    
    protected function flashMessage($type, $mes): self
    {
        $messages[] = ['type' => $type, 'mes' => $mes];
        if (method_exists($this->controller, 'displayMessage')) {
            $this->controller->displayMessage($type, $mes);
        }
        
        return $this;
    }
}
