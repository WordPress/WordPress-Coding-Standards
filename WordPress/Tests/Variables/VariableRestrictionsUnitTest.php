<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package  PHP\CodeSniffer\WordPress-Coding-Standards
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * A sniff unit test checks a .inc file for expected violations of a single
 * coding standard. Expected errors and warnings are stored in this class.
 *
 * @package   PHP\CodeSniffer\WordPress-Coding-Standards
 * @author    Shady Sharaf <shady@x-team.com>
 */
class WordPress_Tests_Variables_VariableRestrictionsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Fill in the $groups property to test the abstract class.
	 */
	protected function setUp() {
		parent::setUp();

		WordPress_AbstractVariableRestrictionsSniff::$groups = array(
			'test' => array(
				'type'          => 'error',
				'message'       => 'Detected usage of %s',
				'object_vars'   => array(
					'$foo->bar',
					'FOO::var',
					'FOO::reg*',
					'FOO::$static',
				),
				'array_members' => array(
					'$foo[\'test\']',
				),
				'variables'     => array(
					'$taz',
				),
			),
		);
	}

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			3 => 1,
			5 => 1,
			11 => 1,
			15 => 1,
			17 => 1,
			21 => 1,
			23 => 1,
		);

	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array();

	}

} // End class.
