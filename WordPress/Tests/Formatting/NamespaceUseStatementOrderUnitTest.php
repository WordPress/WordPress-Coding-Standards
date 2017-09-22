<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Tests\Formatting;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the UseStatementSpacing sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.14.0
 *
 * {@internal Once upstream PR #1674 has been merged and the WPCS minimum
 * PHPCS requirement has gone up to the version which contains that change,
 * it will no longer be necessary to set the tab-width for these unit tests.
 * {@link https://github.com/squizlabs/PHP_CodeSniffer/pull/1674} }}
 */
class NamespaceUseStatementOrderUnitTest extends AbstractSniffUnitTest {

	/**
	 * The tab width to use during testing.
	 *
	 * @var int
	 */
	private $tab_width = 4;

	/**
	 * Get a list of CLI values to set before the file is tested.
	 *
	 * Used by PHPCS 2.x.
	 *
	 * @param string $testFile The name of the file being tested.
	 *
	 * @return array
	 */
	public function getCliValues( $testFile ) {
		return array( '--tab-width=' . $this->tab_width );
	}

	/**
	 * Set CLI values before the file is tested.
	 *
	 * Used by PHPCS 3.x.
	 *
	 * @param string                  $testFile The name of the file being tested.
	 * @param \PHP_CodeSniffer\Config $config   The config data for the test run.
	 *
	 * @return void
	 */
	public function setCliValues( $testFile, $config ) {
		$config->tabWidth = $this->tab_width;
	}

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList( $testFile = '' ) {

		switch ( $testFile ) {
			case 'NamespaceUseStatementOrderUnitTest.1.inc':
				return array(
					10 => 1,
					12 => 1,
					13 => 1,
					14 => 1,
					18 => 1,
				);

			case 'NamespaceUseStatementOrderUnitTest.2.inc':
				return array(
					9  => 1,
					11 => 2,
					12 => 1,
					14 => 3,
					15 => 2,
					16 => 3,
				);

			case 'NamespaceUseStatementOrderUnitTest.3.inc':
				return array(
					7  => 1,
					8  => 2,
					9  => 2,
					10 => 1,
					11 => 2,
					12 => 3,
					13 => 2,
				);

			case 'NamespaceUseStatementOrderUnitTest.4.inc':
				return array(
					13 => 2,
					16 => 1,
					24 => 2,
					34 => 1,
					41 => 2,
					48 => 7,
				);

			case 'NamespaceUseStatementOrderUnitTest.5.inc':
				return array(
					7  => 1,
					8  => 1,
					10 => 2,
					11 => 1,
					13 => 3,
					14 => 2,
					15 => 3,

					21 => 1,
					23 => 2,
					24 => 1,
					26 => 3,
					27 => 2,
					28 => 2,

					34 => 1,
					36 => 2,
					37 => 1,
					39 => 3,
					40 => 2,
					41 => 2,

					44 => 1,
					45 => 1,
					47 => 2,
					48 => 1,
					50 => 3,
					51 => 2,
					52 => 2,
				);

			default:
				return array();

		}

	} // end getErrorList()

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array();

	}

} // End class.
