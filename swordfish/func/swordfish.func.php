<?php
// declare(strict_types = 1);
namespace Swordfish;

use Throwable;
use ReflectionClass;
use ReflectionMethod;

/**
 * $GLOBALS[_swordfish_configs]
 * app_path
 * app_namespace
 * ajax_element = 'is_ajax'
 * controller_layer= 'controller'
 * controller_extra = '.controller.class.php'
 * controller_namespace = 'Controller'
 * controller_class_extra = 'Controller'
 * model_layer = 'model'
 * model_extra = '.model.class.php'
 * model_namespace = 'Model'
 * model_class_extra = 'Model'
 */

/**
 * void function set_config(string $key, mixed $value)
 */
function set_config(string $key, $value): void {
	if(is_database_named_regular($key)) $GOLBALS['_sowrdfish_configs'][$key] = $value;
}

/**
 * ?mixed function get_config(string $key, ?$default = null)
 */
function get_config(string $key, $default = null) {
	return $GLOBALS['_swordfish_configs'][$key] ?? $default;
}

/**
 * array function get_configs(?array $keys = null)
 * @$keys = array((string $key => ?mixed $default)|string $key,...)
 */
function get_configs(array $keys = null): array {
	if(is_null($keys)) return $GLOBALS['_swordfish_configs'] ?? array();
	foreach($keys as $key => $default){
		$datas[] = is_string($key) ? get_config($key, $default) : get_config($default);
	}
	return $datas ?? array();
}

/**
 * boolean function is_integer_array(array $datas, boolean $strict = false)
 */
function is_integer_array(array $datas, bool $strict = false): bool {
	foreach(array_values($datas) as $key => $value){
		if(!is_integer($value)) return false;
		elseif($strict && $key != $value) return false;
	}
	return true;
}

/**
 * boolean function is_var_named_regular(string $data)
 */
function is_var_named_regular(string $data): bool {
	$pattern='/^[a-z]+([A-Z]{2}|[A-Z][a-z]+)*/';
	return preg_match($pattern, $data)?true: false;
}

/**
 * boolean function is_class_named_regular(string $data)
 */
function is_class_named_regular(string $data):bool {
	$pattern = '/^[a-z]+(_[a-z]+)*\.class\.php$/';
	return preg_match($pattern, $data)?true: false;
}

/**
 * boolean function is_database_named_regular(string $data)
 */
function is_database_named_regular(string $data): bool {
	$pattern = '/^[a-z]+(_[a-z]+)*$/';
	return preg_match($pattern, $data) ? true : false;
}

/**
 * boolean is_camel_named_regular(string $data)
 */
function is_camel_named_regular(string $data): bool {
	$pattern = '/^[a-z]+([A-Z][a-z]+)*$/s';
	return preg_match($pattern, $data) ? true : false;
}

/**
 * boolean function is_url_regular(string $data, integer $num = 3, string $separator = '.')
 */
function is_url_regular(string $data, int $num = 3, string $separator = '.'): bool {
	$datas = explode($separator, $data);
	if(count($datas) != $num) return false;
	foreach($datas as $value){
		if(!is_database_named_regular($value)) return false;
	}
	return true;
}

/**
 * string function camel_to_database_named(string $data)
 */
function camel_to_database_named(string $data): string {
	$pattern = '/([A-Z])/';
	$replace = '_$1';
	return strtolower(preg_replace($pattern, $replace, $data));
}

/**
 * strng function database_to_camel_named(string $data)
 */
function database_to_camel_named(string $data): string {
	$pattern = '/_([a-z])/';
	return preg_replace_callback($pattern, function ($matches) {
		return strtoupper($matches[1]);
	}, '_' . $data);
}

/**
 * boolean function is_database_connect_params(array $datas)
 * @$datas = array(string 'operate'|'type'|'host'|'port'|'dbname'|'charset'|'username'|'password' => string $value)
 */
function is_database_connect_params(array $datas): bool {
	$keys = array('operate', 'type', 'host', 'port', 'dbname', 'charset', 'username', 'password');
	if(count($datas) != count($keys)) return false;
	foreach($datas as $key => $value){
		if(!in_array($key, $keys, true)) return false;
		elseif(!is_string($value)) return false;
	}
	return true;
}

/**
 * string function database_backquote(string $identifier)
 */
function database_backquote(string $identifier): string {
	return '`' . $identifier . '`';
}

/**
 * boolean function is_data_validate_rule(array $datas)
 * @$datas = array(string $field, string $regular, array $params, string $message)
 */
