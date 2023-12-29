<?php

/*
 * This file is part of the Sacrpkg CrudBundle package.
 *
 * (c) Oleg Bruyako <jonsm2184@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace sacrpkg\CrudBundle\Action\Render;

use Symfony\Component\HttpFoundation\Response;

class BaseRender extends RenderAbstract
{
    public function getResponse(string $view): Response
    {
        /*
        $formdata['form'] = $form->createView();
        $formdata['title'] = $this->title;
        $formdata['home_route'] = $this->homeroute;
        $formdata['isDeleteBtn'] = $formdata['params']['isDeleteBtn'] ?? false;
        $formdata['isMarkDeleteBtn'] = $formdata['params']['isMarkDeleteBtn'] ?? false;
        $formdata['breadcrumb'] = $this->breadcrumb ?? null;
        
        $this->formview = $this->formview ?: 'Admin/form/form_edit.html.twig';
        
        return $this->controller->renderCrudForm($formdata, $this->formview);
        */
        /*
        $view_form = $this->form->createView();
        
        foreach ($view_form->getIterator() as $item) {
            var_dump( get_class_methods($item));
        }
        */
        
        $params = [
            'form' => $this->form->createView(),
            
            
            /*
            'rows' => $this->collection,
            'fields' => $this->fields,
            'actions' => $this->actions,
            'buttons' => $this->buttons,
            'paginator' => $this->paginator,
            'filter' => $this->filter, 
*/            
        ];   
        
        $params = array_merge($params, $this->params);

        return new Response( $this->twig->render($view, $params));
    }
}
