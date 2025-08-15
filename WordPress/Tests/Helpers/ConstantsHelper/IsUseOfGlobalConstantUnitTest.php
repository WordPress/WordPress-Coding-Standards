<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Helpers\ConstantsHelper;

use PHPCSUtils\BackCompat\Helper;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use WordPressCS\WordPress\Helpers\ConstantsHelper;

/**
 * Tests for the `ConstantsHelper::is_use_of_global_constant()` utility method.
 *
 * @since 3.4.0
 *
 * @covers \WordPressCS\WordPress\Helpers\ConstantsHelper::is_use_of_global_constant
 */
final class IsUseOfGlobalConstantUnitTest extends UtilityMethodTestCase {

	/**
	 * Test is_use_of_global_constant() returns false if the token does not exist.
	 *
	 * @return void
	 */
	public function testIsUseOfGlobalConstantReturnsFalseIfTokenDoesNotExist() {
		$this->assertFalse(
			ConstantsHelper::is_use_of_global_constant(
				self::$phpcsFile,
				-1
			)
		);
	}

	/**
	 * Test is_use_of_global_constant().
	 *
	 * @dataProvider dataIsUseOfGlobalConstant
	 *
	 * @param string      $testMarker     The comment which prefaces the target token in the test file.
	 * @param bool        $expectedResult The expected return value.
	 * @param int|null    $tokenType      Optional. The token type to use for the target token.
	 * @param string|null $tokenContent   Optional. The token content to use for the target token.
	 *
	 * @return void
	 */
	public function testIsUseOfGlobalConstant( $testMarker, $expectedResult, $tokenType = \T_STRING, $tokenContent = null ) {
		$stackPtr = $this->getTargetToken( $testMarker, $tokenType, $tokenContent );
		$result   = ConstantsHelper::is_use_of_global_constant( self::$phpcsFile, $stackPtr );

		$this->assertSame( $expectedResult, $result );
	}

