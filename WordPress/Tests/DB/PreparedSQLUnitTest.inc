<?php

$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . $_GET['title'] . "';" ); // Bad.
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '{$_GET['title']}';" ); // Bad.
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '$var';" ); // Bad.
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE 'Hello World!';" ); // Ok.
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '{$_GET['title']}';" ) ); // Bad.
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '$var';" ) ); // Bad.
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE %s;", $_GET['title'] ) ); // Ok.

$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . $escaped_var . "';" ); // WPCS: unprepared SQL OK.
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '{$escaped_var}';" ); // WPCS: unprepared SQL OK.

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
	implode( ',', array_fill( 0, count( $post_ids ), '%d' ) )
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

wpdb::prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . foo() . "';" ); // Bad.

$wpdb->query( // Some arbitrary comment.
	"SELECT *
		FROM $wpdb->posts
		WHERE post_title LIKE '" . $escaped_var . "';"
); // Bad x 1.

$wpdb->query(
	"SELECT *
		FROM $wpdb->posts
		WHERE post_title LIKE '" . $escaped_var . "';"
); // WPCS: unprepared SQL OK.

$wpdb->query( // WPCS: unprepared SQL OK.
	"SELECT *
		FROM $wpdb->posts
		WHERE post_title LIKE '" . $escaped_var . "';"
);

$wpdb->query( "SELECT * FROM $wpdb->posts WHERE ID = " . (int) $foo . ";" ); // Ok.

$wpdb->query( "SELECT * FROM $wpdb->posts WHERE value = " . (float) $foo . ";" ); // Ok.

$wpdb->query(
	"SELECT * FROM $wpdb->posts
	WHERE ID = "
	. absint // Ok.
		( $foo )
	. ";"
);

// Don't throw an error during live coding.
wpdb::prepare( "SELECT * FROM $wpdb->posts
