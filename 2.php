<?php
//require_once './swift/func/swift.func.php';

try{
	$aa=file_get_contents('222.txt');
	var_dump($aa);
}catch(Throwable $err){
	echo "error";
}
