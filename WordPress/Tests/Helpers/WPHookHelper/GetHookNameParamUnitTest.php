<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Helpers\WPHookHelper;

use PHPUnit\Framework\TestCase;
use WordPressCS\WordPress\Helpers\WPHookHelper;

/**
 * Tests for the `WPHookHelper::get_hook_name_param()` method.
 *
 * @since 3.4.0
 *
 * @covers \WordPressCS\WordPress\Helpers\WPHookHelper::get_hook_name_param
 */
final class GetHookNameParamUnitTest extends TestCase {

	/**
	 * Test get_hook_name_param().
	 *
	 * @dataProvider dataGetHookNameParam
	 *
	 * @param string                  $functionName   The function name to test.
	 * @param array<int, mixed>       $parameters     The parameters array to pass to the method.
	 * @param array<int, mixed>|false $expectedResult The expected return value.
	 *
	 * @return void
	 */
	public function testGetHookNameParam( $functionName, $parameters, $expectedResult ) {
		$this->assertSame(
			$expectedResult,
			WPHookHelper::get_hook_name_param( $functionName, $parameters )
		);
	}

	/**
	 * Data provider.
	 *
	 * @see testGetHookNameParam()
	 *
	 * @return array<string, array<string, array<int, mixed>|false|string>>
	 */
	public static function dataGetHookNameParam() {
		// Note: The parameter arrays use a simplified format instead of the real PassedParameters::getParameters()
		// output as most of the array entries are not relevant for the method under test.
		return array(
			'not_a_hook_function' => array(
				'functionName'   => 'printf',
				'parameters'     => array( 1 => array( 'my_action' ) ),
				'expectedResult' => false,
			),
			'hook_name_param_missing' => array(
				'functionName'   => 'apply_filters_ref_array',
				'parameters'     => array( 2 => array( '$arg' ) ),
				'expectedResult' => false,
			),
			'lowercase_name' => array(
				'functionName'   => 'do_action',
				'parameters'     => array( 1 => array( 'my_action' ) ),
				'expectedResult' => array( 'my_action' ),
			),
			'mixedcase_name' => array(
				'functionName'   => 'aPpLy_FiLtErS',
				'parameters'     => array(
					1 => array( 'my_filter' ),
					2 => array( '$value' ),
				),
				'expectedResult' => array( 'my_filter' ),
			),
			'named_parameter' => array(
				'functionName'   => 'do_action_deprecated',
				'parameters'     => array(
					'args'      => array( '$args' ),
					'hook_name' => array( 'my_action' ),
				),
				'expectedResult' => array( 'my_action' ),
			),
		);
	}
}
