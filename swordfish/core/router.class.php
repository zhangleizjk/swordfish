<?php
// declare(strict_types = 1);
namespace Swordfish;

class Router {
	
	/**
	 * default_access_module = 'home'
	 * default_access_controller = 'user'
	 * default_access_action = 'login'
	 * url_group_model =' rewrite'
	 * url_pathinfo_separator = '/'
	 */
	protected $namedChild = '[a-z]+(_[a-z]+)*';
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
		// echo '-destory-';
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
		$model = get_config('url_group_model', 'rewrite');
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
		//$url = rawurldecode(_i('server.PATH_INFO'));
		$url=$_SERVER['PATH_INFO'];
		$separator = get_config('url_pathinfo_separator', '/');
		$pattern = '/^\/' . $this->namedChild . '(\\' . $separator . $this->namedChild . '){2}.*$/';
		if(!is_null($url) && preg_match($pattern, $url)){
			$datas = explode($separator, substr($url, 1));
			list($this->module, $this->controller, $this->action) = $datas;
			$this->params = array_slice($datas, 3);
			$this->url = $this->module . '.' . $this->controller . '.' . $this->action;
		}
	}
	//
}



















