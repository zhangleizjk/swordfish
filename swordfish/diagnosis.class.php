<?php
// declare(strict_types = 1);
namespace Swordfish;

class Diagnosis {
	/**
	 */
	protected $errMessages = array();
	
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
	 * public array function fire(void)
	 */
	public function fire(): array {
		$this->clearMessages();
		$this->folder()->func()->core()->library()->config()->resource();
		return $this->errMessages;
	}
	
	/**
	 * protected Diagnosis function folder(void)
	 */
	protected function folder(): Diagnosis {
		$folders = array('loader', 'func', 'core', 'lib', 'conf', 'resource');
		foreach($folders as $folder){
			$path = implode('/', array(swordfish_path, $folder));
			if(!is_dir($path)) $this->errMessages[] = 'Sorry, system folder: [' . $folder . '] NO found. #_#';
		}
		return $this;
	}
	
	/**
	 * protected Diagnosis function func(void)
	 */
	protected function func(): Diagnosis {
		$folder = 'func';
		$files = array('swift.func.php');
		foreach($files as $file){
			$path = implode('/', array(swordfish_path, $folder, $file));
			if(!is_file($path)) $this->errMessages[] = 'Sorry, system function file: [' . $file . '] No found. #_#';
		}
		return $this;
	}
	
	/**
	 * protected Diagnosis function core(void)
	 */
	protected function core(): Diagnosis {
		$folder = 'core';
		$files = array('router', 'controller', 'model', 'mysql', 'validator', 'view', 'processor', 'unique');
		$extra = '.class.php';
		foreach($files as $file){
			$path = implode('/', array(swordfish_path, $folder, $file . $extra));
			if(!is_file($path)) $this->errMessages[] = 'Sorry, system core library file: [' . $file . $extra . '] NO found. #_#';
		}
		return $this;
	}
	
	/**
	 * protected Diagnosis function library(void)
	 */
	protected function library(): Diagnosis {
		$folder = 'lib';
		$files = array();
		$extra = '.class.php';
		foreach($files as $file){
			$path = implode('/', array(swordfish_path, $folder, $file . $extra));
			if(!is_file($path)) $this->errMessages[] = 'Sorry, system extra library file: [' . $file . $extra . '] NO found. #_#';
		}
		return $this;
	}
	
	/**
	 * protected Diagnosis function config(void)
	 */
	protected function config(): Diagnosis {
		$folder = 'conf';
		$files = array('swift.conf.php');
		foreach($files as $file){
			$path = implode('/', array(swordfish_path, $folder, $file));
			if(!is_file($path)) $this->errMessages[] = 'Sorry, system config file: [' . $file . '] No found. #_#';
		}
		return $this;
	}
	
	/**
	 * protected Diagnosis function resource(void)
	 */
	protected function resource(): Diagnosis {
		$folder = 'resource';
		$files = array('nofound.html');
		foreach($files as $file){
			$path = implode('/', array(swordfish_path, $folder, $file));
			if(!is_file($path)) $this->errMessage[] = 'Sorry, system resource file: [' . $file . '] NO found. #_#';
		}
		return $this;
	}
	
	/**
	 * void protectd function clearMessages(void)
	 */
	protected function clearMessages(): void {
		$this->errMessages = array();
	}
	//
}












