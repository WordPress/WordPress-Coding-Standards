<?php

use PHPUnit\Framework\TestCase;

class Test_Class_D extends TestCase {

	public function test_something() {
		global $tabs;
		$tabs = 50; // Ok.
	}
}
