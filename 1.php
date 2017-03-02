<?php
declare(strict_types = 1);

require_once './swift/core/model.class.php';
require_once './swift/core/mysql.class.php';
require_once './swift/func/swift.func.php';

$connectors=array(
	array(
		'operate'=>'both',
		'type'=>'mysql',
		'host'=>'localhost',
		'port'=>'3306',
		'dbname'=>'shop',
		'charset'=>'utf8',
		'username'=>'root',
		'password'=>'goodwin@000'
	)
);

$GLOBALS['_configs']['default_database_connector']=$connectors;
$m=new \Swift\Model('product');
print_r($m->limit()->select());
echo $m->sql();

