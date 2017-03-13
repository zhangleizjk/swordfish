<?php
// declare(strict_types = 1);
namespace Swordfish;

class App {
	/**
	 * app_common_layer = '_common'
	 * app_function_layer = 'func'
	 * app_function_doc = 'app.func.php'
	 * app_library_layer = 'lib'
	 * app_config_layer = 'conf'
	 * app_config_doc = 'app.conf.php'
	 */
	protected $common;
	
	/**
	 * public void function __construct(void)
	 */
	public function __construct() {
		$this->common = get_config('app_common_layer', '_common');
	}
	
	/**
	 * public void function __destruct(void)
	 */
	public function __destruct() {
		clearstatcache();
	}
	
	/**
	 * public void function fire(void)
	 */
	public function fire(): void {
		$this->getAppFuncs()->getAppLibraries()->getAppConfigs();
	}
	
	/**
	 * protected App function getAppFuncs(void)
	 */
	protected function getAppFuncs(): App {
		$directory = get_config('app_function_layer', 'func');
		$fileName = get_config('app_function_doc', 'app.func.php');
		$fullName = implode('/', array(app_path, $this->common, $directory, $fileName));
		if(is_file($fullName)) @include_once $fullName;
		return $this;
	}
	
	/**
	 * protected App function getAppLibraries(void)
	 */
	protected function getAppLibraries(): App {
		$directory = get_config('app_library_layer', 'lib');
		$path = implode('/', array(app_path, $this->common, $directory));
		if(is_dir($path)){
			foreach(scandir($path) as $fileName){
				$fullName = implode('/', array($path, $fileName));
				if(is_class_named_regular($fileName) && is_file($fullName)) @include_once $fullName;
			}
		}
		return $this;
	}
	
	/**
	 * protected App function getAppConfigs(void)
	 */
	protected function getAppConfigs(): App {
		$directory = get_config('app_config_layer', 'conf');
		$fileName = get_config('app_config_doc', 'app.conf.php');
		$fullName = implode('/', array(app_path, $this->common, $directory, $fileName));
		if(is_file($fullName)){
			$configs = @include_once $fullName;
			if(is_array($configs)){
				foreach($configs as $key => $config){
					set_config($key, $config);
				}
			}
		}
		return $this;
	}
	//
}