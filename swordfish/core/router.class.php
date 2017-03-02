<?php
// declare(strict_types = 1);
namespace Swift;

class Router {
	
	/**
	 * default_access_module = 'home'
	 * default_access_controller = 'user'
	 * default_access_action = 'login'
	 * url_group_model =' rewrite'
	 * url_pathinfo_separator = '/'
	 */
	protected $identifier = '[a-z][a-z0-9_]*';
	protected $module;
	protected $controller;
	protected $action;
	protected $params = array();
	protected $url;
	
	/**
	 * void public function __construct(void)
	 */
	public function __construct() {
		$this->module = get_config('default_access_module', 'home');
		$this->controller = get_config('default_access_controller', 'user');
		$this->action = get_config('default_access_action', 'hello');
		$this->url = implode('.', array($this->module, $this->controller, $this->action));
	}
	
	/**
	 * void public function __destruct(void)
	 */
	public function __destruct() {
		// echo '-Equairo-';
	}
	
	/**
	 * void public function navigate(void)
	 */
	public function navigate(): void {
		_a($this->url, $this->params);
	}
	
	/**
	 * void protected function url(void)
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
	 * void protected function rewrite(void)
	 */
	protected function rewrite(): void {
		$url = rawurldecode(_i('server.' . strtoupper('path_info')));
		$separator = get_config('url_pathinfo_separator', '/');
		$pattern = '/^\/' . $this->identifier . '(' . $separator . $this->identifier . '){2}(' . $separator . '[^' . $separator . ']+)*$/si';
		if(! is_null($url) && preg_match($pattern, $url)){
			$datas = explode($separator, substr($url, 1));
			list($this->module, $this->controller, $this->action) = $datas;
			$this->params = array_slice($datas, 3);
			$this->url = implode('.', $datas);
		}
	}
	//
}



















