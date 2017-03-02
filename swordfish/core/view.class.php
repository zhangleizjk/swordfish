<?php
// declare(strict_types = 1);
namespace Swift;

class View {
	
	/**
	 * module_name
	 * controller_name
	 * action_name
	 */
	
	/**
	 * template_engine="unique"
	 * echo_default_doctype="text/html"
	 * echo_default_charset="utf-8"
	 * http_cache_control="?"
	 */
	protected $url;
	protected $module;
	protected $controller;
	protected $action;
	protected $identifier = '[a-z][a-z0-9_]*';
	protected $vars = array();
	
	/**
	 * void public function __construct(void)
	 */
	public function __construct() {
		$this->module = module_name;
		$this->controller = controller_name;
		$this->action = action_name;
		$this->url = implode('.', array($this->module, $this->controller, $this->action));
	}
	
	/**
	 * void public fnnction __destruct(void)
	 */
	public function __destruct() {
		//
	}
	
	/**
	 * void public function display(?string $url = null, ?string $type = null, ?string $charset = null)
	 */
	public function display(string $url = null, string $type = null, string $charset = null): void {
		$data = $this->fetch($url);
		$this->render($data, $type, $charset);
	}
	
	/**
	 * void public function appear(string $data, ?string $type = null, ?string $charset = null)
	 */
	public function appear(string $data, string $type = null, string $charset = null): void {
		$this->render($data, $type, $charset);
	}
	
	/**
	 * string public function fetch(string url)
	 */
	public function fetch(string $url): string {
		$engine = get_config('template_engine');
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
				//
				break;
		}
		return ob_get_clean();
	}
	
	/**
	 * boolean public function assign(string $name, mixed $value)
	 */
	public function assign(string $name, $value): bool {
		$pattern = '/^' . $this->identifier . '$/si';
		if(preg_match($pattern, $name)){
			$this->vars[$name] = $value;
			return true;
		}else
			return false;
	}
	
	/**
	 * integer public function assigns(array $vars)
	 */
	public function assigns(array $vars): int {
		$counter = 0;
		foreach($vars as $name => $value){
			if($this->assign($name, $value)) $counter++;
		}
		return $counter;
	}
	
	/**
	 * void protected function render(string $data, ?string $doctype, ?string $charset)
	 * http://www.geekso.com/cache-control
	 * http://www.runoob.com/http/http-header-fields.html
	 */
	protected function render(string $data, string $doctype, string $charset) {
		list($doctype, $charset) = get_configs('echo_default_doctype', 'echo_default_charset');
		$httpCacheControl = get_config("http_cache_control");
		header('Content-Type:' . $doctype . '; charset=' . $charset);
		header('Cache-Control: ' . $httpCacheControl);
		echo $data;
	}
	
	/**
	 * string protected function url(string ?$url)
	 */
	protected function url(string $url): string {
		if(is_null($url)) return $this->url;
		$pattern = '/' . $this->identifier . '(\.' . $this->identifier . '){0,2}/';
		if(preg_match($pattern, $url)){
			$num = count(explode('.', $url));
			if(3 == $num) return $url;
			elseif(2 == $num) return implode('.', array($this->module, $url));
			elseif(1 == $num) return implode('.', array($this->module, $this->controller, $url));
		}else
			return '';
	}
	//
}















