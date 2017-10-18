<?php

$sql = $wpdb->prepare( $sql, $replacements ); // OK - no query available to examine - this will be handled by the PreparedSQL sniff.
$sql = $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE id = %d AND user_login = %s", 1, "admin" ); // OK.
$sql = $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE id = %d AND user_login = %s", array( 1, "admin" ) ); // OK.
$sql = $wpdb->prepare( 'SELECT * FROM `table` WHERE `column` = %s AND `field` = %d', 'foo', 1337 ); // OK.
$sql = $wpdb->prepare( 'SELECT DATE_FORMAT(`field`, "%%c") FROM `table` WHERE `column` = %s', 'foo' ); // OK.

/*
 * No placeholders, no need to use prepare().
 */
$sql = $wpdb->prepare( 'SELECT * FROM `table`' ); // Warning.
$sql = $wpdb->prepare( 'SELECT * FROM `table` WHERE id = ' . $id ); // OK - this will be handled by the PreparedSQL sniff.
$sql = $wpdb->prepare( "SELECT * FROM `table` WHERE id = $id" ); // OK - this will be handled by the PreparedSQL sniff.
$sql = $wpdb->prepare( "SELECT * FROM `table` WHERE id = {$id['some%sing']}" ); // OK - this will be handled by the PreparedSQL sniff.
$sql = $wpdb->prepare( 'SELECT * FROM ' . $wpdb->users ); // Warning.
$sql = $wpdb->prepare( "SELECT * FROM `{$wpdb->users}`" );  // Warning.
$sql = $wpdb->prepare( "SELECT * FROM `{$wpdb->users}` WHERE id = $id" ); // OK - this will be handled by the PreparedSQL sniff.

/*
 * No placeholders found, but replacement variable(s) are being passed.
 */
$sql = $wpdb->prepare( 'SELECT * FROM `table`', $something ); // Warning.

/*
 * Test passing invalid replacement placeholder.
 */
$sql = $wpdb->prepare( '%d %1$d %%% %', 1 ); // Bad x 3.
$sql = $wpdb->prepare( '%%%s', 1 ); // OK.
$sql = $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE id = %1\$d AND user_login = %2\$s", 1, "admin" ); // Bad x 2 + 1 warning.
$sql = $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE id = %01.2f AND user_login = %10.10s", 1, "admin" ); // Bad x 2 + 1 warning.
$sql = $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE id = %'.09d AND user_login = %1\$04d", 1, "admin" ); // Bad x 2 + 1 warning.
$sql = $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE id = \"%1\$d\" AND user_login = '%2\$s'", 1, "admin" ); // Bad x 2 + 1 warning.
$sql = $wpdb->prepare( 'SELECT * FROM ' . $wpdb->users . ' WHERE id = \'%1\$d\' AND user_login = "%2\$s"', 1, "admin" ); // Bad x 2 + 1 warning.

/*
 * Test passing quoted replacement placeholder.
 */
$sql = $wpdb->prepare( '"%f"', 1.1 ); // Bad.
$sql = $wpdb->prepare( 'SELECT * FROM `table` WHERE `field` = \'%s\'', 'string' ); // Bad.
$sql = $wpdb->prepare( "SELECT * FROM `table` WHERE `id` = \"%d\"", 1 ); // Bad.
$sql = $wpdb->prepare( <<<EOD
	SELECT *
	FROM $wpdb->users
	WHERE id = '%d'
		AND user_login = "%s"
EOD
	, 1, "admin"
); // Bad x 2.

/*
 * Test passing an incorrect amount of replacement parameters.
 */
$sql = $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE id = %d AND user_login = %s" ); // Bad.
$sql = $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE id = %d AND user_login = %s", 1 ); // Bad.
$sql = $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE id = %d AND user_login = %s", 1, "admin", $variable ); // Bad.
$sql = $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE id = %d AND user_login = %s", array( 1 ) ); // Bad.
$sql = $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE id = %d AND user_login = %s", [1, "admin", $variable] ); // Bad.

