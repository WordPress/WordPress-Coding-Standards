<?php

/*
 * The below should *NOT* be recognized as inside a function call to one of the valid functions regardless of the value
 * of the optional parameters (`$global_function` and `$allow_nested`).
 */

$a = /* testPlainAssignment */ 'foo';
another_function( /* testDifferentFunction */ 123 );
$anon_func = function() {
	/* testInsideClosure */ $a = 'foo';
};
$my_function( /* testVariableFunction */ $a );
if ( /* testIfCondition */ true ) {}

/*
 * The below should be recognized as inside a function call to one of the valid functions regardless of the value
 * of the optional parameters (`$global_function` and `$allow_nested`).
 */

/* testLowercaseName */
valid_function1( /* testLowercaseNameInsideCall */ $var );
/* testUppercaseName */
VALID_FUNCTION2( /* testUppercaseNameInsideCall */ 'string' );
/* testFullyQualified */
\valid_function2( /* testFullyQualifiedInsideCall */ 42 );
another_function( /* testNestedInner */ valid_function1( /* testNestedInnerInsideCall */ true ) );

/*
 * The below should only be recognized as inside a function call to one of the valid functions when `$global_function`
 * is `false`.
 */

MyNamespace\/* testNamespacedFunction */valid_function1( /* testNamespacedFunctionInsideCall */ some_function() );
\MyNamespace\/* testFullyQualifiedNamespacedFunction */valid_function1(
	/* testFullyQualifiedNamespacedFunctionInsideCall */ null
);
namespace\MyNamespace\/* testNamespaceRelativeFunction */valid_function2(
	/* testNamespaceRelativeFunctionInsideCall */ 3.14
);
MyClass::/* testStaticMethod */valid_function2(
	/* testStaticMethodInsideCall */ 'text'
);
/* testObjectMethod */
$obj->valid_function1( /* testObjectMethodInsideCall */ array() );
/* testNullsafeObjectMethod */
$obj?->valid_function1( /* testNullsafeObjectMethodInsideCall */ [ 1, 2, 3 ] );

/*
 * The below should only be recognized as inside a function call to one of the valid functions when `$allow_nested`
 * is `true`.
 */

/* testNestedOuter */
valid_function1( another_function( /* testNestedOuterInsideCall */ 'param' ) );
/* testNestedMultipleLevels */
valid_function1( middle_function( inner_function( /* testNestedMultipleLevelsInsideCall */ 999 ) ) );

/*
 * The below should only be recognized as inside a function call to one of the valid functions when both
 * `$global_function` is `false` and `$allow_nested` is `true`.
 */

MyNamespace\/* testNestedBothNamespacedOuter */ valid_function1(
	MyNamespace\other_function(
		/* testNestedBothNamespacedOuterInsideCall */ 'value' . $var
	)
);

/*
 * Safeguard to ensure parentheses in other parameters within the same function call don't confuse the method.
 */
/* testOtherParamsWithParentheses */
valid_function1(
	array( 'key1' => 'value1' ),
	/* testOtherParamsWithParenthesesInsideCall */ $var,
	function() { return 'closure value'; }
);
