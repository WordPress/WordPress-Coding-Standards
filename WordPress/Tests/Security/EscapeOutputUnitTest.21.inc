<?php

echo ClassName::someMethodName( $attributes, $test ); // Bad.

echo ClassName::someMethodName( $attributes, $test ), $someOtherUnescapedContent; // Bad x2.

echo ClassName::someMethodName( $attributes, $test ), esc_html($someOtherUnescapedContent); // Bad, but only for the static method that is not escaped.

echo $someOtherUnescapedContent, ClassName::someMethodName( $attributes, $test ), // Bad x2
esc_html($someOtherUnescapedContent);

echo $someOtherUnescapedContent, // Bad
ClassName::someMethodName( $attributes, $test ), // Bad
esc_html($someOtherUnescapedContent);

echo $someOtherUnescapedContent, ClassName::someMethodName( $attributes, $test ), $check_me; // Bad x3.

// These are covered in no 1 test, but should be all ok.
_deprecated_function( __METHOD__, 'x.x.x', ClassName::class ); // OK.
die( self::CLASS . ' has been abandoned' ); // OK.
_deprecated_function( __METHOD__, 'x.x.x', parent::Class ); // OK.
_deprecated_function( __METHOD__, 'x.x.x', static::class ); // OK.
_deprecated_function( __METHOD__, 'x.x.x', ClassName::escapingMethod() ); // Bad.
echo 'Do not use ' . $object::class ); // OK.

echo MyClass\ClassName::render( // Bad.
	'testing',
	ClassName::props('test-other', $attributes, [ // Should be skipped over, since the wrapping static method is not escaped.
		'someName' => $attributeName
	])
);

echo Example::statMethod(); // Bad.

echo \FullyQualifiedWithoutImport::statMethod(); // Bad.

echo \Namespaced\ClassName::statMethod(); // Bad.

echo Example\Namespaced\ClassName::statMethod(); // Bad.

echo \QualifiedExample\Namespaced\ClassName::statMethod(); // Bad.
