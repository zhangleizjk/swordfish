<?php
// declare(strict_types = 1);
namespace Swordfish;

class Router {
	
	/**
	 * default_access_module = 'home'
	 * default_access_controller = 'user'
	 * default_access_action = 'hello'
	 * url_parse_model = 'rewrite'
	 * url_pathinfo_separator = '/'
	 * static_route_rules = array()
	 * pattern_route_rules = array()
	 */
	protected $url;
	protected $module;
	protected $controller;
	protected $action;
	protected $params = array();
	
	/**
	 * public void function __construct(void)
	 */
	public function __construct() {
		$this->module = get_config('default_access_module', 'home');
		$this->controller = get_config('default_access_controller', 'user');
		$this->action = get_config('default_access_action', 'hello');
		$this->url = $this->module . '.' . $this->controller . '.' . $this->action;
	}
	
	/**
	 * public void function __destruct(void)
	 */
	public function __destruct() {
		// echo '-Destory-';
	}
	
	/**
	 * public void function navigate(void)
	 */
	public function navigate(): void {
		$this->url();
		_a($this->url, $this->params);
	}
	
	/**
	 * protected void function url(void)
	 */
	protected function url(): void {
		$model = get_config('url_parse_model', 'rewrite');
		switch($model){
			case 'rewrite':
				$this->rewrite();
				break;
			default:
				die(_msg('Sorry, url parse model configuration error. #_#'));
				break;
		}
	}
	
	/**
	 * protectd string function map(void)
	 */
	protected function map(): string {
		$url = substr(rawurldecode(_i('server.PATH_INFO', '')), 1);
		$rules = get_config('static_route_rules', array());
		if(isset($rules[$url])) return (string)$rules[$url];
		else return $url;
	}
	
	/**
	 * protected void function rewrite(void)
	 */
	protected function rewrite(): void {
		$pathinfo = $this->map();
		$separator = get_config('url_pathinfo_separator', '/');
		$children = explode($separator, $pathinfo);
		if(count($children) >= 3){
			$url = implode('.', array_slice($children, 0, 3));
			if(is_url_regular($url)){
				list($this->module, $this->controller, $this->action) = $children;
				$this->params = array_slice($children, 3);
				$this->url = $url;
			}
		}
	}
	//
}



















