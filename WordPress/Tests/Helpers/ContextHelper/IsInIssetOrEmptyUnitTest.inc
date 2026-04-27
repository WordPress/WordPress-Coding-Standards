<?php

/*
 * The below should NOT be recognized as being inside an isset/empty check.
 */

/* testBareVariable */ $value;
some_function( /* testOtherFunctionCall */ $value );
array_key_exists( /* testMissingArrayParam */ $key );
key_exists( /* testKeyParamNotArrayParam */ $value, $array );
MyNamespace\key_exists( 'key', /* testPartiallyQualifiedFunction */ $array );
\MyNamespace\array_key_exists( 'key', /* testFullyQualifiedNamespacedFunction */ $array );
namespace\array_key_exists( 'key', /* testNamespaceRelativeFunction */ $array ); // The method should start recognizing this once it can resolve relative namespaces.
namespace\Sub\key_exists( 'key', /* testNamespaceRelativeSubFunction */ $array );
$obj->array_key_exists( 'key', /* testObjectMethod */ $array );
$obj?->key_exists( 'key', /* testNullsafeObjectMethod */ $array );
MyClass::array_key_exists( 'key', /* testStaticMethod */ $array );
key_exists( 'key', my_function( /* testNestedNonTargetFunctionCall */ $array ) );
$obj->isset( /* testIssetObjectMethod */ $value );
Foo::empty( /* testEmptyStaticMethod */ $value );
MyNamespace\isset( /* testIssetNamespacedFunction */ $value );

/*
 * The below should be recognized as being inside an isset/empty check.
 */

isset( /* testIsset */ $value );
empty( /* testEmpty */ $value );
array_key_exists( 'key', /* testUnqualifiedFunction */ $array );
Key_Exists( 'key', /* testMixedCaseFunction */ $array );
\array_key_exists( 'key', /* testFullyQualifiedFunction */ $array );
\KEY_EXISTS( 'key', /* testFullyQualifiedUpperCaseFunction */ $array );
array_key_exists( array: /* testNamedParamReversedOrder */ $array, key: 'foo' );
array_key_exists( 'key', \key_exists( 'key', /* testNestedValidFunctionCall */ $array ) );
