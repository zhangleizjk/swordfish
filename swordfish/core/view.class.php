<?php
// declare(strict_types = 1);
namespace Swordfish;

class View {
	
	/**
	 * module_name
	 * controller_name
	 * action_name
	 * template_engine = "unique"
	 * default__echo_doctype = "text/html"
	 * default_echo_charset = "utf-8"
	 * http_cache_control = "private"
	 */
	protected $url;
	protected $module;
	protected $controller;
	protected $action;
	protected $vars = array();
	
	/**
	 * public void function __construct(void)
	 */
	public function __construct() {
		$this->url = module_name . '.' . controller_name . '.' . action_name;
		$this->module = module_name;
		$this->controller = controller_name;
		$this->action = action_name;
	}
	
	/**
	 * public void fnnction __destruct(void)
	 */
	public function __destruct() {
		// echo '-Destory-';
	}
	
	/**
	 * public void function display(?string $url = null, ?string $doctype = null, ?string $charset = null)
	 */
	public function display(string $url = null, string $doctype = null, string $charset = null): void {
		$data = $this->fetch($url);
		$this->render($data, $doctype, $charset);
	}
	
	/**
	 * public void function show(string $data, ?string $type = null, ?string $charset = null)
	 */
	public function show(string $data, string $type = null, string $charset = null): void {
		$this->render($data, $type, $charset);
	}
	
	/**
	 * public string function fetch(?string url = null)
	 */
	public function fetch(string $url = null): string {
		$engine = get_config('template_engine', 'unique');
		$url = $this->url($url);
		ob_start();
		ob_implicit_flush(false);
		extract($this->vars);
		switch($engine){
			case 'unique':
				$unique = new Unique($url);
				echo $unique->compiler();
				break;
			case 'php':
				$processor = new Processor($url);
				echo $processor->compiler();
				break;
			default:
				$html = <<<'code'
<!doctype html>
<html>
<head>
	<title>SwordFish-Framework Message</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta charset="utf-8" />
	<style type="text/css">
		*{maring:0; padding:0; font-family:'Open Sans'; font-size:16px;}
		body {padding:50px;}
	</style>
</head>
<body>
	Sorry, template engine error. #_#
</body>
</html>
code;
				echo $html;
				break;
		}
		return ob_get_clean();
	}
	
	/**
	 * public boolean function assign(string $name, ?mixed $value)
	 */
	public function assign(string $name, $value): bool {
		if(is_var_named_regular($name)){
			$this->vars[$name] = $value;
			return true;
		}else
			return false;
	}
	
	/**
	 * public integer function assigns(array $vars)
	 * @$vars = [string $name => ?mixed $value,...]
	 */
	public function assigns(array $vars): int {
		$counter = 0;
		foreach($vars as $name => $value){
			if($this->assign($name, $value)) $counter++;
		}
		return $counter;
	}
	
	/**
	 * protected void function render(string $data, ?string $doctype, ?string $charset)
	 * &http://www.geekso.com/cache-control
	 * &http://www.runoob.com/http/http-header-fields.html
	 */
	protected function render(string $data, string $doctype, string $charset) {
		$doctype = $doctype ?? get_config('default_echo_doctype', 'text/html');
		$charset = $charset ?? get_config('default_echo_charset', 'utf-8');
		$httpCacheControl = get_config("http_cache_control", 'private');
		header('Content-Type:' . $doctype . '; charset=' . $charset);
		header('Cache-Control: ' . $httpCacheControl);
		echo $data;
	}
	
	/**
	 * protected string function url(string ?$url)
	 */
	protected function url(string $url): string {
		if(is_null($url)) return $this->url;
		elseif(is_url_regular($url)) return url;
		elseif(is_url_regular($url, 2)) return implode('.', array($this->module, $url));
		elseif(is_url_regular($url, 1)) return implode('.', array($this->module, $this->controller, $url));
		else return '';
	}
	//
}