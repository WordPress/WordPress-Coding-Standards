<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\WP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the I18n sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.10.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 */
final class I18nUnitTest extends AbstractSniffUnitTest {

	/**
	 * Set CLI values before the file is tested.
	 *
	 * @param string                  $testFile The name of the file being tested.
	 * @param \PHP_CodeSniffer\Config $config   The config data for the test run.
	 *
	 * @return void
	 */
	public function setCliValues( $testFile, $config ) {
		// Test overruling the text domain from the command line for one test file.
		if ( 'I18nUnitTest.3.inc' === $testFile ) {
			$config->setConfigData( 'text_domain', 'something', true );
		} else {
			// Delete the text domain option so it doesn't persist for subsequent test files.
			$config->setConfigData( 'text_domain', null, true );
		}
	}

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList( $testFile = '' ) {

		switch ( $testFile ) {
			case 'I18nUnitTest.1.inc':
				return array(
					10  => 1,
					11  => 1,
					12  => 1,
					14  => 1,
					16  => 1,
					19  => 1,
					21  => 1,
					23  => 1,
					24  => 1,
					26  => 1,
					27  => 1,
					28  => 1,
					30  => 1,
					31  => 1,
					32  => 1,
					37  => 1,
					38  => 1,
					39  => 1,
					41  => 1,
					42  => 1,
					43  => 1,
					45  => 1,
					47  => 1,
					48  => 1,
					50  => 1,
					52  => 1,
					53  => 1,
					55  => 1,
					56  => 2,
					58  => 1,
					59  => 1,
					60  => 1,
					62  => 1,
					63  => 2,
					65  => 1,
					66  => 1,
					67  => 1,
					72  => 1,
					74  => 1,
					75  => 1,
					76  => 1,
					77  => 1,
					78  => 1,
					93  => 1,
					95  => 2,
					100 => 1,
					101 => 1,
					102 => 1,
					103 => 1,
					105 => 1,
					106 => 1,
					107 => 1,
					116 => 1,
					117 => 1,
					118 => 1,
					119 => 1,
					120 => 1,
					121 => 1,
					124 => 1,
					125 => 1,
					128 => 1,
					134 => 1,
					139 => 1,
					144 => 1,
					153 => 1,
					157 => 1,
					178 => 1,
					181 => 3,
					184 => 1,
					222 => 1,
					223 => 1,
					224 => 1,
					225 => 1,
					226 => 1,
					227 => 1,
				);

			case 'I18nUnitTest.2.inc':
				return array(
					104 => 2,
				);

			case 'I18nUnitTest.3.inc':
				return array(
					10 => 1,
					11 => 1,
					13 => 1,
					14 => 1,
					15 => 1,
					16 => 1,
					17 => 1,
					18 => 1,
					20 => 1,
					21 => 1,
				);

			default:
				return array();
		}
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList( $testFile = '' ) {
		switch ( $testFile ) {
			case 'I18nUnitTest.1.inc':
				return array(
					69  => 1,
					70  => 1,
					100 => 1,
					101 => 1,
					102 => 1,
					103 => 1,
					154 => 1,
					158 => 1,
					159 => 1,
					187 => 1,
					191 => 1,
					193 => 1,
					194 => 1,
					198 => 1,
					199 => 1,
				);

			case 'I18nUnitTest.2.inc':
				return array(
					9   => 1,
					43  => 1,
					49  => 1,
					52  => 1,
					74  => 1,
					85  => 1,
					108 => 1,
				);

			default:
				return array();
		}
	}

}
