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
