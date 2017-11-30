<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Tests\Commenting;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;
use WordPress\PHPCSHelper;

/**
 * Unit test class for the Commenting_DocblockFormat sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.16.0
 */
class DocblockFormatUnitTest extends AbstractSniffUnitTest {

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
		// Tab width setting is only needed for the tabbed file.
		if ( 'DocblockFormatUnitTest.1.inc' === $testFile ) {
			return array( '--tab-width=' . $this->tab_width );
		}

		return array();
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
		// Tab width setting is only needed for the tabbed file.
		if ( 'DocblockFormatUnitTest.1.inc' === $testFile ) {
			$config->tabWidth = $this->tab_width;
		} else {
			$config->tabWidth = 0;
		}
	}

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList( $testFile = '' ) {
		if ( 'DocblockFormatUnitTest.3.inc' === $testFile ) {
			return array(
				8  => ( version_compare( PHPCSHelper::get_version(), '3.0.0', '>' ) ? 2 : 0 ),
				15 => ( version_compare( PHPCSHelper::get_version(), '3.0.0', '>' ) ? 2 : 0 ),
			);
		}

		// File 1 + 2.
		return array(
			19  => 1,
			21  => 1,
			26  => 1,
			27  => 2,
			32  => 2,
			37  => 1,
			39  => 1,
			42  => 2,
			45  => 1,
			47  => 2,
			50  => 1,
			52  => 1,
			60  => 1,
			68  => 1,
			70  => 1,
			77  => 1,
			79  => 1,
			87  => 1,
			88  => 1,
			89  => 1,
			90  => 1,
			91  => 1,
			96  => 1,
			97  => 1,
			98  => 1,
			99  => 1,
			100 => 1,
			105 => 1,
			106 => 1,
			108 => 1,
			112 => 1,
			113 => 1,
			115 => 1,
			120 => 1,
			122 => 1,
			123 => 1,
			127 => 1,
			129 => 1,
			130 => 1,
			134 => 1,
			135 => 1,
			136 => 1,
			137 => 1,
			141 => 1,
			142 => 1,
			143 => 1,
			144 => 1,
			148 => 1,
			149 => 1,
			150 => 1,
			151 => 1,
			155 => 1,
			156 => 1,
			157 => 1,
			158 => 1,
			162 => 1,
			163 => 1,
			164 => 1,
			165 => 1,
			169 => 1,
			170 => 1,
			171 => 1,
			172 => 1,
			176 => 1,
			177 => 1,
			181 => 1,
			184 => 2,
			190 => 3,
			193 => 2,
			199 => 1,
			200 => 1,
			201 => 1,
			202 => 1,
			207 => 1,
			216 => 1,
			229 => 1,
			234 => 1,
		);

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
