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

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

/**
 * Base paginator.
 */
class Paginator implements PaginatorInterface
{  
    /**
     * Custom grid parameters.
     *
     * @var array
     */  
    protected $params;   

    /**
     * @var RouterInterface
     */
    protected $router;
    
    /**
     * Request data.
     *
     * @var RequestStack
     */   
    protected $request;
    
    /**
     * Number items on page.
     *
     * @var int
     */
    protected $items_on_page;
    
    /**
     * Number current page.
     *
     * @var int
     */
    protected $curr_page;
    
    /**
     * Field name for sort.
     *
     * @var string
     */
    protected $sortby;
    
    /**
     * Sort type.
     *
     * @var string
     */
    protected $sorttype;
    
    /**
     * List fields for sort.
     *
     * @var array
     */
    protected $sort_fields = ['id', 'uri', 'name'];
    
    /**
     * Route to grid.
     *
     * @var string
     */
    protected $route;
    
    /**
     * Number total items.
     *
     * @var int
     */
    protected $total;
    
    /**
     * Default sort type.
     *
     * @var SessionInterface
     */
    protected $session;
    
    /**
     * Request data.
     *
     * @var GridInterface
     */
    protected $grid;
    
    /**
     * Flag for use paginator.
     *
     * @var bool
     */     
    protected $use_paginator = true;

    public function __construct (RequestStack $requestStack, RouterInterface $router)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->router = $router;
        $this->session = $this->request->getSession();
    }
    
    /**
     * {@inheritdoc}
     */
    public function setGrid(GridInterface $grid): PaginatorInterface
    {
        $this->grid = $grid;
        
        return $this;
    }    
    
    /**
     * {@inheritdoc}
     */
    public function setSortFields(Array $sort_fields): PaginatorInterface
    {
        $this->sort_fields = $sort_fields;
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getParam($name)
    {
        return $this->params[$name] ?? null;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setParam($name, $value): PaginatorInterface
    {
        $this->params[$name] = $value;
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
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
    
    /**
     * {@inheritdoc}
     */
    public function getTotal(): ?int
    {
        return $this->total;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setTotal($total): PaginatorInterface
    {
        $this->total = $total;
        
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNumPages(): ?int
    {
        return ceil($this->total/$this->items_on_page);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRoute(): string
    {
        return $this->route;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getSortBy(): string
    {
        return $this->sortby;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getSortType(): string
    {
        return $this->sorttype;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getCurrPage(): int
    {
        return $this->curr_page;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getItemsOnPage(): int
    {
        return $this->items_on_page;
    }
    
    /**
     * {@inheritdoc}
     */
    public function incPage(): PaginatorInterface
    {
        $this->curr_page++;
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isUse(): bool
    {
        return $this->use_paginator;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPrevUrl(): ?string
    {
        if ($this->curr_page == 0) {
            return null;
        } else {
            return $this->router->generate($this->route, ['p' => $this->curr_page - 1]);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getNextUrl(): ?string
    {
        if ($this->curr_page == ($this->getNumPages() - 1)) {
            return null;
        } else {
            return $this->router->generate($this->route, ['p' => $this->curr_page + 1]);
        }
    }
    
    /**
     * Get paginator data from session.
     *
     * @param string $name Parameter name
     *
     * @return mixed
     */
    protected function getSession($name)
    {
        return $this->session->get($this->getSessionPrefix().'_'.$name);
    }
    
    /**
     * Set paginator data to session.
     *
     * @param string $name Parameter name
     * @param string $value Parameter value
     *
     * @return GridInterface
     */
    protected function setSession($name, $value)
    {
        $this->session->set($this->getSessionPrefix().'_'.$name, $value);
        return $this;
    }
    
    /**
     * Get paginator session prefix.
     *
     * @return string
     */
    protected function getSessionPrefix()
    {
        $url = $this->request->server->get('REQUEST_URI');
        if (strpos($url, '?') !== false) {
            $url = substr($url, 0, strpos($url, '?'));
        }
        return str_replace('/', '', $url).'_'.(new \ReflectionClass($this))->getShortName();
    }
}
