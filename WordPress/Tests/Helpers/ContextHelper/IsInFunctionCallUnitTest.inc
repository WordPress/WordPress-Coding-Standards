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
valid_function1( /* testLowercaseNameInsideCall */ );
/* testUppercaseName */
VALID_FUNCTION2( /* testUppercaseNameInsideCall */ 'string' );
/* testFullyQualified */
\valid_function2( /* testFullyQualifiedInsideCall */ 42 );

/*
 * The below might be recognized as inside a function call to one of the valid functions or not depending on the values
 * passed to the optional parameters (`$global_function` and `$allow_nested`).
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
/* testNestedOuter */
valid_function1( another_function( /* testNestedOuterInsideCall */ 'param' ) );
another_function( /* testNestedInner */ valid_function1( /* testNestedInnerInsideCall */ true ) );
/* testNestedMultipleLevels */
valid_function1( middle_function( inner_function( /* testNestedMultipleLevelsInsideCall */ 999 ) ) );
MyNamespace\/* testNestedBothNamespacedOuter */ valid_function1(
	MyNamespace\other_function(
		/* testNestedBothNamespacedOuterInsideCall */ 'value' . $var
	)
);
/* testComplexParametersAlwaysMatch */
valid_function1(
	array( 'key1' => 'value1' ),
	other_function( 'nested' ),
	/* testComplexParametersAlwaysMatchInsideCall */ $var
);
/* testComplexParametersNestedOnly */
valid_function1(
	function() { return 'closure value'; },
	$obj->method(),
	another_function( /* testComplexParametersNestedOnlyInsideCall */ $var )
);
/* testComplexParametersNonGlobal */
$obj->valid_function1(
	array( 'key1' => get_value() ),
	MyClass::helper(),
	/* testComplexParametersNonGlobalInsideCall */ true
);
MyClass::/* testComplexParametersNonGlobalNested */ valid_function1(
	$obj->method(),
	'text',
	array( 'key' => MyNamespace\other_function( /* testComplexParametersNonGlobalNestedInsideCall */ 123 ) )
);
