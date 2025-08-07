<?php

$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . $_GET['title'] . "';" ); // Bad.
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '{$_GET['title']}';" ); // Bad.
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '$var';" ); // Bad.
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE 'Hello World!';" ); // Ok.
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '{$_GET['title']}';" ) ); // Bad.
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '$var';" ) ); // Bad.
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE %s;", $_GET['title'] ) ); // Ok.

$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . $escaped_var . "';" ); // Bad: old-style ignore comment. WPCS: unprepared SQL OK.
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '{$escaped_var}';" ); //  Bad: old-style ignore comment. WPCS: unprepared SQL OK.

$wpdb->query( $wpdb->prepare( "SELECT SUBSTRING( post_name, %d + 1 ) REGEXP '^[0-9]+$'", array( 123 ) ) ); // Ok.
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title = 'The \$_GET var can be evil.' AND ID = %s", array( 123 ) ) ); // Ok.
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title = 'The $_GET[foo] var is evil.' AND ID = %s", array( 123 ) ) ); // Bad.
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title = 'The \\$_GET[foo]// var is evil again.' AND ID = %s", array( 123 ) ) ); // Bad.
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title = 'The \$_GET var can be evil, but $_GET[foo] var is evil.' AND ID = %s", array( 123 ) ) ); // Bad.

$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . foo() . "';" ); // Bad.
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . foo() . "';" ) ); // Bad.

$wpdb->query( "SELECT * FROM " . $wpdb->posts . " WHERE post_title LIKE 'foo';" ); // Ok.

// All OK.
$all_post_meta = $wpdb->get_results( $wpdb->prepare( sprintf(
	'SELECT `post_id`, `meta_value` FROM `%s` WHERE `meta_key` = "sort_order" AND `post_id` IN (%s)',
	$wpdb->postmeta,
	implode( ',', array_fill( 0, count( $post_ids ), '%d' ) )
), $post_ids ) );

$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . esc_sql( $foo ) . "';" ); // Ok.
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE ID = " . ABSINT( $foo ) . ";" ); // Ok.

// Test multi-line strings.
$all_post_meta = $wpdb->get_results( $wpdb->prepare( \SPRINTF(
	'SELECT `post_id`, `meta_value`
	FROM `%s`
	WHERE `meta_key` = "sort_order"
		AND `post_id` IN (%s)',
	$wpdb->postmeta,
	\implode( ',', \array_fill( 0, \count( $post_ids ), '%d' ) )
), $post_ids ) ); // Ok.

$wpdb->query( "
	SELECT *
	FROM $wpdb->posts
	WHERE post_title LIKE '" . \esc_SQL( $foo ) . "';"
); // Ok.

$wpdb->query( $wpdb->prepare( "
	SELECT *
	FROM $wpdb->posts
	WHERE post_title = 'The \\$_GET[foo]// var is evil again.'
		AND ID = %s",
	array( 123 )
) ); // Bad.


// Test heredoc & nowdoc for query.
$wpdb->query( <<<EOT
	SELECT *
	FROM {$wpdb->posts}
	WHERE ID = {$foo};
EOT
); // Bad.

$wpdb->query( <<<"HD"
	SELECT *
	FROM {$wpdb->posts}
	WHERE post_title LIKE '{$var}';
HD
); // Bad.

$all_post_meta = $wpdb->get_results( $wpdb->prepare( sprintf( <<<'ND'
	SELECT `post_id`, `meta_value`
	FROM `%s`
	WHERE `meta_key` = "sort_order"
		AND `post_id` IN (%s)
ND
	, $wpdb->postmeta,
	IMPLODE( ',', array_fill( 0, COUNT( $post_ids ), '%d' ) )
), $post_ids ) ); // OK.

wpdb::prepare( "SELECT * FROM $wpdb?->posts WHERE post_title LIKE '" . foo() . "';" ); // Bad.

$wpdb->query( // Some arbitrary comment.
	"SELECT *
		FROM $wpdb->posts
		WHERE post_title LIKE '" . $escaped_var . "';"
); // Bad x 1.

$wpdb->query( "SELECT * FROM $wpdb->posts WHERE ID = " . (int) $foo . ";" ); // Ok.

$wpdb->query( "SELECT * FROM $wpdb->posts WHERE value = " . (float) $foo . ";" ); // Ok.

$wpdb->query(
	"SELECT * FROM $wpdb->posts
	WHERE ID = "
	. absint // Ok.
		( $foo )
	. ";"
);

// Test handling of more complex embedded variables and expressions.
$wpdb->query( "SELECT * FROM {$wpdb->bar()} WHERE post_title LIKE '{$title->sub()}';" ); // Bad x 1.
$wpdb->query( "SELECT * FROM ${wpdb->bar} WHERE post_title LIKE '${title->sub}';" ); // Bad x 1.
$wpdb->query( "SELECT * FROM ${wpdb->{$baz}} WHERE post_title LIKE '${title->{$sub}}';" ); // Bad x 1.
$wpdb->query( "SELECT * FROM ${wpdb->{${'a'}}} WHERE post_title LIKE '${title->{${'sub'}}}';" ); // Bad x 1.

// More defensive variable checking
$wpdb->query( "SELECT * FROM $wpdb" ); // Bad x 1, $wpdb on its own is not valid.

$wpdb
	-> /*comment*/ query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . $_GET['title'] . "';" ); // Bad.

$wpdb?->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . (int) $foo . "';" ); // OK.
$wpdb?->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . foo() . "';" ); // Bad.

