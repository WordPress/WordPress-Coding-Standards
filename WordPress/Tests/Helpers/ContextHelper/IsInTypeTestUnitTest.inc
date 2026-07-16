<?php

/*
 * The below should NOT be recognized as being inside a type test function call.
 */

/* testBareVariable */ $value;
some_function( /* testOtherFunctionCall */ $value );
MyNamespace\is_numeric( /* testPartiallyQualifiedFunction */ $value );
\MyNamespace\is_array( /* testFullyQualifiedNamespacedFunction */ $value );
namespace\is_bool( /* testNamespaceRelativeFunction */ $value ); // The method should start recognizing this as a type test function once it can resolve relative namespaces.
namespace\Sub\is_float( /* testNamespaceRelativeSubFunction */ $value );
$obj->is_string( /* testObjectMethod */ $value );
$obj?->is_object( /* testNullsafeObjectMethod */ $value );
TypeChecker::is_int( /* testStaticMethod */ $value );
is_scalar( my_function( /* testNestedNonTargetFunctionCall */ $value ) );

/*
 * The below should be recognized as being inside a type test function call.
 */

is_array( /* testUnqualifiedFunction */ $value );
Is_Bool( /* testMixedCaseFunction */ $value );
\is_string( /* testFullyQualifiedFunction */ $value );
\IS_NUMERIC( /* testFullyQualifiedUpperCaseFunction */ $value );
is_int( is_float( /* testNestedTypeTestCall */ $value ) );
