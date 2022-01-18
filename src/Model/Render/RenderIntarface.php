<?php

/*
 * This file is part of the Sacrpkg CrudBundle package.
 *
 * (c) Oleg Bruyako <jonsm2184@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace sacrpkg\CrudBundle\Model\Render;

use Symfony\Component\HttpFoundation\RequestStack;
use sacrpkg\CrudBundle\Model\Paginator;
use sacrpkg\CrudBundle\Model\Filter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface for the render.
 */
interface RenderIntarface
{
    public function setCollection(?array $collection): RenderIntarface;
    
    public function setActions(?array $actions): RenderIntarface;
    
    public function setFields(?array $fields): RenderIntarface;
    
    public function setButtons(?array $buttons): RenderIntarface;
    
    public function setPaginator(?Paginator $paginator): RenderIntarface;
    
    public function setFilter(?Filter $filter): RenderIntarface;
    
    public function setParams(?array $params): RenderIntarface;
    
    public function getResponse(string $view): Response;
}
