<?php

/*
 * The below should NOT be recognized as a $wpdb method call.
 */

/* testNotWpdbVariable */
$db->prepare( 'SELECT * FROM table' );
/* testNotWpdbClass */
DB::esc_like( $find );
$array[] = /* testWpdbNotMethodCall */ $wpdb;

/* testPropertyAccess */
echo $wpdb->num_queries /* testPropertyAccessOpenParen */ ;

/* testFunctionCall */
$result = wpdb( 'SELECT * FROM table' );
/* testNotTargetMethod */
$wpdb->insert /* testNotTargetMethodOpenParen */ ( 'table', array( 'name' => 'value' ) );
/* testUppercaseWpdbVariable */
$WPDB->prepare( 'SELECT * FROM table' );

/* testVariableMethodCall */
$wpdb->$prepare /* testVariableMethodCallOpenParen */ ( 'SELECT * FROM table' );

/* testPartiallyQualified */
MyNamespace\wpdb::esc_like /* testPartiallyQualifiedOpenParen */ ( $find );
/* testFullyQualifiedNamespaced */
\MyNamespace\wpdb::esc_like /* testFullyQualifiedNamespacedOpenParen */ ( $find );
/* testNamespaceRelative */
namespace\wpdb::esc_like /* testNamespaceRelativeOpenParen */ ( $find ); // The method should start recognizing this as a WPDB method call once it can resolve relative namespaces since this test file is not namespaced.
/* testNamespaceRelativeSub */
namespace\Sub\wpdb::esc_like /* testNamespaceRelativeSubOpenParen */ ( $find );

/*
 * The below should be recognized as a $wpdb method call.
 */

/* testObjectOperator */
$wpdb->prepare /* testObjectOperatorOpenParen */ ( 'SELECT * FROM table WHERE id = %d', $id );
/* testNullsafeObjectOperator */
$wpdb?->PREPARE /* testNullsafeObjectOperatorOpenParen */ ( 'SELECT * FROM table WHERE id = %d', $id );
/* testUnqualifiedClassUppercase */
WPDB::esc_like /* testUnqualifiedClassUppercaseOpenParen */ ( $find );
/* testUnqualifiedClassLowercase */
wpdb::esc_like /* testUnqualifiedClassLowercaseOpenParen */ ( $find );
/* testFullyQualifiedGlobalLowercase */
\wpdb::esc_like /* testFullyQualifiedGlobalLowercaseOpenParen */ ( $find );
/* testFullyQualifiedGlobalUppercase */
\WPDB /* comment */ :: /* comment */ esc_like /* testFullyQualifiedGlobalUppercaseOpenParen */ ( $find );
