<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\DB;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;
use WordPressCS\WordPress\AbstractFunctionRestrictionsSniff;

/**
 * Unit test class for the DB_RestrictedFunctions sniff.
 *
 * @since 0.10.0
 * @since 0.13.0 Class name changed: this class is now namespaced.
 *
 * @covers \WordPressCS\WordPress\AbstractFunctionRestrictionsSniff
 * @covers \WordPressCS\WordPress\Sniffs\DB\RestrictedFunctionsSniff
 */
final class RestrictedFunctionsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Add a number of extra restricted functions to unit test the abstract
	 * AbstractFunctionRestrictionsSniff class.
	 *
	 * @before
	 */
	protected function enhanceGroups() {
		parent::setUp();

		AbstractFunctionRestrictionsSniff::$unittest_groups = array(
			'test-empty-funtions-array' => array(
				'type'      => 'error',
				'message'   => 'Detected usage of %s.',
				'functions' => array(),
			),
			'test_allow-key-handled-case-insensitively' => array(
				'type'      => 'error',
				'message'   => 'Detected usage of %s.',
				'functions' => array( 'myFiction*' ),
				'allow'     => array( 'myFictional' => true ),
			),
		);
	}

	/**
	 * Reset the $groups property.
	 *
	 * @after
	 */
	protected function resetGroups() {
		AbstractFunctionRestrictionsSniff::$unittest_groups = array();
		parent::tearDown();
	}

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected errors.
	 */
	public function getErrorList() {
		return array(
			25 => 1,
			26 => 1,
			27 => 1,
			28 => 1,
			29 => 1,
			30 => 1,
			31 => 1,
			32 => 1,
			33 => 1,

			36 => 1,
			37 => 1,
			38 => 1,
			39 => 1,
			40 => 1,
			41 => 1,
			42 => 1,
			43 => 1,
			44 => 1,

			47 => 1,
			48 => 1,
			49 => 1,
			50 => 1,
			51 => 1,

			54 => 1,
			55 => 1,
			56 => 1,
			57 => 1,

			60 => 1,

			63 => 1,

			66 => 1,
			67 => 1,
			68 => 1,
			69 => 1,
			70 => 1,
			71 => 1,
			72 => 1,
			73 => 1,
			74 => 1,
			75 => 1,
			76 => 1,

			94 => 1,
		);
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected warnings.
	 */
	public function getWarningList() {
		return array();
	}
}
