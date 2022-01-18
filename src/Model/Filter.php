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

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

/**
 * Base filter class.
 */
class Filter implements FilterInterface
{
    const SESSION_FILTER = 'filter';
    
    /**
     * Request data.
     *
     * @var RequestStack
     */       
    protected $request;
    
    /**
     * Default sort type.
     *
     * @var SessionInterface
     */
    protected $session;
    
    /**
     * Filter data.
     *
     * @var array
     */  
    protected $data = [];
    
    /**
     * Filter fields.
     *
     * @var array
     */  
    protected $settings;
    
    /**
     * Route name for grid list.
     *
     * @var string
     */ 
    protected $grid_route;
    
    /**
     * @var RouterInterface
     */
    protected $router;

    public function __construct (RequestStack $requestStack, RouterInterface $router)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->session = $this->request->getSession();
        $this->router = $router;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setGridRoute(string $route): FilterInterface
    {
        $this->grid_route = $route;
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function addData($name, $value): self
    {
        $this->data[$name] = $value;
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFilterUrl(): string
    {
        return $this->router->generate($this->grid_route);
    }
    
    /**
     * {@inheritdoc}
     */
    public function init(?array $settings, ?array $data): FilterInterface
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
    
    /**
     * {@inheritdoc}
     */
    public function getData(): ?array
    {
        return $this->data;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isUseFilter(): bool
    {
        return !is_null($this->settings);
    }
    
    /**
     * {@inheritdoc}
     */
    public function hasField($field_name): bool
    {
        return isset($this->data[$field_name]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getSettings(): ?Array
    {
        return $this->settings;
    }
    
    /**
     * Get filter data from session.
     *
     * @param string $name Parameter name
     *
     * @return mixed
     */
    protected function getFromSession($name)
    {
        return $this->session->get($this->getSessionPrefix().'_'.$name);
    }
    
    /**
     * Set filter data to session.
     *
     * @param string $name Parameter name
     * @param string $value Parameter value
     *
     * @return FilterInterface
     */
    protected function setToSession($name, $value): FilterInterface
    {
        $this->session->set($this->getSessionPrefix().'_'.$name, $value);
        return $this;
    }
    
    /**
     * Get grid session prefix.
     *
     * @return string
     */
    protected function getSessionPrefix(): string
    { 
        $url = $this->request->server->get('REQUEST_URI');
        if (strpos($url, '?') !== false) {
            $url = substr($url, 0, strpos($url, '?'));
        }
        return str_replace('/', '', $url).'_'.(new \ReflectionClass($this))->getShortName();
    }
}