function is_data_validate_rule(array $datas): bool {
	if(count($datas) != 4) return false;
	elseif(!is_integer_array(array_keys($datas), true)) return false;
	list($field, $regular, $params, $message) = $datas;
	if(!is_string($field) or !is_database_named_regular($field)) return false;
	elseif(!is_string($regular) or !is_camel_named_regular($regular)) return false;
	elseif(!is_array($params) or !is_integer_array(array_keys($params), true)) return false;
	elseif(!is_string($message)) return false;
	return true;
}

/**
 * ?string function get_request_method(void)
 */
function get_request_method(): string {
	if(is_request_ajax()) return 'ajax';
	elseif(is_request_get()) return 'get';
	elseif(is_request_post()) return 'post';
	else return null;
}

/**
 * ?array function get_method_params(string $class, string $method)
 */
function get_method_params(string $class, string $method, array $inputs): array {
	try{
		$refMethod = new \ReflectionMethod($class, $method);
	}catch(\Throwable $err){
		return null;
	}
	$params = $refMethod->getParameters();
	foreach($params as $param){
		$datas[$param->getName()] = (string)$param->getType();
	}
	return $datas ?? array();
}

/**
 * array function get_pathinfo_params(array $needs , $array $inputs)
 */
function get_pathinfo_params(array $needs, array $inputs): array {
	for($i = 0;$i < count($inputs);$i++){
		if(isset($needs[$i])) @settype($inputs[$i], $needs[$i]);
	}
	return $inputs;
}

/**
 * boolean function is_request_get(void)
 */
function is_request_get(): bool {
	if(!is_request_ajax()){
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		if('get' == $method) return true;
	}
	return false;
}

/**
 * boolean function is_request_post(void)
 */
function is_request_post(): bool {
	if(!is_request_ajax()){
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		if('post' == $method) return true;
	}
	return false;
}

/**
 * boolean function is_request_ajax(void)
 */
function is_request_ajax(): bool {
	$element = get_config('ajax_element', 'is_ajax');
	$param = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? null;
	if(!is_null($param) && strtolower($param) == 'xmlhttprequest') return true;
	elseif(isset($_GET[$element]) or isset($_POST[$element])) return true;
	return false;
}

/**
 * string function find_controller(string $module, string $controller)
 */
function find_controller(string $module, string $controller): string {
	$layer = get_config('controller_layer', 'controller');
	$extra = get_config('controller_extra', '.controller.class.php');
	return implode('/', array(app_path, $module, $layer, $controller . $extra));
}

/**
 * string find_controller_class(string $module, string $controller)
 */
function find_controller_class(string $module, string $controller): string {
	$app_namespace = get_config('app_namespace', 'Code42');
	$namespace = get_config('controller_namespace', 'Controller');
	$extra = get_config('controller_class_extra', 'Controller');
	return implode('\\', array('', $app_namespace, database_to_camel_named($module), $namespace, database_to_camel_named($controller) . $extra));
}

/**
 * string function find_model(string $module, string $model)
 */
function find_model(string $module, string $model): string {
	$layer = get_config('model_layer', 'model');
	$extra = get_config('model_extra', '.model.class.php');
	return implode('/', array(app_path, $module, $layer, $model . $extra));
}

/**
 * string find_model_class(string $module, string $model)
 */
function find_model_class(string $module, string $model): string {
	$namespace = get_config('model_namespace', 'Model');
	$extra = get_config('model_class_extra', 'Model');
	return implode('\\', array('', app_namespace, database_to_camel_named($module), $namespace, database_to_camel_named($model) . $extra));
}

/**
 * string function get_app404(void)
 */
function find_app404(): string {
	$common = get_config('common_layer', '_common');
	$resource = get_config('resource_layer', 'resource');
	$nofound = get_config('nofound_doc', '404.html');
	return implode('/', array(app_path, $common, $resource, $nofound));
}

/**
 * string function find_sys404(void)
 */
function find_sys404(): string {
	$resource = 'resource';
	$nofound = 'nofound.html';
	return implode('/', array(swordfish_path, $resource, $nofound));
}

/**
 * string find_log(void)
 */
function find_log(): string {
	$runtime = get_config('runtime_layer', '_runtime');
	$log = get_config('log_layer', 'log');
	$logFile = get_config('log_doc', 'log.txt');
	return implode('/', array(app_path, $runtime, $log, $logFile));
}

/**
 * boolean function _log(string $message)
 */
