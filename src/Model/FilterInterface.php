<?php

namespace sacrpkg\CrudBundle\Model;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

interface FilterInterface
{
    public function __construct (RequestStack $requestStack, RouterInterface $router);
    
    public function init($settings, $data): FilterInterface;
    
    public function getData(): ?array;
    
    public function setGridRoute(string $route): FilterInterface;
    
    public function getFilterUrl(): string;
    
    public function isUseFilter(): bool;

    public function hasField($field_name): bool;
    
    public function getSettings(): ?Array;
}
