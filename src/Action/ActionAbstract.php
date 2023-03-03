<?php

namespace sacrpkg\CrudBundle\Action;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Doctrine\Persistence\ManagerRegistry;

abstract class ActionAbstract
{
    protected $title;
    protected $entity;
    protected $controller;
    protected $formclass;
    protected $actionname;
    protected $request;
    protected $homeroute;
	protected $dispatcher;
    protected $formview;
    protected $params;
	protected $breadcrumb;
	protected $homeparams = [];
    protected $em;
    protected $beforesave;
    protected $beforepersist;    
    protected $aftersave;
    protected $beforeinit;
    protected $messages;
    protected $formfactory;
    
    abstract public function execute($params = []);
    
    public function __construct(Request $request, ManagerRegistry $doctrine,
                AbstractController $controller, RouterInterface $router)
    { 
        $this->request = $request;
        $this->controller = $controller;
        $this->em = $doctrine->getManager();
        $this->router = $router;
        return $this;
    }

    public function setFormFactory(FormFactoryInterface $formfactory): self
    {
        $this->formfactory = $formfactory;
        
        return $this;
    }

    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }
    
    public function setFormView($formview)
    {
        $this->formview = $formview;
        return $this;
    }
    
	public function setDispatcher(EventDispatcherInterface $dispatcher)
	{
        $this->dispatcher = $dispatcher;
        return $this;		
	}
	
    public function setEntityManager(EntityManager $em): self
    {
        $this->em = $em;
        return $this;
    }
    
    public function setParam(string $name, $value)
    {
        $this->params[$name] = $value;
        return $this;
    }

    public function setForm($formclass)
    {
        $this->formclass = $formclass;
        return $this;
    }
    
	public function setBreadcrumb(array $breadcrumb): self
	{
		$this->breadcrumb = $breadcrumb;
		return $this;
	}
	
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setHomeRoute($homeroute, $homeparams = [])
    {
		$this->homeroute = $homeroute;
		$this->homeparams = $homeparams;
		return $this;        
    }

    public function setBeforeSave($beforesave): self
    {
        $this->beforesave = $beforesave;
        return $this;
    }

    public function setBeforePersist($beforepersist): self
    {
        $this->beforepersist = $beforepersist;
        return $this;
    }

	public function saveFields($row, Request $request, $params = [])
	{
    }

    public function setBeforeInit($beforeinit): self
    {
        $this->beforeinit = $beforeinit;
        return $this;
    }

    public function setAfterSave($aftersave): self
    {
        $this->aftersave = $aftersave;
        return $this;
    }

    protected function getForm(string $formclass, $row)
    {
        return $this->formfactory->create($this->formclass, $row, []);
    }

    protected function renderForm($form, $formdata): Response
    {
        $formdata['form'] = $form->createView();
        $formdata['title'] = $this->title;
        $formdata['home_route'] = $this->homeroute;
        $formdata['home_params'] = $this->homeparams;
        $formdata['isDeleteBtn'] = $formdata['params']['isDeleteBtn'] ?? false;
        $formdata['isMarkDeleteBtn'] = $formdata['params']['isMarkDeleteBtn'] ?? false;
        $formdata['breadcrumb'] = $this->breadcrumb ?? null;
        
        $this->formview = $this->formview ?: 'Admin/form/form_edit.html.twig';
        
        return $this->controller->renderForm($formdata, $this->formview);
    }

    protected function flashMessage($type, $mes): self
    {
        $messages[] = ['type' => $type, 'mes' => $mes];
        if (method_exists($this->controller, 'displayMessage')) {
            $this->controller->displayMessage($type, $mes);
        }
        
        return $this;
    }
    
    protected function beforeSaveExecute(EntityManager $em, $item)
    {
        $func = $this->beforesave;
        if (is_callable($func)) {
            $res = $func($em, $item, $this->request);
        }
        
        return $res ?? null;
    }
    
    protected function beforePersistExecute(EntityManager $em, $item): self
    {
        $func = $this->beforepersist;
        if (is_callable($func)) {
            $func($em, $item, $this->request);
        }
        
        return $this;
    }  
    
    protected function afterSaveExecute(EntityManager $em, $item): self
    {
        $func = $this->aftersave;
        if (is_callable($func)) {
            $func($em, $item);
        }
        
        return $this;
    } 
    
    protected function redirectToRoute($route, $home_params = [])
    { 
        $url = $this->router->generate($route, $home_params, UrlGeneratorInterface::ABSOLUTE_PATH);
        return new RedirectResponse($url, 302);
    }

    protected function beforeInitExecute(EntityManager $em, $item): self
    {
        $func = $this->beforeinit;
        if (is_callable($func)) {
            $func($em, $item);
        }
        
        return $this;
    }
    
    
}
