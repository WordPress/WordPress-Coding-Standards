<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Helpers\WPHookHelper;

use PHPCSUtils\BackCompat\Helper;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\PassedParameters;
use WordPressCS\WordPress\Helpers\WPHookHelper;

/**
 * Tests for the `WPHookHelper::get_hook_name_param()` method.
 *
 * @since 3.4.0
 *
 * @covers \WordPressCS\WordPress\Helpers\WPHookHelper::get_hook_name_param
 */
final class GetHookNameParamUnitTest extends UtilityMethodTestCase {

	/**
	 * Test get_hook_name_param().
	 *
	 * @dataProvider dataGetHookNameParam
	 *
	 * @param string       $testMarker     The comment which prefaces the target token in the test file.
	 * @param string|false $expectedResult The raw content of the expected hook name parameter,
	 *                                     or `false` when no hook name parameter is expected.
	 * @param int|string   $tokenType      Optional. The token type to search for. Defaults to `T_STRING`.
	 *
	 * @return void
	 */
	public function testGetHookNameParam( $testMarker, $expectedResult, $tokenType = \T_STRING ) {
		$stackPtr     = $this->getTargetToken( $testMarker, $tokenType );
		$functionName = self::$phpcsFile->getTokens()[ $stackPtr ]['content'];
		$parameters   = PassedParameters::getParameters( self::$phpcsFile, $stackPtr );

		$result = WPHookHelper::get_hook_name_param( $functionName, $parameters );

		if ( is_array( $result ) ) {
			// The details of the parameter are populated by PassedParameters::getParameters().
			// Here we only verify which parameter was selected.
			$result = $result['clean'];
		}

		$this->assertSame( $expectedResult, $result );
	}

	/**
	 * Data provider.
	 *
	 * @see testGetHookNameParam()
	 *
	 * @return array<string, array<string, int|string|false>>
	 */
	public static function dataGetHookNameParam() {
		$phpcs_version = Helper::getVersion();
		$is_phpcs_4    = version_compare( $phpcs_version, '3.99.99', '>' );

		return array(
			'not_a_hook_function' => array(
				'testMarker'     => '/* testNotAHookFunction */',
				'expectedResult' => false,
			),
			'hook_name_param_missing' => array(
				'testMarker'     => '/* testHookNameParamMissing */',
				'expectedResult' => false,
			),
			'lowercase_name' => array(
				'testMarker'     => '/* testLowercaseName */',
				'expectedResult' => "'my_action'",
			),
			'mixedcase_name' => array(
				'testMarker'     => '/* testMixedCaseName */',
				'expectedResult' => "'my_filter'",
			),
			'named_parameter' => array(
				'testMarker'     => '/* testNamedParameter */',
				'expectedResult' => "'my_action'",
			),
			'fully_qualified_name' => array(
				'testMarker'     => '/* testFullyQualifiedName */',
				'expectedResult' => "'my_action'",
				'tokenType'      => ( true === $is_phpcs_4 ? \T_NAME_FULLY_QUALIFIED : \T_STRING ),
			),
		);
	}
}
