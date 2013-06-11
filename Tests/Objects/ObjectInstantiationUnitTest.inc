<?php

class My_Plugin {
	public function __construct() {}
}

new My_Plugin(); // Bad

$my_plugin = new My_Plugin(); // Good
