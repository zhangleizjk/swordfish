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
		$this->checkFolders()->checkFuncs()->checkCores()->checkLibraries()->checkConfigs()->checkResources();
		return $this->errMessages;
	}
	
	/**
	 * protected Diagnosis function checkFolders(void)
	 */
	protected function checkFolders(): Diagnosis {
		$directories = array('loader', 'func', 'core', 'lib', 'conf', 'resource');
		foreach($directories as $directory){
			$path = implode('/', array(swordfish_path, $directory));
			if(!is_dir($path)) $this->message('Sorry, system folder : [' . $directory . '] NO found. #_#');
		}
		return $this;
	}
	
	/**
	 * protected Diagnosis function checkFuncs(void)
	 */
	protected function checkFuncs(): Diagnosis {
		$directory = 'func';
		$files = array('swordfish.func.php');
		foreach($files as $fileName){
			$fullName = implode('/', array(swordfish_path, $directory, $fileName));
			if(!is_file($fullName)) $this->message('Sorry, system function file: [' . $fileName . '] No found. #_#');
		}
		return $this;
	}
	
	/**
	 * protected Diagnosis function checkCores(void)
	 */
	protected function checkCores(): Diagnosis {
		$directory = 'core';
		$files = array('router', 'controller', 'model', 'mysql', 'validator', 'view', 'processor', 'unique');
		$extra = '.class.php';
		foreach($files as $fileBasicName){
			$fileName = $fileBasicName . $extra;
			$fullName = implode('/', array(swordfish_path, $directory, $fileName));
			if(!is_file($fullName)) $this->message('Sorry, system core library file : [' . $fileName . '] NO found. #_#');
		}
		return $this;
	}
	
	/**
	 * protected Diagnosis function checkLibraries(void)
	 */
	protected function checkLibraries(): Diagnosis {
		$directory = 'lib';
		$files = array();
		$extra = '.class.php';
		foreach($files as $fileBasicName){
			$fileName = $fileBasicName . $extra;
			$fullName = implode('/', array(swordfish_path, $directory, $fileName));
			if(!is_file($fullName)) $this->message('Sorry, system extra library file: [' . $fileName . '] NO found. #_#');
		}
		return $this;
	}
	
	/**
	 * protected Diagnosis function checkConfigs(void)
	 */
	protected function checkConfigs(): Diagnosis {
		$directory = 'conf';
		$files = array('swordfish.conf.php');
		foreach($files as $fileName){
			$fullName = implode('/', array(swordfish_path, $directory, $fileName));
			if(!is_file($fullName)) $this->message('Sorry, system config file: [' . $fileName . '] No found. #_#');
		}
		return $this;
	}
	
	/**
	 * protected Diagnosis function checkResources(void)
	 */
	protected function checkResources(): Diagnosis {
		$directory = 'resource';
		$files = array('nofound.html');
		foreach($files as $fileName){
			$fullName = implode('/', array(swordfish_path, $directory, $fileName));
			if(!is_file($fullName)) $this->message('Sorry, system resource file: [' . $fileName . '] NO found. #_#');
		}
		return $this;
	}
	
	/**
	 * protected void function message(string $data)
	 */
	protected function message(string $data): void {
		$this->errMessages[] = $data;
	}
	
	/**
	 * void protectd function clearMessages(void)
	 */
	protected function clearMessages(): void {
		$this->errMessages = array();
	}
	//
}












