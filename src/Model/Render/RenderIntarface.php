<?php

namespace sacrpkg\CrudBundle\Model\Render;

use Symfony\Component\HttpFoundation\RequestStack;
use sacrpkg\CrudBundle\Model\Paginator;
use sacrpkg\CrudBundle\Model\Filter;
use Symfony\Component\HttpFoundation\Response;

interface RenderIntarface
{
    public function __construct(RequestStack $requestStack, \Twig\Environment $twig);
    
    public function setCollection(?array $collection): RenderIntarface;
    
    public function setActions(?array $actions): RenderIntarface;
    
    public function setFields(?array $fields): RenderIntarface;
    
    public function setButtons(?array $buttons): RenderIntarface;
    
    public function setPaginator(?Paginator $paginator): RenderIntarface;
    
    public function setFilter(?Filter $filter): RenderIntarface;
    
    public function setParams(?array $params): RenderIntarface;
    
    public function getReResponse(string $view): Response;
}
