<?php

namespace sacrpkg\CrudBundle\Model;

use Symfony\Component\HttpFoundation\RequestStack;

class Filter
{
    const SESSION_FILTER = 'filter';
    
    private $request;
    private $session;
    private $data = [];
    private $settings;
    
    public function __construct (RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->session = $this->request->getSession();
    }
    
    public function init($settings, $data): void
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
    }
    
    public function getData(): ?array
    {
        return $this->data;
    }
    
    public function isUseFilter(): bool
    {
        return !is_null($this->data);
    }
    
    public function hasField($field_name)
    {
        return isset($this->data[$field_name]);
    }
    
    protected function getFromSession($name)
    {
        return $this->session->get((new \ReflectionClass($this))->getShortName().'_'.$name);
    }
    
    protected function setToSession($name, $value)
    {
        $this->session->set((new \ReflectionClass($this))->getShortName().'_'.$name, $value);
        return $this;
    }
}