function _log(string $message): bool {
	$path = find_log();
	$handle = @fopen($path, 'ab');
	if(is_resource($handle)){
		$now = date('Y-m-d H:i:s');
		$record = '[' . $now . '] ' . $message . '\n';
		$byteNum = @fwrite($handle, $record);
		fclose($handle);
		return is_int($byteNum) ? true : false;
	}
	return false;
}

/**
 * mixed function _i(string $name, $default = null, ?array $filters = null, ?string $type = null)
 */
function _i(string $name, $default = null, array $filters = null, string $type = null) {
	$identifier = '[a-zA-Z][a-zA-Z0-9_]*';
	$keys = array('global', 'server', 'env', 'request', 'get', 'post', 'session', 'cookie');
	$values = array($GLOBALS, $_SERVER, $_ENV, $_REQUEST, $_GET, $_POST, $_SESSION ?? array(), $_COOKIE);
	$maps = array_combine($keys, $values);
	$pattern = '/^(' . implode('|', $keys) . ')\.(\*|' . $identifier . ')$/';
	if(preg_match($pattern, $name, $matches)){
		$predefined = $maps[$matches[1]];
		$key = $matches[2];
		if('*' == $key) $data = $predefined;
		elseif(isset($predefined[$key])) $data = $predefined[$key];
		else return $default;
	}else
		return $default;
	
	if(is_null($filters)) $filters = get_config('default_var_filters', array('htmlspecialchars'));
	$func = function ($data, $filter) {
		try{
			$data = $filter($data);
		}catch(Throwable $err){
			// _log();
		}
		return $data;
	};
	foreach($filters as $filter){
		if(is_array($data)){
			foreach($data as $value){
				$value = $func($value, $filter);
			}
		}else
			$data = $func($data, $filter);
	}
	
	if(!is_null($type)) settype($data, $type);
	
	return $data;
}

/**
 * ?Controller function _c(string $url)
 */
function _c(string $url) {
	if(is_url_regular($url, 2)) list($module, $controller) = explode('.', $url);
	else return null;
	@include_once find_controller($module, $controller);
	try{
		$class = new ReflectionClass(find_controller_class($module, $controller));
		return $class->newInstance();
	}catch(Throwable $err){
		// _log($err->getMessage());
		return null;
	}
}

/**
 * void function _a(string $url, array $params = array())
 */
function _a(string $url, array $params = array()): void {
	if(is_url_regular($url)) list($module, $controller, $action) = explode('.', $url);
	else return;
	$class = _c($module . '.' . $controller);
	if($class){
		try{
			$method = new ReflectionMethod($class, database_to_camel_named($action));
			$method->invokeArgs($class, $params);
		}catch(Throwable $err){
			// _log($err->getMessage());
		}
	}
}

/**
 * Model function _m(?string $memory = null, ?string $connector = null)
 */
function _m(string $memory = null, string $connector = null): Model {
	if(!is_null($memory) && !is_database_named_regular($memory)) $memory = null;
	return new Model($memory, $connector);
}

/**
 * Model function _d(string $url, ?string $connector = null)
 */
function _d(string $url, string $connector = null) {
	if(is_url_regular($url, 2)){
		list($module, $model) = explode('.', $url);
		@include_once find_model($module, $model);
		try{
			$class = new ReflectionClass(find_model_class($module, $model));
			return $class->newInstance($model, $connector);
		}catch(Throwable $err){
			// _log($err->getMessage());
			return _m($model, $connector);
		}
	}else
		return _m(null, $connector);
}

/**
 * ?string read_file(string $path)
 */
function read_file(string $path): string {
	$data = @file_get_contents($path);
	return is_string($data) ? $data : null;
}

/**
 * string function get404(void)
 */
function get404(): string {
	$html = <<<'code'
<!doctype html>
<html>
<head>
	<title>Swordfish-Framework Message</title>
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
	$sys = read_file($this->find_sys404());
	$app = read_file($this->find_app404());
	if(is_string($app)) return $app;
	elseif(is_string($sys)) return $sys;
	else return $html;
}

//


/**
 * boolean function is_integer_and_string_array(array $datas, boolean $strict = false)
 */
/*
function is_integer_and_string_array(array $datas, bool $strict = false): bool {
	foreach(array_values($datas) as $key => $value) {
		if (is_integer($value)) $integers[] = $value;
		elseif (is_string($value)) $strings[] = $value;
		else return false;
	}
	if (! ($integers ?? array()) or ! ($strings ?? array())) return false;
	foreach($integers as $key => $value) {
		if ($strict && $key != $value) return false;
	}
	return true;
}
*/























