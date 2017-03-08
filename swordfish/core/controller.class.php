<?php
// declare(strict_types = 1);
namespace Swordfish;

abstract class Controller {
	
	/**
	 * default_ajax_type = 'json'
	 */
	protected $view;
	
	/**
	 * public void function __construct(void)
	 */
	public function __construct() {
		$this->view = new View();
	}
	
	/**
	 * public void function __destruct(void)
	 */
	public function __destruct() {
		// echo '-Destory-';
	}
	
	/**
	 * public void function __set(string $name, ?mixed $value)
	 */
	public function __set(string $name, string $value) {
		$this->view->assign($name, $value);
	}
	
	/**
	 * public boolean function __isset(string $name)
	 */
	public function __isset($name): bool {
		return is_null($this->view->get($name)) ? false : true;
	}
	
	/**
	 * public void function display(?string $url = null, ?string $doctype = null, ?string $charset = null)
	 */
	public function display(string $url = null, string $doctype = null, string $charset = null): void {
		$this->view->display($url, $doctype, $charset);
	}
	
	/**
	 * public void function show(string $data, ?string $doctype = null, ?string $charset = null)
	 */
	public function show(string $data, $type = null, $charset = null): void {
		$this->view->show($data, $type, $charset);
	}
	
	/**
	 * public string function fetch(?string $url = null)
	 */
	public function fetch(string $url = null): string {
		return $this->view->fetch($url);
	}
	
	/**
	 * public function ajax(string $data, ?string $type = null)
	 */
	public function ajax($data, $type = null) {
		$type = $type ?? get_config('default_ajax_type', 'json');
		switch($type){
			case 'json':
				header('Content-Type:application/json;charset=utf-8');
				die(json_encode($data));
				break;
			case 'eval':
				header('Content-Type:text/plain;charset=utf-8');
				die($data);
			default:
				header('Content-Type:text/plain;charset=utf-8');
				die('Sorry, ajax return type error. #_#');
				break;
		}
	}
	
	/**
	 * public boolean function assign(string $name, ?mixed $value)
	 */
	public function assign(string $name, $value): bool {
		return $this->view->assign($name, $value);
	}
	
	/**
	 * public integer function assigns(array $vars)
	 * @$vars = [string $name => ?mixed $value]
	 */
	public function assigns(array $vars): int {
		return $this->view->assigns($vars);
	}
	//
}