	/**
	 * Data provider.
	 *
	 * @see testIsUseOfGlobalConstant()
	 *
	 * @return array<string, array<string, bool|int|string>>
	 */
	public static function dataIsUseOfGlobalConstant() {
		$phpcs_version = Helper::getVersion();
		$is_phpcs_4    = version_compare( $phpcs_version, '3.99.99', '>' );

		return array(
			// Cases that should return false.
			'variable_assignment' => array(
				'testMarker'     => '/* testVariableAssignment */',
				'expectedResult' => false,
				'tokenType'      => \T_VARIABLE,
			),
			'function_call' => array(
				'testMarker'     => '/* testFunctionCall */',
				'expectedResult' => false,
			),
			'function_declaration' => array(
				'testMarker'     => '/* testFunctionDeclaration */',
				'expectedResult' => false,
			),
			'class_instantiation' => array(
				'testMarker'     => '/* testClassInstantiation */',
				'expectedResult' => false,
			),
			'static_method_call' => array(
				'testMarker'     => '/* testStaticMethodCall */',
				'expectedResult' => false,
			),
			'static_class_constant_access' => array(
				'testMarker'     => '/* testStaticClassConstantAccess */',
				'expectedResult' => false,
			),
			'class_name_resolution' => array(
				'testMarker'     => '/* testClassNameResolution */',
				'expectedResult' => false,
			),
			'namespace_declaration' => array(
				'testMarker'     => '/* testNamespaceDeclaration */',
				'expectedResult' => false,
			),
			'use_statement' => array(
				'testMarker'     => '/* testUseStatement */',
				'expectedResult' => false,
			),
			'class_extends' => array(
				'testMarker'     => '/* testClassExtends */',
				'expectedResult' => false,
			),
			'class_implements' => array(
				'testMarker'     => '/* testClassImplements */',
				'expectedResult' => false,
			),
			'class_instantiation_no_parentheses' => array(
				'testMarker'     => '/* testClassInstantiationNoParentheses */',
				'expectedResult' => false,
			),
			'instanceof' => array(
				'testMarker'     => '/* testInstanceof */',
				'expectedResult' => false,
			),
			'preceded_by_insteadof' => array(
				'testMarker'     => '/* testPrecededByInsteadOf */',
				'expectedResult' => false,
			),
			'goto_label' => array(
				'testMarker'     => '/* testGotoLabel */',
				'expectedResult' => false,
			),
			'preceded_by_as' => array(
				'testMarker'     => '/* testPrecededByAs */',
				'expectedResult' => false,
			),
			'class_declaration' => array(
				'testMarker'     => '/* testClassDeclaration */',
				'expectedResult' => false,
			),
			'interface_declaration' => array(
				'testMarker'     => '/* testInterfaceDeclaration */',
				'expectedResult' => false,
			),
			'trait_declaration' => array(
				'testMarker'     => '/* testTraitDeclaration */',
				'expectedResult' => false,
			),
			'enum_declaration' => array(
				'testMarker'     => '/* testEnumDeclaration */',
				'expectedResult' => false,
			),
			'object_property_access' => array(
				'testMarker'     => '/* testObjectPropertyAccess */',
				'expectedResult' => false,
			),
			'nullsafe_object_property_access' => array(
				'testMarker'     => '/* testNullsafeObjectPropertyAccess */',
				'expectedResult' => false,
			),
			'class_constant_access' => array(
				'testMarker'     => '/* testClassConstantAccess */',
				'expectedResult' => false,
			),
			'preceded_by_public' => array(
				'testMarker'     => '/* testPrecededByPublic */',
				'expectedResult' => false,
			),
			'preceded_by_protected' => array(
				'testMarker'     => '/* testPrecededByProtected */',
				'expectedResult' => false,
			),
			'preceded_by_private' => array(
				'testMarker'     => '/* testPrecededByPrivate */',
				'expectedResult' => false,
			),
			'preceded_by_public_set' => array(
				'testMarker'     => '/* testPrecededByPublicSet */',
				'expectedResult' => false,
			),
			'preceded_by_protected_set' => array(
				'testMarker'     => '/* testPrecededByProtectedSet */',
				'expectedResult' => false,
			),
			'preceded_by_private_set' => array(
				'testMarker'     => '/* testPrecededByPrivateSet */',
				'expectedResult' => false,
			),
			'partially_qualified_namespaced_constant' => array(
				'testMarker'     => '/* testPartiallyQualifiedNamespacedConstant */',
				'expectedResult' => false,
				'tokenType'      => ( true === $is_phpcs_4 ? \T_NAME_QUALIFIED : \T_STRING ),
				'tokenContent'   => ( true === $is_phpcs_4 ? 'MyNamespace\PHP_OS' : 'PHP_OS' ),
			),
			'fully_qualified_namespaced_constant' => array(
				'testMarker'     => '/* testFullyQualifiedNamespacedConstant */',
				'expectedResult' => false,
				'tokenType'      => ( true === $is_phpcs_4 ? \T_NAME_FULLY_QUALIFIED : \T_STRING ),
				'tokenContent'   => ( true === $is_phpcs_4 ? '\MyNamespace\PHP_OS' : 'PHP_OS' ),
			),
			'namespace_relative_constant' => array(
				'testMarker'     => '/* testNamespaceRelativeConstant */',
				'expectedResult' => false,
				'tokenType'      => ( true === $is_phpcs_4 ? \T_NAME_RELATIVE : \T_STRING ),
				'tokenContent'   => ( true === $is_phpcs_4 ? 'namespace\PHP_OS' : 'PHP_OS' ),
			),
			'namespace_relative_sub_constant' => array(
				'testMarker'     => '/* testNamespaceRelativeSubConstant */',
				'expectedResult' => false,
				'tokenType'      => ( true === $is_phpcs_4 ? \T_NAME_RELATIVE : \T_STRING ),
				'tokenContent'   => ( true === $is_phpcs_4 ? 'namespace\Sub\PHP_OS' : 'PHP_OS' ),
			),
			'class_constant_declaration' => array(
				'testMarker'     => '/* testClassConstantDeclaration */',
				'expectedResult' => false,
			),
			'use_const_statement_grouped' => array(
				'testMarker'     => '/* testUseConstStatementGrouped */',
				'expectedResult' => false,
			),
			'use_statement_grouped' => array(
				'testMarker'     => '/* testUseStatementGrouped */',
				'expectedResult' => false,
			),

			// Cases that should return true.
			'echo_statement' => array(
				'testMarker'     => '/* testEchoStatement */',
				'expectedResult' => true,
			),
			'echo_statement_fully_qualified' => array(
				'testMarker'     => '/* testEchoStatementFullyQualified */',
				'expectedResult' => true,
				'tokenType'      => ( true === $is_phpcs_4 ? \T_NAME_FULLY_QUALIFIED : \T_STRING ),
				'tokenContent'   => ( true === $is_phpcs_4 ? '\PHP_OS' : 'PHP_OS' ),
			),
			'const_declaration' => array(
				'testMarker'     => '/* testConstDeclaration */',
				'expectedResult' => true,
			),
			'const_declaration_in_list' => array(
				'testMarker'     => '/* testConstDeclarationInList */',
				'expectedResult' => true,
			),
			'use_const_statement' => array(
				'testMarker'     => '/* testUseConstStatement */',
				'expectedResult' => true,
				'tokenType'      => \T_STRING,
				'tokenContent'   => 'PHP_OS',
			),
		);
	}
}
