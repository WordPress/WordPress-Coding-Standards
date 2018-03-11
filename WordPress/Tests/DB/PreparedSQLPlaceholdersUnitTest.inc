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
$sql = $wpdb->prepare( '%d %1$e %%% % %A %h', 1 ); // Bad x 5.
$sql = $wpdb->prepare( '%%%s', 1 ); // OK.
$sql = $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE id = %1\$d AND user_login = %2\$s", 1, "admin" ); // OK. 2 x warning for unquoted complex placeholders.
$sql = $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE id = %01.2f AND user_login = %10.10X", 1, "admin" ); // Bad x 1 + 1 warning unquoted complex placeholders + 1 warning nr of replacements.
$sql = $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE id = %'.09F AND user_login = %1\$04x", 1, "admin" ); // Bad x 1 + 1 warning unquoted complex placeholders + 1 warning nr of replacements.
$sql = $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE id = \"%1\$c\" AND user_login = '%2\$e'", 1, "admin" ); // Bad x 2 + 1 warning.
$sql = $wpdb->prepare( 'SELECT * FROM ' . $wpdb->users . ' WHERE id = \'%1\$b\' AND user_login = "%2\$o"', 1, "admin" ); // Bad x 2 + 1 warning.

/*
 * Test passing quoted simple replacement placeholder and unquoted complex placeholder.
 */
$sql = $wpdb->prepare( '"%f"', 1.1 ); // Bad.
$sql = $wpdb->prepare( 'SELECT * FROM `table` WHERE `field` = \'%s\'', 'string' ); // Bad.
$sql = $wpdb->prepare( "SELECT * FROM `table` WHERE `id` = \"%d\"", 1 ); // Bad.
$sql = $wpdb->prepare( <<<EOD
	SELECT *
	FROM `%1\$s`
	WHERE id = %2\$d
		AND `%3\$s` = "%4\$s"
EOD
	, $wpdb->users, 1, 'user_login', "admin"
); // Warning x 3, unquoted complex placeholder.

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
		IMPLODE( ',', Array_Fill( 0, count($post_statusses), '%s' ) )
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

/*
 * Test distinguising wildcard _ and %'s in LIKE statements.
 */
$sql = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_content LIKE %s", $like ); // OK.
$sql = $wpdb->prepare( 'SELECT * FROM ' . $wpdb->posts . ' WHERE post_content LIKE \'a string\'' ); // Warning x 2. Like without wildcard, no need for prepare.
$sql = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_content LIKE 'a string'" ); // Warning x 2. Like without wildcard, no need for prepare.
$sql = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_content LIKE '%a string' AND post_status = %s", $status ); // Bad.
$sql = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_content LIKE 'a string%' AND post_status = %s", $status ); // Bad.
$sql = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_content LIKE '%a string%' AND post_status = %s", $status ); // Bad.
$sql = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_content LIKE 'a_string' AND post_status = %s", $status ); // Bad.

// Some VALID test cases as found in plugins published on WP.org.
$comment_id = $wpdb->get_var($wpdb->prepare('SELECT comment_ID FROM ' . $wpdb->comments . ' WHERE comment_post_ID = %d AND comment_agent LIKE %s', intval($post->ID), 'Disqus/1.0:' . $comment_id)); // OK.

