<?php
// declare(strict_types = 1);
namespace Swordfish;

class Core {
	
	/**
	 * void public function __construct(void)
	 */
	public function __construct() {
		//
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
		$this->getSysFuncs()->getSysCores()->getSysLibs()->getSysConfigs();
	}
	
	/**
	 * Core protected function getSysFuncs()(void)
	 */
	protected function getSysFuncs(): Core {
		$layer = 'func';
		$file = 'swift.func.php';
		$fullName = implode('/', array(swordfish_path, $layer, $file));
		if(is_file($fullName)) @include_once $fullName;
		return $this;
	}
	
	/**
	 * void protected function getClasses(string $dir)
	 */
	protected function getClasses(string $dir): void {
		$pattern = '/^[a-z]+(_[a-z]+)*\.class\.php$/';
		$path = implode('/', array(swordfish_path, $dir));
		if(is_dir($path)){
			foreach(scandir($path) as $file){
				$fullName = implode('/', array($path, $file));
				if(preg_match($pattern, $file) && is_file($fullName)) @include_once $fullName;
			}
		}
	}
	
	/**
	 * Core protected function getSysCores(void)
	 */
	protected function getSysCores(): Core {
		$layer = 'core';
		$this->getClasses($layer);
		return $this;
	}
	
	/**
	 * Core protected function getSysLibs(void)
	 */
	protected function getSysLibs(): Core {
		$layer = 'lib';
		$this->getClasses($layer);
		return $this;
	}
	
	/**
	 * Core protected function getSysConfigs(void)
	 */
	protected function getSysConfigs(): Core {
		$layer = 'conf';
		$file = 'swift.conf.php';
		$fullName = implode('/', array(swordfish_path, $layer, $file));
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












