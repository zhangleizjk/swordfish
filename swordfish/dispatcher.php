<?php
// declare(strict_types = 1);
use Swordfish\Diagnosis;
use Swordfish\Core;
use Swordfish\App;
use Swordfish\Router;

/**
 */
date_default_timezone_set('Asia/Shanghai');

/**
 */
defined('swordfish_path') ?: define('swordfish_path', './swordfish');
defined('swordfish_diagnosis') ?: define('swordfish_diagnosis', true);
defined('app_path') ?: define('app_path', './program');
defined('app_debug') ?: define('app_debug', false);

/**
 * string function _msg(string $message)
 */
function _msg(string $data): string {
	$patterns = array('/__message__/', '/ NO /');
	$datas = array('<div>' . $data . '</div>', ' <span class="_msg_red">NO</span> ');
	$html = <<<'code'
<!doctype html>
<html>
<head>
<title>Swordfish Message</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta charset="UTF-8" />
<style type="text/css">
	*{maring:0; padding:0; font-family:'Open Sans'; font-size:18px;}
	html{height:100%;}
	body{height:100%; padding:50px; background:linear-gradient(#222,#555); color:#fff;}
	div {margin-bottom:2px;}
	._msg_red{color:#f00;}
	._msg_doc{color:#888;}
</style>
</head>
<body>
	__message__
</body>
</html>		
code;
	return preg_replace($patterns, $datas, $html);
}

/**
 */
$file = 'diagnosis.class.php';
$path = implode('/', array(swordfish_path, $file));
@include_once $path;
try{
	$diagnosis = new Diagnosis();
	$messages = $diagnosis->fire();
}catch(Throwable $err){
	die(_msg('Sorry, system diagnosis program NO found. #_#'));
}
if(messages){
	foreach($messages as &$message){
		$message = '<div>' . $message . '</div>';
	}
	die(_msg(implode('', $messages)));
}

/**
 */
$bootstrap = 'loader';

/**
 */
$file = 'core.class.php';
$path = implode('/', array(swordfish_path, $bootstrap, $file));
@include_once $path;
$system = new Core();
$system->fire();

/**
 */
$file = 'app.class.php';
$path = implode('/', array(swordfish_path, $bootstrap, $file));
@include_once $path;
$app = new App();
$app->fire();

/**
 */
$router = new Router();
$router->navigate();
//