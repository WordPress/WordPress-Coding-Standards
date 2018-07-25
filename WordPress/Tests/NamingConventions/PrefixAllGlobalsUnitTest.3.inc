<?php
// @codingStandardsChangeSetting WordPress.NamingConventions.PrefixAllGlobals prefixes acronym,tgmpa

namespace Acronym\Tests;

class Some_Test extends \PHPUnit_Framework_TestCase {

	public function testPass() {
		define( 'SOME_GLOBAL', '4.0.0' );

		do_action( 'some-action', $something );
	}
}

// @codingStandardsChangeSetting WordPress.NamingConventions.PrefixAllGlobals prefixes false
