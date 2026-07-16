<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Helpers\ArrayWalkingFunctionsHelper;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use WordPressCS\WordPress\Helpers\ArrayWalkingFunctionsHelper;

/**
 * Tests for the `ArrayWalkingFunctionsHelper::get_callback_parameter()` method.
 *
 * @since 3.4.0
 *
 * @covers \WordPressCS\WordPress\Helpers\ArrayWalkingFunctionsHelper::get_callback_parameter
 */
final class GetCallbackParameterUnitTest extends UtilityMethodTestCase {

	/**
	 * Test get_callback_parameter() returns false if the token does not exist.
	 *
	 * @return void
	 */
	public function testGetCallbackParameterReturnsFalseIfTokenDoesNotExist() {
		$this->assertFalse( ArrayWalkingFunctionsHelper::get_callback_parameter( self::$phpcsFile, -1 ) );
	}

	/**
	 * Test get_callback_parameter() returns the callback parameter info or false.
	 *
	 * @dataProvider dataGetCallbackParameter
	 *
	 * @param string       $testMarker      The comment which prefaces the target token in the test file.
	 * @param string|false $expectedContent The expected 'clean' content of the callback parameter,
	 *                                      or false if the method should return false.
	 *
	 * @return void
	 */
	public function testGetCallbackParameter( $testMarker, $expectedContent ) {
		$stackPtr = $this->getTargetToken( $testMarker, array( \T_STRING, \T_NAME_FULLY_QUALIFIED ) );
		$result   = ArrayWalkingFunctionsHelper::get_callback_parameter( self::$phpcsFile, $stackPtr );

		if ( false === $expectedContent ) {
			$this->assertFalse( $result );
		} else {
			$this->assertSame( $expectedContent, $result['clean'] );
		}
	}

	/**
	 * Data provider.
	 *
	 * @see testGetCallbackParameter()
	 *
	 * @return array<string, array<string, string|false>>
	 */
	public static function dataGetCallbackParameter() {
		return array(
			// Cases where false should be returned.
			'not_array_walking_function' => array(
				'testMarker'      => '/* testNotArrayWalkingFunction */',
				'expectedContent' => false,
			),
			'callback_param_missing'     => array(
				'testMarker'      => '/* testCallbackParamMissing */',
				'expectedContent' => false,
			),

			// Cases where the callback parameter should be returned.
			'array_map_callback'         => array(
				'testMarker'      => '/* testArrayMapCallback */',
				'expectedContent' => "'sanitize_text_field'",
			),
			'map_deep_mixed_case'        => array(
				'testMarker'      => '/* testMapDeepMixedCase */',
				'expectedContent' => "'esc_html'",
			),
		);
	}
}