$sql = $wpdb->prepare( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s", self::CACHE_KEY_PREFIX . '%' ); // OK.

$wpdb->query($wpdb->prepare('UPDATE '.$wpdb->prefix.'posts SET post_content = REPLACE(post_content, %s, %s) WHERE post_type = "page" AND post_content LIKE %s', $meta_before, $meta_after, '%'.$wpdb->esc_like($meta_before).'%')); // OK.

$query = $wpdb->prepare("DELETE FROM {$wpdb->prefix}options WHERE `option_name` LIKE %s OR `option_name` LIKE %s", "%{$transient_name}%", "%{$transient_timeout_name}%"); // OK.

// Some INVALID test cases as found in plugins published on WP.org.
$wpdb->prepare( "UPDATE $wpdb->posts SET post_status = 'pending' WHERE (post_type LIKE 'product_variation' or post_type LIKE 'product') AND NOT ID IN (". implode(",", $imported_ids ) .")"); // Error x 1 for `product_variation`; warning x 1 for wrong use of LIKE with `product`; the PreparedSQL sniff will also kick in and throw an error about `$imported_ids`.

$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid LIKE '%%%s%%' LIMIT 1;", $img_url ) ); // Bad.

$result = $wpdb->get_col($wpdb->prepare("SELECT guid FROM $wpdb->posts WHERE guid LIKE '%%%s' and post_parent=%d;", $atts['model'], $post->ID )); // Bad.

$comments = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_agent NOT LIKE 'Disqus/%%'", $post->ID) ); // Bad.

$sql = $wpdb->prepare( "SELECT count(*) FROM $this->fontsTable WHERE name LIKE '%%%s%%' OR status LIKE '%%%s%%' OR metadata LIKE '%%%s%%'", $search, $search, $search ); // Bad x 3, the PreparedSQL sniff will also kick in and throw an error about `$this`.

$additional_where = $wpdb->prepare(' AND (network like "%%%s%%" OR ProgramTitle like "%%%s%%" OR TransactionStatus like "%%%s%%" ) ', $search, $search, $search); // Bad x 3.

$robots_query    = $wpdb->prepare( "SELECT name FROM $robots_table WHERE %s LIKE concat('%%',name,'%%')", $http_user_agent ); // Bad, the PreparedSQL sniff will also kick in.

$sql = $wpdb->prepare('SELECT * FROM ' . $wpdb->avatar_privacy . ' WHERE email LIKE "%s"', $email); // Bad (quotes).

$res = $wpdb->query( $wpdb->prepare( 'UPDATE ' . $wpdb->posts . ' SET post_name="feed" WHERE post_name LIKE "feed-%" AND LENGTH(post_name)=6 AND post_type=%s', BAWAS_POST_TYPE ) ); // Bad.

$sql = $wpdb->prepare( "SELECT ID FROM $wpdb->users AS us INNER JOIN $wpdb->usermeta AS mt ON ( us.ID = mt.user_id ) WHERE ( mt.meta_key = 'bbp_last_login' AND mt.meta_value < %s ) AND user_id IN ( SELECT user_id FROM $wpdb->usermeta AS mt WHERE (mt.meta_key = '{$wpdb->prefix}capabilities' AND mt.meta_value LIKE '%%bbp_user%%' ))", $beforegmdate ); // Bad.

$paged_events = $wpdb->get_results( $wpdb->prepare( "SELECT id as event_id FROM {$bp->events->table_name} WHERE ( name LIKE '{$filter}%%' OR description LIKE '{$filter}%%' ) AND id IN ({$gids}) " . $oldevents . " {$pag_sql}" ) ); // Bad x 2, the PreparedSQL sniff will also kick in and throw six errors.

$query = $wpdb->prepare( "SELECT language_code FROM {$wpdb->prefix}icl_translations WHERE element_type LIKE '%%post_%%' AND element_id = %d", $post_ID ); // Bad.

$postID = $wpdb->get_var($wpdb->prepare("SELECT `postID` FROM `".EPDataBase::$table_name."` WHERE `path` like '".$filePath."';")); // OK, the PreparedSQL sniff will kick in and throw four errors.

$wpdb->query($wpdb->prepare("show tables like '$this->table_name'")) > 0; // OK, the PreparedSQL sniff will kick in.

$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->wp_options WHERE option_name LIKE '%widget_gigya%'" ) ); // Bad.

$where .= $wpdb->prepare( " AND `name` LIKE '%%%%" . '%s' . "%%%%' ", $args['name'] ); // Bad x 2.

$wpdb->query($wpdb->prepare("delete from wp_postmeta where post_id = $target_postId AND meta_key like 'google_snippets'")); // Bad, the PreparedSQL sniff will also kick in and throw an error about `$target_postId`.
