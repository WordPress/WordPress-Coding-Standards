<?php

// phpcs:set WordPress.WP.GlobalVariablesOverride custom_test_class_whitelist[] My\NameSp\TestClass

namespace My \ /* comment */ NameSp;

class Test_Class_D extends TestClass {

	public function test_something() {
		global $tabs;
		$tabs = 50; // Ok.
	}
}
// phpcs:set WordPress.WP.GlobalVariablesOverride custom_test_class_whitelist[]
