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

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Doctrine\Persistence\ManagerRegistry;
use sacrpkg\CrudBundle\Action\Render\RenderIntarface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Abstract class for action.
 */
abstract class ActionAbstract
{
    /**
     * Dispatcher object.
     *
     * @var EventDispatcherInterface
     */  
    protected $dispatcher;
    
    /**
     * ManagerRegistry object.
     *
     * @var ManagerRegistry
     */    
    protected $doctrine;
    
    /**
     * Grid title.
     *
     * @var string
     */    
    protected $title;
    
    /**
     * Form object.
     *
     * @var string
     */  
    protected $form_item;
    
    /**
     * Entity class name.
     *
     * @var string
     */    
    protected $entity;
    
    /**
     * Form class name.
     *
     * @var string
     */   
    protected $formclass;

    /**
     * Request data.
     *
     * @var RequestStack
     */   
    protected $request;
    
    /**
     * Route name for grid list.
     *
     * @var string
     */ 
    protected $homeroute;
    
    /**
     * Path to template file.
     *
     * @var string
     */ 
    protected $formview;
    
    /**
     * Custom grid parameters.
     *
     * @var array
     */    
    protected $params;
    
    /**
     * Breadcrumb array.
     *
     * @var array
     */      
	protected $breadcrumb;
    
    /**
     * Parameters for .
     *
     * @var array
     */  
	protected $homeparams = [];
    
    /**
     *
     * @var EntityManager
     */         
    protected $em;
    
    /**
     * Event before save data.
     *
     * @var function
     */  
    protected $beforesave;
    
    /**
     * Event before presist data.
     *
     * @var function
     */   
    protected $beforepersist;   

    /**
     * Event after save data.
     *
     * @var function
     */  
    protected $aftersave;
    
    /**
     * Event before load data.
     *
     * @var function
     */  
    protected $beforeinit;
    
    /**
     * Event before delete data.
     *
     * @var function
     */  
    protected $beforedelete;
    
    /**
     * @var RouterInterface
     */
    protected $router;
    
    /**
     * @var FormFactoryInterface
     */ 
    protected $formfactory;

    /**
     * Route name for edit form.
     *
     * @var string
     */ 
    protected $editroute;
    
    /**
     * @var RenderIntarface
     */
    protected $render;
    /**
     * Default sort type.
     *
     * @var SessionInterface
     */
    protected $session;
    
    /**
     * History messages.
     *
     * @var array
     */     
    protected $messages;

    /**
     * Execute operation with data
     *
     * @param array $params
     *
     * @return Response
     */
    abstract public function execute($params = []): Response;
    
    public function __construct(Request $request, ManagerRegistry $doctrine,
                RouterInterface $router)
    { 
        $this->request = $request;
        $this->doctrine = $doctrine;
        $this->em = $doctrine->getManager();
        $this->router = $router;
        $this->session = $this->request->getSession();
    }


    /**
     * Set form factory
     *
     * @param FormFactoryInterface $formfactory
     *
     * @return $this
     */
    public function setFormFactory(FormFactoryInterface $formfactory): self
    {
        $this->formfactory = $formfactory;
        
        return $this;
    }

    /**
     * Set render
     *
     * @param RenderIntarface $render
     *
     * @return $this
     */
    public function setRender(RenderIntarface $render): self
    {
        $this->render = $render;
        
        return $this;
    }

    /**
     * Set entity class name.
     *
     * @param string $entity
     *
     * @return $this
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }
    
    /**
     * Set template path.
     *
     * @param string $formview Template name 
     *
     * @return $this
     */
    public function setFormView($formview)
    {
        $this->formview = $formview;
        return $this;
    }
    
    /**
     * Set dispatcher.
     *
     * @param EventDispatcher $dispatcher  
     *
     * @return $this
     */
	public function setDispatcher(EventDispatcherInterface $dispatcher)
	{
        $this->dispatcher = $dispatcher;
        return $this;		
	}
	
    /**
     * Set EntityManager.
     *
     * @param EntityManager $em  
     *
     * @return $this
     */
    public function setEntityManager(EntityManager $em): self
    {
        $this->em = $em;
        return $this;
    }
    
    /**
     * Set params for template.
     *
     * @param string $name Parameter name 
     * @param string $value Parameter value 
     *
     * @return $this
     */
    public function setParam(string $name, $value): self
    {
        $this->params[$name] = $value;
        return $this;
    }

    /**
     * Get from object.
     *
     * @return form object
     */
    public function getFormItem()
    {
        return $this->form_item;
    }

    /**
     * Set form class name.
     *
     * @param string $formclass
     *
     * @return $this
     */
    public function setForm(string $formclass)
    {
        $this->formclass = $formclass;
        return $this;
    }
    
    /**
     * Set breadcrumb data.
     *
     * @param array $breadcrumb
     *
     * @return $this
     */
	public function setBreadcrumb(array $breadcrumb): self
	{
		$this->breadcrumb = $breadcrumb;
		return $this;
	}
    
