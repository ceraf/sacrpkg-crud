<?php

namespace sacrpkg\CrudBundle\Model;

use Symfony\Component\HttpFoundation\RequestStack;

class Paginator
{  
    protected $params;    
    private $request;
    private $items_on_page;
    private $curr_page;
    private $sortby;
    private $sorttype;
    private $sort_fields = ['id', 'uri', 'name'];
    private $route;
    private $total;
    private $session;
    private $grid;

    public function __construct (RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->session = $this->request->getSession();
    }
    
    public function setGrid(AdminGrid $grid): self
    {
        $this->grid = $grid;
        
        return $this;
    }    
    
    public function setSortFields(Array $sort_fields): self
    {
        $this->sort_fields = $sort_fields;
        
        return $this;
    }
    
    public function getParam($name)
    {
        return $this->params[$name] ?? null;
    }
    
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
        
        return $this;
    }
    
    public function init($items_on_page, $sortby, $sorttype, $grid_route)
    {
        $p = $this->request->get('p') ?? 0;
        $this->curr_page = $p;
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
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }
    
    public function setTotal($total): self
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
    
    protected function getSession($name)
    {
        return $this->session->get((new \ReflectionClass($this->grid))->getShortName().'_'.$name);
    }
    
    protected function setSession($name, $value)
    {
        $this->session->set((new \ReflectionClass($this->grid))->getShortName().'_'.$name, $value);
        return $this;
    }
}
