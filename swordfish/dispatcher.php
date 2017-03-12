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
defined('app_path') ?: define('app_path', './program');
defined('public_path') ?: define('public_path', './public');
defined('swordfish_debug') ?: define('swordfish_debug', true);
defined('app_debug') ?: define('app_debug', true);

/**
 * string function _msg(string $message)
 */
function _msg(string $data): string {
	$patterns = array('/__message__/', '/ NO /', '/ \[(.*)\] /');
	$datas = array('<div>' . $data . '</div>', ' <span class="_msg_red">NO</span> ', ' [<span class="_msg_doc">$1</span>] ');
	$html = <<<'code'
<!doctype html>
<html>
<head>
<title>Swordfish Message</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta charset="UTF-8" />
<style type="text/css">
	*{maring:0; padding:0; font:18px/26px 'Open Sans';}
	html{height:100%;}
	body{padding:50px; background:linear-gradient(#222,#555); color:#fff;}
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
if(swordfish_debug){
	$fileName = 'diagnosis.class.php';
	$fullName = implode('/', array(swordfish_path, $fileName));
	@include_once $fullName;
	try{
		$diagnosis = new Diagnosis();
		$messages = $diagnosis->fire();
	}catch(Throwable $err){
		die(_msg('Sorry, system diagnosis program NO found. #_#'));
	}
	if($messages){
		$msgStr = nl2br(implode("\n", $messages));
		die(_msg($msgStr));
	}
}

/**
 */
$bootstrap = 'loader';

/**
 */
$fileName = 'core.class.php';
$fullName = implode('/', array(swordfish_path, $bootstrap, $fileName));
@include_once $fullName;
$system = new Core();
$system->fire();

/**
 */
$fileName = 'app.class.php';
$fullName = implode('/', array(swordfish_path, $bootstrap, $fileName));
@include_once $fullName;
$app = new App();
$app->fire();

/**
 */
$router = new Router();
$router->navigate();
//