<?php
declare(strict_types = 1)
	;

namespace Swift;

abstract class Controller {
	
	/**
	 */
	protected $view;
	
	/**
	 * void public function __construct(void)
	 */
	public function __construct() {
		$this->view = new \Swift\View();
	}
	
	/**
	 * void public function __destruct(void)
	 */
	public function __destruct() {
		//
	}
	
	/**
	 * void public function __set(string $name, mixed $value)
	 */
	public function __set(string $name, string $value) {
		$this->view->assign($name, $value);
	}
	
	/**
	 * boolean public function __isset(string $name)
	 */
	public function __isset($name): bool {
		return is_null($this->view->get($name)) ? false : true;
	}
	
	/**
	 * void public function display(string $data, string $type = null, string $charset = null)
	 */
	public function display(string $data, string $type = null, string $charset = null) {
		$this->view->display($data, $type, $charset);
	}
	
	/**
	 * public function output(string $data, string $type = null, string $charset = null)
	 */
	public function output(string $data, $type = null, $charset = null) {
		$this->view->output($data, $type, $charset);
	}
	
	/**
	 * string public function fetch(string $url)
	 */
	public function fetch(string $url): string {
		return $this->view->fetch($url);
	}
	
	/**
	 * public function ajax($data, $type)
	 */
	public function ajax($data, $type = null) {
		$type = is_null($type) ? C('ajax_default_type') : strtolower($type);
		switch ($type) {
			case 'json' :
				header('Content-Type:application/json; charset=utf-8');
				die(json_encode($data));
				break;
			default :
				//
				break;
		}
	}
	
	/**
	 * boolean public function assign(string $name, mixed $value)
	 */
	public function assign(string $name, $value): bool {
		return $this->view->assign($name, $value);
	}
	
	/**
	 * integer public function assigns(array $vars)
	 */
	public function assigns(array $vars): int {
		return $this->view->assigns($vars);
	}
	//
}
