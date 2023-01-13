<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\WP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;
use PHPCSUtils\BackCompat\Helper;

/**
 * Unit test class for the Capabilities sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   3.0.0
 */
final class CapabilitiesUnitTest extends AbstractSniffUnitTest {

	/**
	 * Adjust the config to allow for testing with specific CLI arguments.
	 *
	 * @param string                  $filename The name of the file being tested.
	 * @param \PHP_CodeSniffer\Config $config   The config data for the run.
	 *
	 * @return void
	 */
	public function setCliValues( $filename, $config ) {
		if ( 'CapabilitiesUnitTest.1.inc' === $filename ) {
			$config->warningSeverity = 3;
		} else {
			$config->warningSeverity = 5;
		}

		if ( 'CapabilitiesUnitTest.2.inc' === $filename ) {
			Helper::setConfigData( 'minimum_wp_version', '2.9', true, $config );
		} elseif ( 'CapabilitiesUnitTest.3.inc' === $filename ) {
			Helper::setConfigData( 'minimum_wp_version', '6.1', true, $config );
		} else {
			// Delete for other files.
			Helper::setConfigData( 'minimum_wp_version', null, true, $config );
		}
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
			case 'CapabilitiesUnitTest.1.inc':
				return array(
					40  => 1,
					49  => 1,
					50  => 1,
					65  => 1,
					66  => 1,
					67  => 1,
					68  => 1,
					69  => 1,
					70  => 1,
					71  => 1,
					72  => 1,
					73  => 1,
					74  => 1,
					78  => 1,
					85  => 1,
					106 => 1,
				);

			case 'CapabilitiesUnitTest.3.inc':
				return array(
					10 => 1,
					12 => 1,
					14 => 1,
				);

			case 'CapabilitiesUnitTest.4.inc':
				return array(
					8  => 1,
					10 => 1,
					12 => 1,
				);

			default:
				return array();
		}
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList( $testFile = '' ) {
		switch ( $testFile ) {
			case 'CapabilitiesUnitTest.1.inc':
				return array(
					11  => 1,
					12  => 1,
					17  => 1,
					21  => 1,
					22  => 1,
					23  => 1,
					24  => 1,
					25  => 1,
					29  => 1,
					31  => 1,
					32  => 1,
					35  => 1,
					46  => 1,
					55  => 1,
					56  => 1,
					57  => 1,
					58  => 1,
					59  => 1,
					60  => 1,
					92  => 1,
					100 => 1,
					105 => 1,
				);

			case 'CapabilitiesUnitTest.2.inc':
				return array(
					10 => 1,
					12 => 1,
					14 => 1,
				);

			default:
				return array();
		}
	}
}
