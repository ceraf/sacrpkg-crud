<?php

/*
 * This file is part of the Sacrpkg CrudBundle package.
 *
 * (c) Oleg Bruyako <jonsm2184@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace sacrpkg\CrudBundle\Action;

use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use sacrpkg\CrudBundle\Action\Render\RenderIntarface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Factory for action.
 */
class ActionFactory implements ActionFactoryInterface
{
    /**
     * Request data.
     *
     * @var RequestStack
     */    
    private $request;
    
    /**
     * @var ManagerRegistry
     */ 
    private $doctrine;
    
    /**
     * @var FormFactoryInterface
     */ 
    private $formfactory;
    
    /**
     * @var ContainerBagInterface
     */
    private $params;
    
    /**
     * @var RenderIntarface
     */    
    private $render;

    public function __construct(ManagerRegistry $doctrine, RequestStack $requestStack,
            FormFactoryInterface $formfactory, RouterInterface $router, ContainerBagInterface $params,
            RenderIntarface $render, EventDispatcherInterface $dispatcher)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->formfactory = $formfactory;
        $this->router = $router;
        $this->doctrine = $doctrine;
        $this->params = $params;
        $this->render = $render;
        $this->dispatcher = $dispatcher;
    }
    
    /**
     * {@inheritdoc}
     */
    public function get($action): ActionAbstract
    {
        $configlists = $this->params->get('crud_actions') ?? null;

        $classname = $configlists[$action]['class'];

        $action = new $classname($this->request, $this->doctrine, $this->router);
        $action->setFormFactory($this->formfactory)
                ->setRender($this->render)
                ->setDispatcher($this->dispatcher);
                
        return $action;
    }
}
