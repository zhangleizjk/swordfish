<?php
// declare(strict_types = 1);
namespace Swordfish;

class Unique {
	
	/**
	 * swordfish_path
	 * app_path
	 *
	 * app_common = "_common"
	 * app_resource = "resource"
	 * app_template_nofound = "404"
	 * view_layer = "view"
	 * template_left_delimiter="{"
	 * template_right_delimiter="}"
	 * template_extension = '.master.html'
	 * template_prototype_layer="prototype"
	 * template_prototype_extra=".prototype.html"
	 * template_cache_layer="cache"
	 * template_cache_extra=".cache.html";
	 */
	protected $url;
	protected $module;
	protected $controller;
	protected $action;
	protected $hash;
	protected $name_regular = '[a-z][a-z0-9_]*';
	protected $literals = array();
	protected $phps = array();
	
	/**
	 * void public funtion __construct(string $url)
	 * url="shop_system.user_order.add"
	 */
	public function __construct(string $url) {
		if(is_url_regular($url)){
			list($this->module, $this->controller, $this->action) = explode('.', $url);
			$this->hash = md5($url);
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
		if(is_null($this->url)) return $this->get404();
		$cache = $this->getCache();
		return is_string($cache) && !app_debug ? $cache : $this->getTemplate();
	}
	
	/**
	 * string protected function clearOriginalPhp(string $data)
	 */
	protected function clearOriginalPhp(string $data): string {
		$pattern = '/<\?php\s+.*?\s+\?>/si';
		return preg_replace($pattern, '', $data);
	}
	
	/**
	 * string protected function parseLiteral(string $data)
	 */
	protected function parse_literal(string $data): string {
	}
	protected function parseLiteral(string $data): string {
		$this->literals = array();
		$pattern = '/<literal>(.*?)<\/literal>/si';
		return preg_replace_callback($pattern, function ($matches) {
			$hash = md5($matches[1]);
			$this->literals[$hash] = $matches[1];
			return '<literal>' . $hash . '</literal>';
		}, $data);
	}
	
	/**
	 * string protected function restoreLiteral(string $data)
	 */
	protected function restoreLiteral(string $data): string {
		$pattern = '/<literal>(.*?)<\/literal>/si';
		return preg_replace_callback($pattern, function ($matches) {
			$hash = $matches[1];
			return $this->literals[$hash] ?? '';
		}, $data);
	}
	
	/**
	 * string protected function parsePhp(string $data)
	 */
	protected function parsePhp(string $data): string {
		$this->phps = array();
		$pattern = '/<php>(.*?)<\/php>/si';
		return preg_replace_callback($pattern, function ($matches) {
			$hash = md5($matches[1]);
			$this->phps[$hash] = $matches[1];
			return '<php>' . $hash . '</php>';
		}, $data);
	}
	
	/**
	 * string protected function restorePhp(string $data)
	 */
	protected function restorePhp(string $data): string {
		$pattern = '/<php>(.*?)<\/php>/si';
		return preg_replace_callback($pattern, function ($matches) {
			$hash = $matches[1];
			return isset($this->phps[$hash]) ? '<?php ' . $this->phps[$hash] . ' ?>' : '';
		}, $data);
	}
	
	/**
	 * string protected function parseSysConst(string $data)
	 */
	protected function parseSysConst(string $data) {
		list($begin, $end) = get_configs('template_left_delimiter', 'template_right_delimiter');
		$pattern = '/' . $begin . '\$sys\.const.(' . $this->name_regular . ')' . $end . '/si';
		$replace = '<?php echo $1; ?>';
		return preg_replace($pattern, $replace, $data);
	}
	
	/**
	 * string protected function parseSysVar(string $data)
	 */
	protected function parseSysVar(string $data) {
		$begin = get_config('template_left_delimiter', '{');
		$end = get_config('template_right_delimiter', '}');
		$predefineds = array('server', 'env', 'request', 'get', 'post', 'session', 'cookie');
		$pattern = '/' . $begin . '\$sys\.(' . implode('|', $predefineds) . ')\.(' . $this->name_regular . ')' . $end . '/si';
		return preg_replace_callback($pattern, function ($matches) {
			if(in_array($matches[1], array('server', 'env'))) $matches[2] = strtoupper($matches[2]);
			return '<?php echo $_' . strtoupper($matches[1]) . '[\'' . $matches[2] . '\']; ?>';
		}, $data);
	}
	
	/**
	 * string protected function parseUserVar(string $data)
	 */
	protected function parseUserVar(string $data): string {
		$begin = get_config('template_left_delimiter', '{');
		$end = get_config('template_right_delimiter', '}');
		$patterns = array('/' . $begin . '(\$' . $this->name_regular . ')' . $end . '/si', '/' . $begin . '(\$' . $this->name_regular . ')\.(' . $this->name_regular . ')' . $end . '/si', '/' . $begin . '(\$' . $this->name_regular . '):(' . $this->name_regular . ')' . $end . '/si');
		$replaces = array('<?php echo $1; ?>', '<?php echo $1[\'$2\']; ?>', '<?php echo $$1->$2; ?>');
		return preg_replace($patterns, $replaces, $data);
	}
	
	/**
	 * string protected function findTemplate(void)
	 */
	protected function findTemplate(): string {
		$view = get_config('view_layer', 'view');
		$extension = get_config('template_extension', '.master.html');
		$fullNameChildren = array(app_path, $this->module, $view, $this->controller, $this->action . $extension);
		return implode('/', $fullNameChildren);
	}
	
	/**
	 * string protected function parseTemplate(string $data)
	 */
	protected function parseTemplate(string $data): string {
		$data = $this->clearOriginalPhp($data);
		$data = $this->parsePhp($this->parseLiteral($data));
		$data = $this->parseUserVar($this->parseSysVar($this->parseSysConst($data)));
		$data = $this->restoreLiteral($this->restorePhp($data));
		$data = $this->clearPrototype($data);
		$data = $this->parseListTag($data);
		return $data;
	}
	
	/**
	 * string protected function getTemplate(void)
	 */
	protected function getTemplate(): string {
		$data = $this->getFile($this->findTemplate());
		if(is_string($data)){
			$data = $this->parseTemplate($data);
			$this->setCache($data);
		}else
			$data = $this->get404();
		return $data;
	}
	
	/**
	 * string protected function findPrototype(string $name)
	 */
	protected function findPrototype(string $name): string {
		list($view, $prototype, $extra) = get_configs('view_layer', 'template_prototype_layer', 'template_prototype_extra');
		$paths = array('.', app_path, $this->module, $view, $prototype, $name . $extra);
		return implode('/', $paths);
	}
	
	/**
	 * string protected function parsePrototype(string $data)
	 */
	protected function parsePrototype(string $data): string {
		$pattern = '/<prototype\s+url="(' . $this->name_regular . ')\s+/">/si';
		if(preg_match($pattern, $data, $matches)) $prototype = $this->getPrototype($matches[1]);
		else return $data;
		if(is_null($prototype)) return $data;
		$pattern = '/<sign\s+id="(' . $this->name_regular . ')">(.*?)<\/sign>/si';
		$num = preg_match_all($pattern, $data, $matches);
		$signs = $num ? array_combine($matches[1], $matches[2]) : array();
		return preg_replace_callback($pattern, function ($matches) {
			$key = $matches[1];
			return $signs[$key] ?? $matches[2];
		}, $prototype);
	}
	
	/*
	 * string protected function clearPrototype(string $data)
	 */
	protected function clearPrototype(string $data): string {
		$patterns = array('/<prototype\s+url="' . $this->name_regular . '"\s+\/>/si', '/<sign\s+id="' . $this->name_regular . '">/si', '/<\/sign>/si');
		return preg_replace($patterns, '', $data);
	}
	
	/**
	 * ?string protected function getPrototype(string $name)
	 */
	protected function getPrototype($name): string { // ?string
		return $this->getFile($this->findPrototype());
	}
	
	/**
	 * string protected function findCache(void)
	 */
	protected function findCache(): string {
		$view = get_config('view_layer', 'view');
		$cache = get_config('view_cache_layer', 'cache');
		$extension = get_config('template_cache_extension', '.cache.html');
		$fullNameChildren = array(app_path, $this->module, $view, $cache, $this->hash . $extension);
		return implode('/', $fullNameChildren);
	}
	
	/**
	 * boolean protected function setCache(string $data)
	 */
	protected function setCache(string $data): bool {
		return $this->write($this->findCache(), $data);
	}
	
	/**
	 * ?string protected function getCache(void)
	 */
	protected function getCache(): string { // ?string
		return $this->getFile($this->findCache());
	}
	
	/**
	 * string protected function findApp404(void)
	 */
	protected function findApp404(): string {
		$common = get_config('app_common_layer', '_common');
		$resource = get_config('app_resource_layer', 'resource');
		$nofound = get_config('app_nofound_file', '404.html');
		$pathChildren = array(app_path, $common, $resource, $nofound);
		return implode('/', $pathChildren);
	}
	
	/**
	 * string protected function findSys404(void)
	 */
	protected function findSys404(): string {
		$resource = 'resource';
		$nofound = 'nofound.html';
		$pathChildren = array(swordfish_path, $resource, $nofound);
		return implode('/', $pathChildren);
	}
	
	/**
	 * string protected function get404(void)
	 */
	protected function get404(): string {
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
		$app = $this->read($this->findApp404());
		$sys = $this->read($this->findSys404());
		if(is_string($app)) return $app;
		elseif(is_string($sys)) return $sys;
		else return $html;
	}
	
	/**
	 * boolean protected function write(string $path, string $data)
	 */
	protected function write(string $path, string $data): bool {
		$num = @file_put_contents($path, $data);
		return is_integer($num) ? true : false;
	}
	
	/**
	 * ?string protected function read(string $path)
	 */
	protected function read(string $path): string {
		$data = @file_get_contents($path);
		return is_string($data) ? $data : null;
	}
	
	/**
	 * string protected function parseListTag(string $data)
	 * <list data="?" key="?" value="?"></list>
	 */
	protected function parseListTag(string $data): string {
		$patterns = array('/<list\s+data="(' . $this->named_regular . ')"\s+key="(' . $id . ')"\s+value="(' . $id . ')">/si', '/<\/list>/si');
		$replaces = array('<?php foreach($$1 as $$2=>$$3) { ?>', '<?php } ?>');
		return preg_replace($patterns, $replaces, $data);
	}
	//
}





































