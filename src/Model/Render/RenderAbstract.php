<?php

namespace sacrpkg\CrudBundle\Model\Render;

use sacrpkg\CrudBundle\Model\Paginator;
use sacrpkg\CrudBundle\Model\Filter;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

abstract class RenderAbstract implements RenderIntarface
{
    protected $request;
    protected $twig;
    protected $collection = [];
    protected $actions;
    protected $buttons;  
    protected $paginator; 
    protected $filter;    
    protected $fields;
    protected $params;
    
    abstract public function getReResponse(string $view): Response;
    
    public function __construct(RequestStack $requestStack, \Twig\Environment $twig)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->twig = $twig;
    }
    
    public function setParams(?array $params): RenderIntarface
    {
        $this->params = $params;
        
        return $this;
    }    
    
    public function setFields(?array $fields): RenderIntarface
    {
        $this->fields = $fields;
        
        return $this;
    }
    
    public function setCollection(?array $collection): RenderIntarface
    {
        $this->collection = $collection;
        
        return $this;
    }
    
    public function setActions(?array $actions): RenderIntarface
    {
        $this->actions = $actions;
        
        return $this;
    }
    
    public function setButtons(?array $buttons): RenderIntarface
    {
        $this->buttons = $buttons;
        
        return $this;
    }
    
    public function setPaginator(?Paginator $paginator): RenderIntarface
    {
        $this->paginator = $paginator;
        
        return $this;
    }
    
    public function setFilter(?Filter $filter): RenderIntarface
    {
        $this->filter = $filter;
        
        return $this;
    }
}