WPDB::prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . foo() . "';" ); // Bad.
$wpdb->Query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . foo() . "';" ); // Bad.

$wpdb->query( "SELECT * FROM $wpdb->posts WHERE value = " . {$foo} . ";" ); // Bad - on $foo, not on the {}.
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE value = " . (array) $foo . ";" ); // Bad - on $foo, not on the (array).
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE value = " . -10 . ";" ); // OK.
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE value = " . +1.0 . ";" ); // OK.
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE value = " . 10 / 2.5 . ";" ); // OK.
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE value = " . ++$foo . ";" ); // Bad - on $foo, not on the ++.

// Safeguard handling of PHP 8.0+ nullsafe object operators found within a query.
$wpdb->query( $wpdb->prepare( 'SELECT * FROM ' . $wpdb::TABLE_NAME . " WHERE post_title LIKE '%s';", '%something' ) ); // OK.
$wpdb->query( $wpdb->prepare( 'SELECT * FROM ' . $notwpdb?->posts . " WHERE post_title LIKE '%s';", '%something' ) ); // Bad.

// Safeguard handling of PHP 7.4+ numeric literals with undersocres and PHP 8.1 explicit octals.
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE value = " . 10_000 . ";" ); // OK.
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE value = " . 0o34 . ";" ); // OK.

// Not a method call.
$wpdb = new WPDB();
$foo = $wpdb->propertyAccess;
echo $wpdb::CONSTANT_NAME;

// Not an identifiable method call.
$wpdb->{$methodName}('query');

/*
 * Safeguard correct handling of all types of namespaced calls to the wpdb::prepare() method.
 *
 * Note that calling wpdb::prepare() statically will result in an error. Still, the tests are included here since the
 * sniff handles those calls.
 *
 * Related to: https://github.com/WordPress/WordPress-Coding-Standards/issues/2710.
 */
\wpdb::prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . foo() . "';" ); // Bad.
\WPDB::prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . foo() . "';" ); // Bad.
MyNamespace\wpdb::prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . foo() . "';" ); // Ok.
\MyNamespace\WPDB::prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . foo() . "';" ); // Ok.
namespace\wpdb::prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . foo() . "';" ); // Ok. The sniff should start flagging this once it can resolve relative namespaces as this test file is not namespaced.
namespace\Sub\wpdb::prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . foo() . "';" ); // Ok.

/*
 * Safeguard correct handling of all types of namespaced calls to PreparedSQLSniff::$SQLEscapingFunctions.
 *
 * Note: The sniff currently has a limitation in how it identifies and counts errors for namespaced function calls that
 * match function names in $SQLEscapingFunctions, $SQLAutoEscapedFunctions, or
 * FormattingFunctionsHelper::$formattingFunctions. When it encounters such a call, it treats the function name as if it
 * were a global function call and skips checking the contents. For example, `MyNamespace\absint( $foo )` should trigger
 * two errors (one for MyNamespace\absint, one for $foo), but currently only triggers one error for "MyNamespace"
 * because the sniff incorrectly treats "absint" as a valid global escaping function and skips its contents.
 * Additionally, multi-level namespace calls like `namespace\Sub\count( $foo )` generate multiple errors (one for
 * "namespace", one for "Sub") instead of recognizing it as a single namespaced function call. This will be easier to
 * fix once only PHPCS 4 is supported. Reported in https://github.com/WordPress/WordPress-Coding-Standards/issues/2648.
 */
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE ID = " . \absint( $foo ) );
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE ID = " . MyNamespace\esc_sql( $foo ) );
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE ID = " . \MyNamespace\intval( $foo, $bar ) );
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE ID = " . namespace\floatval( $foo ) ); // This should NOT be flagged in the future once the sniff is able to resolve relative namespaces.
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE ID = " . namespace\Sub\like_escape( $foo ) );

/*
 * Safeguard correct handling of all types of namespaced calls to PreparedSQLSniff::$SQLAutoEscapedFunctions.
 *
 * Note: See the comment above the $SQLEscapingFunctions tests for details about the sniff's current limitations.
 */
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE ID = " . \count( $foo ) );
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE ID = " . \Count( $foo ) );
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE ID = " . MyNamespace\count( $foo ) );
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE ID = " . \MyNamespace\count( $foo ) );
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE ID = " . namespace\count( $foo ) ); // This should NOT be flagged in the future once the sniff is able to resolve relative namespaces.
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE ID = " . namespace\Sub\count( $foo ) );

/*
 * Safeguard correct handling of all types of namespaced calls to FormattingFunctionsHelper::$formattingFunctions.
 *
 * Note: See the comment above the $SQLEscapingFunctions tests for details about the sniff's current limitations.
 */
$wpdb->get_results( \sprintf( "SELECT * FROM $wpdb->posts WHERE ID = %s", intval( $id ) ) );
$wpdb->get_results( MyNamespace\wp_sprintf( "SELECT * FROM $wpdb->posts WHERE ID = %s", intval( $id ) ) );
$wpdb->get_results( \MyNamespace\sprintf( "SELECT * FROM $wpdb->posts WHERE ID = %s", intval( $id ) ) );
$wpdb->get_results( namespace\wp_sprintf( "SELECT * FROM $wpdb->posts WHERE ID = %s", intval( $id ) ) ); // This should NOT be flagged in the future once the sniff is able to resolve relative namespaces.
$wpdb->get_results( namespace\Sub\sprintf( "SELECT * FROM $wpdb->posts WHERE ID = %s", intval( $id ) ) );
