<?php

namespace sacrpkg\CrudBundle\Model;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class Filter implements FilterInterface
{
    const SESSION_FILTER = 'filter';
    
    protected $request;
    protected $session;
    protected $data = [];
    protected $settings;
    protected $grid_route;
    protected $router;

    public function __construct (RequestStack $requestStack, RouterInterface $router)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->session = $this->request->getSession();
        $this->router = $router;
    }
    
    public function setGridRoute(string $route): FilterInterface
    {
        $this->grid_route = $route;
        
        return $this;
    }
    
    public function getFilterUrl(): string
    {
        return $this->router->generate($this->grid_route);
    }
    
    public function init($settings, $data): FilterInterface
    {
        $this->settings = $settings;
		$currfilter = $data;
        if ($this->request->get('filter_apply', null)) {
            $filter = $this->request->get('filter');
            $this->setToSession(self::SESSION_FILTER, $filter);
        } elseif ($this->request->get('filter_reset', null)) {
            $filter = null;
            $this->setToSession(self::SESSION_FILTER, $filter);
        } else
            $filter = ($this->getFromSession(self::SESSION_FILTER)) ?? null;
        $this->data = $filter;
		if ($currfilter) {
			if (is_array($this->data))
				$this->data = array_merge($this->data, $currfilter);
			else
				$this->data = $currfilter;
		}
        
        return $this;
    }
    
    public function getData(): ?array
    {
        return $this->data;
    }
    
    public function isUseFilter(): bool
    {
        return !is_null($this->settings);
    }
    
    public function hasField($field_name): bool
    {
        return isset($this->data[$field_name]);
    }
    
    public function getSettings(): ?Array
    {
        return $this->settings;
    }
    
    protected function getFromSession($name)
    {
        return $this->session->get($this->getSessionPrefix().'_'.$name);
    }
    
    protected function setToSession($name, $value)
    {
        $this->session->set($this->getSessionPrefix().'_'.$name, $value);
        return $this;
    }
    
    protected function getSessionPrefix()
    { 
        $url = $this->request->server->get('REQUEST_URI');
        if (strpos($url, '?') !== false) {
            $url = substr($url, 0, strpos($url, '?'));
        }
        return str_replace('/', '', $url).'_'.(new \ReflectionClass($this))->getShortName();
    }
}
