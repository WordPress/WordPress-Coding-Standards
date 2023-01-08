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
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE ID = " . absint( $foo ) . ";" ); // Ok.

// Test multi-line strings.
$all_post_meta = $wpdb->get_results( $wpdb->prepare( sprintf(
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
	WHERE post_title LIKE '" . esc_sql( $foo ) . "';"
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
	implode( ',', array_fill( 0, count( $post_ids ), '%d' ) )
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

// Don't throw an error during live coding.
wpdb::prepare( "SELECT * FROM $wpdb->posts
