<?php
// phpcs:set WordPress.NamingConventions.PrefixAllGlobals prefixes[] acronym,tgmpa

namespace Acronym\Tests;

class Some_Test extends \PHPUnit_Framework_TestCase {

	public function testPass() {
		define( 'SOME_GLOBAL', '4.0.0' );

		do_action( 'some-action', $something );
	}
}

$acronym_test = new class extends \phpunit_framework_testcase {

	public function testPass() {
		define( 'SOME_GLOBAL', '4.0.0' );

		do_action( 'some-action', $something );
	}
};

// Test namespace resolution when the sniff checks the extending class.
class Extends_Namespaced_Class_Not_WP_Test extends WP_Font_Face_UnitTestCase {

    public function testPass() {
        do_action( 'some-action', $something );
    }
}

/*
 * Safeguard correct handling of namespaced extending classes.
 */
class Extends_Partially_Qualified_Not_WP_Test extends MyNamespace\WP_UnitTestCase_Base {

    public function testPass() {
        do_action( 'some-action', $something );
    }
}

class Extends_Fully_Qualified_Not_WP_Test extends \MyNamespace\WP_Ajax_UnitTestCase {

    public function testPass() {
        do_action( 'some-action', $something );
    }
}

class Extends_Relative_Namespace_Not_WP_Test extends namespace\WP_Canonical_UnitTestCase {

    public function testPass() {
        do_action( 'some-action', $something );
    }
}

// phpcs:set WordPress.NamingConventions.PrefixAllGlobals prefixes[]
