<?php
// declare(strict_types = 1);
namespace Swordfish;

class Processor {
	
	/**
	 * swordfish_path
	 * app_path
	 *
	 * app_common_layer = "_common"
	 * app_resource_alyer = "resource"
	 * app_nofound_file = "404.html"
	 * view_layer = "view"
	 * template_extra = ".master.html"
	 */
	protected $url;
	protected $module;
	protected $controller;
	protected $action;
	
	/**
	 * void public funtion __construct(string $url)
	 * @url = "shop_system.user_order.add"
	 */
	public function __construct(string $url) {
		if(is_url_regular($url)){
			list($this->module, $this->controller, $this->action) = explode('.', $url);
			$this->url = $url;
		}
	}
	
	/**
	 * void public function __destruct(void)
	 */
	public function __destruct() {
		//
	}
	
	/**
	 * string public function compiler(void)
	 */
	public function compiler(): string {
		return is_null($this->url) ? $this->getNoFound() : $this->getTemplate();
	}
	
	/**
	 * string protected function findTemplate(void)
	 */
	protected function findTemplate(): string {
		$view = get_config('view_layer', 'view');
		$extra = get_config('template_extra', '.master.html');
		$fullNames = array(app_path, $this->module, $view, $this->controller, $this->action . $extra);
		return implode('/', $fullNames);
	}
	
	/**
	 * string protected function getTemplate(void)
	 */
	protected function getTemplate(): string {
		$data = $this->read($this->findTemplate());
		return $data ?? $this->getNofound();
	}
	
	/**
	 * string protected function findSysNoFound(void)
	 */
	protected function findSysNoFound(): string {
		$resource = 'resource';
		$nofound = 'nofound.html';
		return implode('/', array(swordfish_path, $resource, $nofound));
	}
	
	/**
	 * string protected function getAppNoFound(void)
	 */
	protected function findAppNoFound(): string {
		$common = get_config('app_common_layer', '_common');
		$resource = get_config('app_resource_layer', 'resource');
		$nofound = get_config('app_nofound_file', '404.html');
		return implode('/', array(app_path, $common, $resource, $nofound));
	}
	
	/**
	 * string protected function getNoFound(void)
	 */
	protected function getNoFound(): string {
		$html = <<<'code'
<!doctype html>
<html>
<head>
	<title>Swift-Framework Message</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta charset="utf-8" />
	<style type="text/css">
		*{maring:0; padding:0; font-family:'open sans','microsoft yahei'; font-size:16px;}
		body {padding:50px;}
	</style>
</head>
<body>
	Sorry, 404 error. #_#
</body>
</html>		
code;
		$sys = $this->read($this->findSysNoFound());
		$app = $this->read($this->findAppNoFound());
		if(is_string($app)) return $app;
		elseif(is_string($sys)) return $sys;
		else return $html;
	}
	
	/**
	 * ?string protected function read(string $fullName)
	 */
	protected function read(string $fullName): string {
		$data = @file_get_contents($fullName);
		return is_string($data) ? $data : null;
	}
	//
}





