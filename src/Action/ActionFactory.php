<?php

namespace sacrpkg\CrudBundle\Action;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class ActionFactory implements ActionFactoryInterface
{
    const ACTION_CLASESS = [
        'add' => ['class' => AddAction::class],
        'addmore' => ['class' => AddMoreAction::class],
        'edit' => ['class' => EditAction::class],
        'delete' => ['class' => DeleteAction::class],
    ];
    
    private $controller;
    private $request;
    private $doctrine;
    private $session;
    private $formfactory;
    private $params;

    public function __construct(ManagerRegistry $doctrine, RequestStack $requestStack,
            FormFactoryInterface $formfactory, RouterInterface $router, ContainerBagInterface $params)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->formfactory = $formfactory;
        $this->router = $router;
        $this->doctrine = $doctrine;
        $this->params = $params;
    }
    
    public function setController(AbstractController $controller): self
    {
        $this->controller = $controller;
        
        return $this;
    }
    /*
    public function __construct (RequestStack $requestStack, ValidatorInterface $validator)
    {
        $this->doctrine = $doctrine;
        $this->request = $requestStack->getCurrentRequest();
        $this->validator = $validator;
    }    
    */
    public function get($action): ActionAbstract
    {
        $classlists = self::ACTION_CLASESS;
        $configlists = $this->params->get('crud_actions') ?? null;
        if ($configlists) {
            $classlists = array_merge($classlists, $configlists);
        }
        $classname = $classlists[$action]['class'];

        $action = new $classname($this->request, $this->doctrine, $this->controller, $this->router);
        $action->setFormFactory($this->formfactory);
        return $action;
    }
}
