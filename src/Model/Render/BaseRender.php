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

use Symfony\Component\HttpFoundation\Response;

class BaseRender extends RenderAbstract
{
    public function getResponse(string $view): Response
    {
        $params = [
            'rows' => $this->collection,
            'fields' => $this->fields,
            'actions' => $this->actions,
            'buttons' => $this->buttons,
            'paginator' => $this->paginator,
            'filter' => $this->filter,            
        ];   
        
        $params = array_merge($params, $this->params);

        return new Response( $this->twig->render($view, $params));
    }
}
