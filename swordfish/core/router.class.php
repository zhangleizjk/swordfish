<?php
// declare(strict_types = 1);
namespace Swordfish;

class Router {
	
	/**
	 * default_access_module = 'home'
	 * default_access_controller = 'user'
	 * default_access_action = 'login'
	 * url_parse_model = 'rewrite'
	 * url_pathinfo_separator = '/'
	 */
	protected $module;
	protected $controller;
	protected $action;
	protected $params = array();
	protected $url;
	
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
		// echo '-swordfish-';
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
				break;
		}
	}
	
	/**
	 * protected void function rewrite(void)
	 */
	protected function rewrite(): void {
		$pathinfo = substr(rawurldecode(_i('server.PATH_INFO', '')), 1);
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



















