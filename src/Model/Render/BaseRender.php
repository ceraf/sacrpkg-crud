<?php

namespace sacrpkg\CrudBundle\Model\Render;

use Symfony\Component\HttpFoundation\Response;

class BaseRender extends RenderAbstract
{
    public function getReResponse(string $view): Response
    {
        $params = [
            'rows' => $this->collection,
            'fields' => $this->fields,
            'actions' => $this->actions,
            'buttons' => $this->buttons,
            'paginator' => $this->paginator,
            'filter' => $this->filter,

         //   'sortby' => $this->sortby,
         //   'sorttype' => strtolower($this->sorttype),
		//		'use_paginator' => $this->use_paginator,            
        ];   
        
        $params = array_merge($params, $this->params);
        /*
            'grid_route' => $this->grid_route,

                'title' => $this->title,

                'search' => $this->search,
                'usefilter' => $this->usefilter,
                
                'linestyles' => $this->linestyles,
				'params' => $this->params,
				'breadcrumb' => $this->breadcrumb,

				'edit_only' => $this->edit_only,
				'action_route' => $this->action_route,
                'use_checker' => $this->use_checker,

                */
        return new Response( $this->twig->render($view, $params));
    }
}
