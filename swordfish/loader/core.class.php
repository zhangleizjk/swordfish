<?php
// declare(strict_types = 1);
namespace Swordfish;

class Core {
	
	/**
	 * public void function __construct(void)
	 */
	public function __construct() {
		// echo '-INIT-';
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
		$this->getSysFuncs()->getSysCores()->getSysLibraries()->getSysConfigs();
	}
	
	/**
	 * protected Core function getSysFuncs()(void)
	 */
	protected function getSysFuncs(): Core {
		$directory = 'func';
		$fileName = 'swordfish.func.php';
		$fullName = implode('/', array(swordfish_path, $directory, $fileName));
		if(is_file($fullName)) @include_once $fullName;
		return $this;
	}
	
	/**
	 * protected void function getClasses(string $path)
	 */
	protected function getClasses(string $path): void {
		if(is_dir($path)){
			foreach(scandir($path) as $fileName){
				$fullName = implode('/', array($path, $fileName));
				if(is_class_named_regular($fileName) && is_file($fullName)) @include_once $fullName;
			}
		}
	}
	
	/**
	 * protected Core function getSysCores(void)
	 */
	protected function getSysCores(): Core {
		$directory = 'core';
		$path = implode('/', array(swordfish_path, $directory));
		$this->getClasses($path);
		return $this;
	}
	
	/**
	 * protected Core function getSysLibraries(void)
	 */
	protected function getSysLibraries(): Core {
		$directory = 'lib';
		$path = implode('/', array(swordfish_path, $directory));
		$this->getClasses($path);
		return $this;
	}
	
	/**
	 * protected Core function getSysConfigs(void)
	 */
	protected function getSysConfigs(): Core {
		$directory = 'conf';
		$fileName = 'swordfish.conf.php';
		$fullName = implode('/', array(swordfish_path, $directory, $fileName));
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