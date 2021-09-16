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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use sacrpkg\CrudBundle\Model\Reader\DoctrineReader;
use Doctrine\Persistence\ManagerRegistry;
use sacrpkg\CrudBundle\Model\Render\RenderIntarface;

/**
 * Abstract base grid.
 */
abstract class BaseGrid extends GridAbstract
{
    public function __construct(RequestStack $requestStack, DoctrineReader $reader, ManagerRegistry $doctrine,
            PaginatorInterface $paginator, RouterInterface $router, FilterInterface $filter, RenderIntarface $render)
    { 
        parent::__construct($requestStack, $reader, $paginator,
                $router, $filter, $render);
    }
}
