<?php
// declare(strict_types = 1);
namespace Swift;

class App {
	/**
	 */
	protected $common;
	
	/**
	 * void public function __construct(void)
	 */
	public function __construct() {
		$this->common = get_config('app_common_layer', '_common');
	}
	
	/**
	 * void public function __destruct(void)
	 */
	public function __destruct() {
		clearstatcache();
	}
	
	/**
	 * void public function fire(void)
	 */
	public function fire(): void {
		$this->getAppFuncs()->getAppLibs()->getAppConfigs();
	}
	
	/**
	 * App protected function getAppFuncs(void)
	 */
	protected function getAppFuncs(): App {
		$layer = get_config('app_function_layer', 'func');
		$file = get_config('app_function_file', 'app.func.php');
		$fullName = implode('/', array(app_path, $this->common, $layer, $file));
		if(is_file($fullName)) @include_once $fullName;
		return $this;
	}
	
	/**
	 * App protected function getAppLibs(void)
	 */
	protected function getAppLibs(): App {
		$layer = get_config('app_library_layer', 'lib');
		$pattern = '/^[a-z]+(_[a-z]+)*\.class\.php$/';
		$path = implode('/', array(app_path, $this->common, $layer));
		if(is_dir($path)){
			foreach(scandir($path) as $file){
				$fullName = implode('/', array($path, $file));
				if(preg_match($pattern, $file) && is_file($fullName)) @include_once $fullName;
			}
		}
		return $this;
	}
	
	/**
	 * App protected function getAppConfigs(void)
	 */
	protected function getAppConfigs(): App {
		$layer = get_config('app_config_layer', 'conf');
		$file = get_config('app_config_file', 'app.conf.php');
		$fullName = implode('/', array(app_path, $this->common, $layer, $file));
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