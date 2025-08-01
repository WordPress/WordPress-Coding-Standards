<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Utils;

use PHP_CodeSniffer\Files\DummyFile;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;
use PHPCSUtils\BackCompat\Helper;
use PHPCSUtils\TestUtils\ConfigDouble;

/**
 * Unit test class for the I18nTextDomainFixer sniff.
 *
 * @since 1.2.0
 *
 * @covers \WordPressCS\WordPress\AbstractFunctionParameterSniff::is_targetted_token
 * @covers \WordPressCS\WordPress\Sniffs\Utils\I18nTextDomainFixerSniff
 */
final class I18nTextDomainFixerUnitTest extends AbstractSniffUnitTest {

	/**
	 * The tab width to use during testing.
	 *
	 * @var int
	 */
	private $tab_width = 4;

	/**
	 * Set CLI values before the file is tested.
	 *
	 * @param string                  $testFile The name of the file being tested.
	 * @param \PHP_CodeSniffer\Config $config   The config data for the test run.
	 *
	 * @return void
	 */
	public function setCliValues( $testFile, $config ) {
		// Tab width setting is only needed for the file with the function calls.
		if ( 'I18nTextDomainFixerUnitTest.4.inc' === $testFile ) {
			$config->tabWidth = $this->tab_width;
		} else {
			$config->tabWidth = 0;
		}
	}

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected errors.
	 */
	public function getErrorList( $testFile = '' ) {

		$phpcs_version = Helper::getVersion();
		$is_phpcs_4    = version_compare( $phpcs_version, '3.99.99', '>' );

		switch ( $testFile ) {
			case 'I18nTextDomainFixerUnitTest.css':
				return array(
					29  => ( true === $is_phpcs_4 ? 0 : 1 ),
					92  => ( true === $is_phpcs_4 ? 0 : 1 ),
					107 => ( true === $is_phpcs_4 ? 0 : 1 ),
					120 => ( true === $is_phpcs_4 ? 0 : 1 ),
					133 => ( true === $is_phpcs_4 ? 0 : 1 ),
					149 => ( true === $is_phpcs_4 ? 0 : 1 ),
				);

			case 'I18nTextDomainFixerUnitTest.3.inc':
				return array(
					32  => 1,
					42  => 1,
					84  => 1,
					97  => 1,
					109 => 1,
					116 => 1,
				);

			case 'I18nTextDomainFixerUnitTest.4.inc':
				return array(
					79  => 1,
					80  => 1,
					81  => 1,
					82  => 1,
					83  => 1,
					84  => 1,
					86  => 1,
					87  => 1,
					88  => 1,
					89  => 1,
					90  => 1,
					91  => 1,
					92  => 1,
					95  => 1,
					96  => 1,
					97  => 1,
					99  => 1,
					100 => 1,
					101 => 1,
					102 => 1,
					103 => 1,
					107 => 1,
					110 => 1,
					111 => 1,
					113 => 1,
					117 => 1,
					120 => 1,
					121 => 1,
					122 => 1,
					127 => 1,
					128 => 1,
					129 => 1,
					130 => 1,
					131 => 1,
					133 => 1,
					135 => 1,
					136 => 1,
					137 => 1,
					138 => 1,
					139 => 1,
					140 => 1,
					141 => 1,
					142 => 1,
					143 => 1,
					144 => 1,
					146 => 1,
					147 => 1,
					148 => 1,
					152 => 1,
					153 => 1,
					154 => 1,
					158 => 1,
					160 => 1,
					162 => 1,
					163 => 1,
					165 => 1,
					166 => 1,
					167 => 1,
					202 => 1,
					203 => 1,
					204 => 1,
					208 => 1,
					215 => 1,
					224 => 1,
					225 => 1,
					241 => 1,
					242 => 1,
					245 => 1,
					277 => 1,
					278 => 1,
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
	 * @return array<int, int> Key is the line number, value is the number of expected warnings.
	 */
	public function getWarningList( $testFile = '' ) {
		switch ( $testFile ) {
			case 'I18nTextDomainFixerUnitTest.1.inc':
			case 'I18nTextDomainFixerUnitTest.2.inc':
				return array(
					1 => 1,
				);

			case 'I18nTextDomainFixerUnitTest.4.inc':
				return array(
					172 => 1,
					173 => 1,
					174 => 1,
					175 => 1,
					176 => 1,
					177 => 1,
					178 => 1,
					179 => 1,
					181 => 1,
					182 => 1,
					184 => 1,
					185 => 1,
					186 => 1,
					189 => 1,
					190 => 1,
					191 => 1,
					195 => 1,
					196 => 1,
					201 => 1,
				);

			default:
				return array();
		}
	}

	/**
	 * Test the sniff bails early when handling a plugin header passed via STDIN.
	 *
	 * @return void
	 */
	public function testStdIn() {
		$config = new ConfigDouble();
		Helper::setConfigData( 'installed_paths', dirname( dirname( __DIR__ ) ), true, $config );
		$config->standards = array( 'WordPress' );
		$config->sniffs    = array( 'WordPress.Utils.I18nTextDomainFixer' );

		$ruleset = new Ruleset( $config );

		$content = '<?php // phpcs:set WordPress.Utils.I18nTextDomainFixer new_text_domain test-std-in
/**
 * Plugin Name: Missing text domain, docblock format.
 * Plugin URI: https://www.bigvoodoo.com/
 * Description: Sniff triggers a missing text domain error for a normal file, but not for STDIN.
 */';

		$file = new DummyFile( $content, $ruleset, $config );
		$file->process();

		$this->assertSame( 0, $file->getErrorCount() );
		$this->assertSame( 0, $file->getWarningCount() );
		$this->assertCount( 0, $file->getErrors() );
	}
}
