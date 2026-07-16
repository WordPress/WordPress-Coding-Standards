<?php

/*
 * The below should NOT be recognized as being inside an array comparison function call.
 */

/* testBareVariable */ $value;
some_function( /* testOtherFunctionCall */ $value );
MyNamespace\in_array( /* testPartiallyQualifiedFunction */ $value, $haystack );
\MyNamespace\array_search( /* testFullyQualifiedNamespacedFunction */ $value, $haystack );
namespace\array_keys( $array, /* testNamespaceRelativeFunction */ $value ); // The method should start recognizing this as an array comparison function once it can resolve relative namespaces.
namespace\Sub\in_array( /* testNamespaceRelativeSubFunction */ $value, $haystack );
$obj->array_search( /* testObjectMethod */ $value, $haystack );
$obj?->array_keys( $array, /* testNullsafeObjectMethod */ $value );
ArrayHelper::in_array( /* testStaticMethod */ $value, $haystack );
array_keys( /* testArrayKeysFirstParamOnly */ $array );
array_keys( array: /* testArrayKeysWrongNamedParam */ $array, search_value: 'value', strict: true );

/*
 * The below should be recognized as being inside an array comparison function call.
 */

in_array( /* testInArray */ $value, $haystack );
array_search( /* testArraySearch */ $value, $haystack );
array_keys( $array, /* testArrayKeysWithFilterValue */ $value );
array_keys( filter_value: /* testArrayKeysNamedParam */ $value, array: $array );
IN_array( /* testMixedCaseFunction */ $value, $haystack );
\array_search( /* testFullyQualifiedFunction */ $value, $haystack );
\ARRAY_KEYS( $array, /* testFullyQualifiedUpperCaseFunction */ $value );
in_array( my_function( /* testNestedFunctionCall */ $value ), $haystack );