    /**
     * Set title to form.
     *
     * @param string $title 
     *
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }
    
    /**
     * Set editroute.
     *
     * @param string $editroute 
     *
     * @return $this
     */
    public function setEditRoute(string $editroute): self
    {
		$this->editroute = $editroute;
		return $this;        
    }  

    /**
     * Set route to grid.
     *
     * @param string $homeroute 
     * @param array $homeparams 
     *
     * @return $this
     */
    public function setHomeRoute(string $homeroute, array $homeparams = []): self
    {
		$this->homeroute = $homeroute;
		$this->homeparams = $homeparams;
		return $this;        
    }

    /**
     * Set function for event before save data.
     *
     * @param function  $beforesave 
     *
     * @return $this
     */
    public function setBeforeSave($beforesave): self
    {
        $this->beforesave = $beforesave;
        return $this;
    }

    /**
     * Set function for event before delete data.
     *
     * @param function  $beforedelete 
     *
     * @return $this
     */
    public function setBeforeDelete($beforedelete): self
    {
        $this->beforedelete = $beforedelete;
        return $this;
    }

    /**
     * Set function for event before persist data.
     *
     * @param function  $beforepersist 
     *
     * @return $this
     */
    public function setBeforePersist($beforepersist): self
    {
        $this->beforepersist = $beforepersist;
        return $this;
    }

	public function saveFields($row, Request $request, $params = [])
	{
    }

    /**
     * Set function for event before load data.
     *
     * @param function  $beforeinit 
     *
     * @return $this
     */
    public function setBeforeInit($beforeinit): self
    {
        $this->beforeinit = $beforeinit;
        return $this;
    }

    /**
     * Set function for event after save data.
     *
     * @param function  $aftersave 
     *
     * @return $this
     */
    public function setAfterSave($aftersave): self
    {
        $this->aftersave = $aftersave;
        return $this;
    }

    /**
     * Get form object.
     *
     * @param string $formclass 
     * @param object $row 
     *
     * @return $this
     */
    protected function getForm(string $formclass, $row)
    {
        return $this->formfactory->create($this->formclass, $row, []);
    }

    /**
     * Render form.
     *
     * @param object $form 
     * @param object $formdata 
     *
     * @return Response
     */
    protected function renderForm($form, $formdata): Response
    {
        $formview = $this->formview ?: '@SacrpkgCrud/form/form_edit.html.twig';
        
        return $this->render->setForm($form)
                    ->setParams([
                            'title' => $this->title,
                            'home_route' => $this->homeroute,
                            'homeparams' => $this->homeparams,
                            'params' => $this->params,
                            'breadcrumb' => $this->breadcrumb ?? null,
                            'isDeleteBtn' => $formdata['params']['isDeleteBtn'] ?? false,
                            'isMarkDeleteBtn' => $formdata['params']['isMarkDeleteBtn'] ?? false,
                            'item' => $formdata['item'] ?? false
                    ])
                    ->getResponse($formview);
    }
    
    /**
     * Add message to session flash.
     *
     * @param string $type Type message
     * @param string $mes Message text
     *
     * @return $this
     */
    protected function flashMessage(string $type, string $mes): self
    {
        $messages[] = ['type' => $type, 'mes' => $mes];
        $this->session->getFlashBag()->add($type, $mes);
        
        return $this;
    }
    
    /**
     * Redirect to route.
     *
     * @param string $route
     * @param array $params
     *
     * @return RedirectResponse
     */
    protected function redirectToRoute(string $route, $params = []): RedirectResponse
    { 
        $url = $this->router->generate($route, $params, UrlGeneratorInterface::ABSOLUTE_PATH);
        return new RedirectResponse($url, 302);
    }
    
    protected function beforeSaveExecute(EntityManager $em, $item)
    {
        $func = $this->beforesave;
        if (is_callable($func)) {
            $res = $func($em, $item, $this->request, $this);
        }
        
        return $res ?? null;
    }
    
    protected function beforePersistExecute(EntityManager $em, $item)
    {
        $func = $this->beforepersist;
        if (is_callable($func)) {
            $res = $func($em, $item, $this->request, $this);
        }
        
        return $res ?? null;
    }  
    
    protected function beforeDeleteExecute(EntityManager $em, $item)
    {
        $func = $this->beforedelete;
        if (is_callable($func)) {
            $res = $func($em, $item, $this->request, $this);
        }
        
        return $res ?? null;
    }
    
    protected function afterSaveExecute(EntityManager $em, $item)
    {
        $func = $this->aftersave;
        if (is_callable($func)) {
           $res =  $func($em, $item, $this->request, $this);
        }
        
        return $res ?? null;
    } 

    protected function beforeInitExecute(EntityManager $em, $item)
    {
        $func = $this->beforeinit;
        if (is_callable($func)) {
            $res = $func($em, $item, $this->request, $this);
        }
        
        return $res ?? null;
    }
}
