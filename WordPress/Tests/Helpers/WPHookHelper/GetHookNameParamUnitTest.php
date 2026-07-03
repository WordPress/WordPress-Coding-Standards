<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Helpers\WPHookHelper;

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
	 *
	 * @return void
	 */
	public function testGetHookNameParam( $testMarker, $expectedResult ) {
		$stackPtr     = $this->getTargetToken( $testMarker, \T_STRING );
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
	 * @return array<string, array<string, string|false>>
	 */
	public static function dataGetHookNameParam() {
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
		);
	}
}
