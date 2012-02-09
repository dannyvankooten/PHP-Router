<?php

class Route {
	
	/**
	* URL of this Route
	* @var string
	*/
	private $url;

	/**
	* Accepted HTTP methods for this route
	* @var array
	*/
	private $methods = array('GET','POST','PUT','DELETE');

	/**
	* Target for this route, can be anything.
	* @var mixed
	*/
	private $target;

	/**
	* The name of this route, used for reversed routing
	* @var string
	*/
	private $name;

	/**
	* Custom parameter filters for this route
	* @var array
	*/
	private $filters = array();

	public function getUrl() {
		return $this->url;
	}

	public function setUrl($url) {
		$this->url = $url;
	}

	public function getTarget() {
		return $this->target;
	}

	public function setTarget($target) {
		$this->target = $target;
	}

	public function getMethods() {
		return $this->methods;
	}

	public function setMethods(array $methods) {
		$this->methods = $methods;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = (string) $name;
	}

	public function setFilters(array $filters) {
		$this->filters = $filters;
	}

	public function getRegex() {
		return preg_replace_callback("/:(\w+)/", array(&$this, 'substituteFilter'), $this->url);
	}

	private function substituteFilter($matches) {
		if (isset($matches[1]) && isset($this->filters[$matches[1]])) {
            return $this->filters[$matches[1]];
        }
        
        return "(\w+)";
	}




}