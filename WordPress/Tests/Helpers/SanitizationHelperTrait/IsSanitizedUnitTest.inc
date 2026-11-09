<?php

/*
 * Test cases that should not be considered sanitized.
 */

/* testNotWithinFunctionCall */
$var = $_GET['bar'];

/* testNonSanitizingFunction */
strtolower( $_REQUEST['name'] );

/* testInnerFunctionNotSanitizing */
sanitize_text_field( strtolower( get_input() ) ); // T_STRING entry token: not reached via the sniffs that call the method (they only ever pass a T_VARIABLE), but the method supports any token type.

/* testUnslashedInNonSanitizingFunction */
strtolower( wp_unslash( $_POST['content'] ) );

/* testPartiallyQualified */
MyNamespace\sanitize_text_field( $_POST['title'] );

/* testFullyQualifiedNamespaced */
\MyNamespace\absint( $_GET['id'] );

/* testNamespaceRelative */
namespace\array_map( 'sanitize_text_field', $_REQUEST['items'] ); // The method should recognize this as sanitized once it can resolve relative namespaces since this test file is not namespaced.

/* testNamespaceRelativeSub */
namespace\Sub\wp_unslash( $_SERVER['foo'] );

$obj->sanitize_text_field( /* testMethodCall */ $_POST['title'] );

/* testStaticMethodCall */
MyClass::sanitize_text_field( $_POST['title'] );

/* testArrayWalkingNonSanitizingCallback */
array_map( 'strtolower', $_POST['items'] );

/* testArrayWalkingNonStringCallback */
array_map( array( $obj, 'sanitize_text_field' ), $_POST['items'] );

/* testArrayWalkingMissingCallback */
map_deep( value: $_POST['items'] ); // Missing the required callback argument, but that's not the concern of the method.

/*
 * Test cases that should be considered sanitized.
 */

/* testInUnset */
unset( $_COOKIE['temp'] );

/* testSafeCast */
(int) $_POST['count'];

/* testSanitizingFunction */
sanitize_text_field( $_GET['name'] );

/* testSanitizingAndUnslashingFunction */
\absint( $_POST['id'] );

/* testUnslashedThenSanitized */
sanitize_text_field( wp_unslash( $_POST['name'] ) );

/* testArrayWalkingSanitizingCallback */
array_map( 'sanitize_text_field', $_POST['items'] );

sanitize_text_field( /* testStringTokenSanitized */ get_input() ); // T_STRING entry token: not reached via the sniffs that call the method (they only ever pass a T_VARIABLE), but the method supports any token type.

/*
 * Unslashing function call wrapped in a sanitizing function, across the namespace forms of the inner call.
 * All of these currently return `true`.
 *
 * `\wp_unslash()` is the fully qualified global unslashing function, so `true` is correct in every version.
 *
 * `MyNamespace\wp_unslash()`, `\MyNamespace\wp_unslash()` and `namespace\Sub\wp_unslash()` are namespaced
 * functions distinct from the global one, so the method should return `false`. They are false positives in
 * PHPCS 3.x due to https://github.com/WordPress/WordPress-Coding-Standards/issues/2665. PHPCS 4.x returns
 * `false` correctly.
 *
 * `namespace\wp_unslash()` is the global function in this non-namespaced file, so it should be `true`, but
 * the method does not resolve relative names: like the namespaced forms it returns `true` in PHPCS 3.x (via
 * the same bug) and `false` in PHPCS 4.x.
 */
/* testFullyQualifiedGlobalUnslashSanitized */
sanitize_text_field( \wp_unslash( $_POST['foo'] ) );
/* testPartiallyQualifiedUnslashSanitized */
sanitize_text_field( MyNamespace\wp_unslash( $_POST['foo'] ) );
/* testFullyQualifiedNamespacedUnslashSanitized */
sanitize_text_field( \MyNamespace\wp_unslash( $_POST['foo'] ) );
/* testNamespaceRelativeUnslashSanitized */
sanitize_text_field( namespace\wp_unslash( $_POST['foo'] ) );
/* testNamespaceRelativeSubUnslashSanitized */
sanitize_text_field( namespace\Sub\wp_unslash( $_POST['foo'] ) );
