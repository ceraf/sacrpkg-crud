<?php

namespace sacrpkg\CrudBundle\Model;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class Paginator implements PaginatorInterface
{  
    protected $params;    
    protected $router;
    protected $request;
    protected $items_on_page;
    protected $curr_page;
    protected $sortby;
    protected $sorttype;
    protected $sort_fields = ['id', 'uri', 'name'];
    protected $route;
    protected $total;
    protected $session;
    protected $grid;
    protected $use_paginator = true;

    public function __construct (RequestStack $requestStack, RouterInterface $router)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->router = $router;
        $this->session = $this->request->getSession();
    }
    
    public function setGrid(GridAbstract $grid): PaginatorInterface
    {
        $this->grid = $grid;
        
        return $this;
    }    
    
    public function setSortFields(Array $sort_fields): PaginatorInterface
    {
        $this->sort_fields = $sort_fields;
        
        return $this;
    }

    public function getParam($name)
    {
        return $this->params[$name] ?? null;
    }
    
    public function setParam($name, $value): PaginatorInterface
    {
        $this->params[$name] = $value;
        
        return $this;
    }
    
    public function init($items_on_page, $sortby, $sorttype, $grid_route): PaginatorInterface
    {
        $p = $this->request->get('p') ?? 0;
        $this->curr_page = $p ?: 0;
        $this->items_on_page = $items_on_page;
        $this->route = $grid_route;

        $this->sortby = ($this->getSession('sort_by')) ?? $sortby;
        $this->sorttype = ($this->getSession('sort_type')) ?? $sorttype;

        if ($this->request->get('sort_by') &&
                in_array($this->request->get('sort_by'), array_keys(array_filter($this->grid->getFields(), 
                    function($item){
                        return $item['sortable'] ?? false;}
                    ))
                ))
        {
            if ($this->sortby == $this->request->get('sort_by'))
                $this->sorttype = ($this->sorttype == 'ASC') ? 'DESC' : 'ASC';       
            $this->sortby = $this->request->get('sort_by');
            $this->setSession('sort_by', $this->sortby);
            $this->setSession('sort_type', $this->sorttype);
        }
        
        return $this;
    }
    
    public function getTotal(): ?int
    {
        return $this->total;
    }
    
    public function setTotal($total): PaginatorInterface
    {
        $this->total = $total;
        
        return $this;
    }

    public function getNumPages(): ?int
    {
        return ceil($this->total/$this->items_on_page);
    }
    
    public function getRoute(): string
    {
        return $this->route;
    }
    
    public function getSortBy(): string
    {
        return $this->sortby;
    }
    
    public function getSortType(): string
    {
        return $this->sorttype;
    }
    
    public function getCurrPage(): int
    {
        return $this->curr_page;
    }
    
    public function getItemsOnPage(): int
    {
        return $this->items_on_page;
    }
    
    public function incPage(): self
    {
        $this->curr_page++;
        
        return $this;
    }
    
    public function isUse(): bool
    {
        return $this->use_paginator;
    }
    
    public function getPrevUrl(): ?string
    {
        if ($this->curr_page == 0) {
            return null;
        } else {
            return $this->router->generate($this->route, ['p' => $this->curr_page - 1]);
        }
    }
    
    public function getNextUrl(): ?string
    {
        if ($this->curr_page == ($this->getNumPages() - 1)) {
            return null;
        } else {
            return $this->router->generate($this->route, ['p' => $this->curr_page + 1]);
        }
    }
    
    protected function getSession($name)
    {
        return $this->session->get($this->getSessionPrefix().'_'.$name);
    }
    
    protected function setSession($name, $value)
    {
        $this->session->set($this->getSessionPrefix().'_'.$name, $value);
        return $this;
    }
    
    protected function getSessionPrefix()
    {
        $url = $this->request->server->get('REQUEST_URI');
        if (strpos($url, '?') !== false) {
            $url = substr($url, 0, strpos($url, '?'));
        }
        return str_replace('/', '', $url).'_'.(new \ReflectionClass($this))->getShortName();
    }
}
