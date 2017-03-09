<?php
declare(strict_types = 1);

namespace Code42\Home\Controller;
use Swordfish\Controller;

class UserController {
	public function hello($name) {
		echo "<b style='color: green;'>Hello, $name!</b>";
	}
}