$replacements = [1, "admin", $variable];
$sql = $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE id = %d AND user_login = %s", $replacements ); // Bad.
$sql = $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE id = %d AND user_login = %s", $replacements ); // WPCS: PreparedSQLPlaceholders replacement count OK.

// Valid test case as found in WP core /wp-admin/includes/export.php
$esses = array_fill( 0, count($post_types), '%s' );
$where = $wpdb->prepare( "{$wpdb->posts}.post_type IN (" . implode( ',', $esses ) . ')', $post_types ); // Warning.
// Testing that whitelist comment work for this mismatch too.
$where = $wpdb->prepare( "{$wpdb->posts}.post_type IN (" . implode( ',', $esses ) . ')', $post_types ); // WPCS: PreparedSQLPlaceholders replacement count OK.

/*
 * Test correctly recognizing queries using IN in combination with dynamic placeholder creation.
 */
// The proper way to write a query using `IN` won't throw a warning:
$where = $wpdb->prepare(
	sprintf(
		"{$wpdb->posts}.post_type IN (%s)",
		implode( ',', array_fill( 0, count($post_types), '%s' ) )
	),
	$post_types
); // OK.

$where = $wpdb->prepare(
	sprintf(
		"{$wpdb->posts}.post_type IN (%s)
		AND {$wpdb->posts}.post_status IN (%s)",
		implode( ',', array_fill( 0, count($post_types), '%s' ) ),
		implode( ',', array_fill( 0, count($post_statusses), '%s' ) )
	),
	array_merge( $post_types, $post_statusses )
); // OK.

$where = $wpdb->prepare(
	"{$wpdb->posts}.post_type IN ("
		. implode( ',', array_fill( 0, count($post_types), '%s' ) )
		. ") AND {$wpdb->posts}.post_status IN ("
		. implode( ',', array_fill( 0, count($post_statusses), '%s' ) )
		. ')',
	array_merge( $post_types, $post_statusses )
); // OK.

$query = $wpdb->prepare(
	sprintf(
		'SELECT COUNT(ID)
		  FROM `%s`
		 WHERE ID IN (%s)
		   AND post_status = "publish"',
		$wpdb->posts,
		implode( ',', array_fill( 0, count( $post_ids ), '%d' ) )
	) . ' AND post_type = %s',
	array_merge( $post_ids, array( $this->get_current_post_type() ) ),
); // OK.

$results = $wpdb->get_results(
	$wpdb->prepare( '
		SELECT ID
		FROM ' . $wpdb->posts . '
		WHERE ID NOT IN( SELECT post_id FROM ' . $wpdb->postmeta . ' WHERE meta_key = %s AND meta_value = %s )
			AND post_status in( "future", "draft", "pending", "private", "publish" )
			AND post_type in( ' . implode( ',', array_fill( 0, count( $post_types ), '%s' ) ) . ' )
		LIMIT %d',
		$replacements
	),
	ARRAY_A
); // OK.

$query = $wpdb->prepare(
	sprintf(
		'SELECT COUNT(ID)
		  FROM `%s`
		 WHERE ID in (%s)
		   AND post_status = "publish"',
		$wpdb->posts,
		implode( ',', array_fill( 0, count( $post_ids ), '%d' ) )
	) . ' AND post_type = %s',
	array_merge( $post_ids, array( $this->get_current_post_type() ) ),
	$another
); // Error - second replacement param is incorrect, with a variable nr of placeholders you always need to pass a replacement array.

$where = $wpdb->prepare(
	sprintf(
		"{$wpdb->posts}.post_type IN ('%s')",
		implode( ',', array_fill( 0, count($post_types), '%s' ) ),
	),
	array_merge( $post_types, $post_statusses )
); // Bad x 2 - %s is quoted, so this won't work properly, will throw incorrect nr of replacements error + quotes found.

$where = $wpdb->prepare(
	"{$wpdb->posts}.post_type IN (\""
		. implode( ',', array_fill( 0, count($post_types), '%s' ) )
		. "\") AND {$wpdb->posts}.post_status IN ('"
		. implode( ',', array_fill( 0, count($post_statusses), '%s' ) )
		. '\')',
	array_merge( $post_types, $post_statusses )
); // Bad x 2 - quotes between the () for the IN.
