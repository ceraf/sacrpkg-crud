<?php

namespace sacrpkg\CrudBundle\Model;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

interface PaginatorInterface
{
    public function __construct (RequestStack $requestStack, RouterInterface $router);
    
    public function setGrid(GridAbstract $grid): PaginatorInterface; 
    
    public function setSortFields(Array $sort_fields): PaginatorInterface;
    
    public function getParam($name);
    
    public function setParam($name, $value): PaginatorInterface;
    
    public function init($items_on_page, $sortby, $sorttype, $grid_route): PaginatorInterface;

    public function getTotal(): ?int;
    
    public function setTotal($total): PaginatorInterface;

    public function getNumPages(): ?int;
    
    public function getSortBy(): string;
    
    public function getSortType(): string;
    
    public function getCurrPage(): int;
    
    public function getItemsOnPage(): int;
    
    public function isUse(): bool;
    
    public function getPrevUrl(): ?string;
}
