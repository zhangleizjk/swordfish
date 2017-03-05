<?php
use function Swordfish\database_to_camel_named;

require_once './swordfish/func/swift.func.php';

$data='home_user';
$newdata=database_to_camel_named($data);
echo $newdata;
