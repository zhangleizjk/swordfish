<?php
// declare(strict_types = 1);
namespace Swordfish;

class Diagnosis {
	/**
	 */
	protected $messages = array();
	
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
	 * array public function fire(void)
	 */
	public function fire(): array {
		$this->clearMessages();
		$this->folder()->func()->core()->library()->config()->resource();
		return $this->messages;
	}
	
	/**
	 * Diagnosis protected function folder(void)
	 */
	protected function folder(): Diagnosis {
		$folders = array('loader', 'func', 'core', 'lib', 'conf', 'resource');
		foreach($folders as $folder){
			$here = implode('/', array(swordfish_path, $folder));
			if(!is_dir($here)) $this->messages[] = 'System folder: [' . $folder . '] NO found. #_#';
		}
		return $this;
	}
	
	/**
	 * Diagnosis protected function func(void)
	 */
	protected function func(): Diagnosis {
		$folder = 'func';
		$files = array('swift.func.php');
		foreach($files as $file){
			$here = implode('/', array(swordfish_path, $folder, $file));
			if(!is_file($here)) $this->messages[] = 'System function file: [' . $file . '] No found. #_#';
		}
		return $this;
	}
	
	/**
	 * Diagnosis protected function core(void)
	 */
	protected function core(): Diagnosis {
		$folder = 'core';
		$files = array('router', 'controller', 'model', 'mysql', 'validator', 'view', 'processor', 'unique');
		$extra = '.class.php';
		foreach($files as $file){
			$here = implode('/', array(swordfish_path, $folder, $file . $extra));
			if(!is_file($here)) $this->messages[] = 'System core library file: [' . $file . $extra . '] NO found. #_#';
		}
		return $this;
	}
	
	/**
	 * Diagnosis protected function library(void)
	 */
	protected function library(): Diagnosis {
		$folder = 'lib';
		$files = array();
		$extra = '.class.php';
		foreach($files as $file){
			$here = implode('/', array(swordfish_path, $folder, $file . $extra));
			if(!is_file($here)) $this->message[] = 'System extra library file: [' . $file . $extra . '] NO found. #_#';
		}
		return $this;
	}
	
	/**
	 * Diagnosis protected function config(void)
	 */
	protected function config(): Diagnosis {
		$folder = 'conf';
		$files = array('swift.conf.php');
		foreach($files as $file){
			$here = implode('/', array(swordfish_path, $folder, $file));
			if(!is_file($here)) $this->messages[] = 'System config file: [' . $file . '] No found. #_#';
		}
		return $this;
	}
	
	/**
	 * Diagnosis protected function resource(void)
	 */
	protected function resource(): Diagnosis {
		$folder = 'resource';
		$files = array('nofound.html');
		foreach($files as $file){
			$here = implode('/', array(swordfish_path, $folder, $file));
			if(!is_file($here)) $this->message[] = 'System resource file: [' . $file . '] NO found. #_#';
		}
		return $this;
	}
	
	/**
	 * void protectd function clearMessages(void)
	 */
	protected function clearMessages(): void {
		$this->messages = array();
	}
	//
}












