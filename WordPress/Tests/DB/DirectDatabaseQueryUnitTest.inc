<?php

function foo() {
	global $wpdb;

	$listofthings = $wpdb->get_col( 'SELECT something FROM somewhere WHERE someotherthing = 1' ); // Error + Warning.

	$listofthings = $wpdb->get_col( 'SELECT something FROM somewhere WHERE someotherthing = 1' ); // DB call okay ( No Warning, but Error for not caching! ).

	return $listofthings;
}

function bar() {
	global $wpdb;

	if ( ! ( $listofthings = wp_cache_get( $foo ) ) ) {
		$listofthings = $wpdb->get_col( 'SELECT something FROM somewhere WHERE someotherthing = 1' ); // Warning.
		wp_cache_set( 'foo', $listofthings );
	}

	return $listofthings;
}

function dummy() {
}

function baz() {
	global $wpdb;
	$baz = wp_cache_get( 'baz' );
	if ( false !== $baz ) {

		$wpdb->query( 'ALTER TABLE TO ADD SOME FIELDS' ); // DB call okay (but not really because ALTER TABLE!).

		$wpdb->query( $wpdb->prepare( 'CREATE TABLE ' ) ); // DB call okay (but not really because CREATE TABLE!).

		$wpdb->query( 'SELECT QUERY' ); // DB call okay.

		$baz = $wpdb->get_results( $wpdb->prepare( 'SELECT X FROM Y ' ) );

		wp_cache_set( 'baz', $baz );
	}


}

function quux() {
	global $wpdb;
	$quux = wp_cache_get( 'quux' );
	if ( false !== $quux ) {
		$quux = $wpdb->get_results( $wpdb->prepare( 'SELECT X FROM Y ' ) ); // Bad, no wp_cache_set, results in Error + Warning.
	}

}

function barzd() {
	global $wpdb;
	$autoload = $wpdb->get_var( $wpdb->prepare( "SELECT autoload FROM $wpdb->options WHERE option_name = %s", $option_name ) ); // DB call ok; no-cache ok.
}

function taz() {
	/* @var wpdb $wpdb */
	global $wpdb;
	echo $wpdb->insert_id; // Good, no actual call, and doesn't need any caching.
}

// Some $wpdb methods can pass with only deleting the cache.
function cache_delete_only() {
	global $wpdb;

	$data = $where = array();

	// These methods are allowed to be used with just wp_cache_delete().
	$wpdb->update( $wpdb->users, $data, $where ); // DB call ok; OK.
	$wpdb->replace( $wpdb->users, $data, $where ); // DB call ok; OK.
	$wpdb->delete( $wpdb->users, $data, $where ); // DB call ok; OK.
	$wpdb->query( 'SELECT X FROM Y' ); // DB call ok; OK.

	$wpdb->get_results( 'SELECT X FROM Y' ); // DB call ok; Bad.
	$wpdb->get_row( 'SELECT X FROM Y' ); // DB call ok; Bad.
	$wpdb->get_col( 'SELECT X FROM Y' ); // DB call ok; Bad.

	wp_cache_delete( 'key', 'group' );
}

// It is OK to use the wp_cache_add() function in place of wp_cache_set().
function cache_add_instead_of_set() {
	global $wpdb;

	$baz = wp_cache_get( 'baz' );

	if ( false !== $baz ) {

		$data = $where = array();

		$wpdb->update( $wpdb->users, $data, $where ); // DB call ok; OK.
		$wpdb->replace( $wpdb->users, $data, $where ); // DB call ok; OK.
		$wpdb->delete( $wpdb->users, $data, $where ); // DB call ok; OK.
		$wpdb->query( 'SELECT X FROM Y' ); // DB call ok; OK.
		$wpdb->get_row( 'SELECT X FROM Y' ); // DB call ok; OK.
		$wpdb->get_col( 'SELECT X FROM Y' ); // DB call ok; OK.
		$baz = $wpdb->get_results( $wpdb->prepare( 'SELECT X FROM Y ' ) ); // DB call ok; OK.

		wp_cache_add( 'baz', $baz );
	}
}

// Database calls in a closure.
$b = function () {
	global $wpdb;

	if ( ! ( $listofthings = wp_cache_get( $foo ) ) ) {
		$listofthings = $wpdb->get_col( 'SELECT something FROM somewhere WHERE someotherthing = 1' ); // Warning.
		wp_cache_set( 'foo', $listofthings );
	}

	return $listofthings;
};

/*
 * Test using custom properties, setting & unsetting (resetting).
 */
// phpcs:set WordPress.DB.DirectDatabaseQuery customCacheGetFunctions[] my_cacheget
// phpcs:set WordPress.DB.DirectDatabaseQuery customCacheSetFunctions[] my_cacheset,my_other_cacheset
// phpcs:set WordPress.DB.DirectDatabaseQuery customCacheDeleteFunctions[] my_cachedel
function cache_customA() {
	global $wpdb;

	$quux = my_cacheget( 'quux' );
	if ( false !== $quux ) {
		$wpdb->get_results( 'SELECT X FROM Y' ); // DB call ok; OK.
		my_cacheset( 'key', 'group' );
	}
}

function cache_customB() {
	global $wpdb;

	$quux = my_cacheget( 'quux' );
	if ( false !== $quux ) {
		$wpdb->get_results( 'SELECT X FROM Y' ); // DB call ok; OK.
		my_other_cacheset( 'key', 'group' );
	}
}

function cache_customC() {
	global $wpdb;

	$wpdb->query( 'SELECT X FROM Y' ); // DB call ok; OK.
	my_cachedel( 'key', 'group' );
}

// phpcs:set WordPress.DB.DirectDatabaseQuery customCacheSetFunctions[] my_cacheset
// phpcs:set WordPress.DB.DirectDatabaseQuery customCacheDeleteFunctions[]

function cache_customD() {
	global $wpdb;

	$quux = my_cacheget( 'quux' );
	if ( false !== $quux ) {
		$wpdb->get_results( 'SELECT X FROM Y' ); // DB call ok; OK.
		my_cacheset( 'key', 'group' );
	}
}

function cache_customE() {
	global $wpdb;

	$quux = my_cacheget( 'quux' );
	if ( false !== $quux ) {
		$wpdb->get_results( 'SELECT X FROM Y' ); // DB call ok; Bad.
		my_other_cacheset( 'key', 'group' );
	}
}

function cache_customF() {
	global $wpdb;

	$wpdb->query( 'SELECT X FROM Y' ); // DB call ok; Bad.
	my_cachedel( 'key', 'group' );
}

// phpcs:set WordPress.DB.DirectDatabaseQuery customCacheGetFunctions[]
// phpcs:set WordPress.DB.DirectDatabaseQuery customCacheSetFunctions[]

function cache_customG() {
	global $wpdb;

	$quux = my_cacheget( 'quux' );
	if ( false !== $quux ) {
		$quux = $wpdb->get_results( 'SELECT X FROM Y' ); // DB call ok; Bad.
		my_cacheset( 'key', 'group' );
	}
}

function custom_modify_attachment() {
	global $wpdb;
	$wpdb->update( $wpdb->posts, array( 'post_title' => 'Hello' ), array( 'ID' => 1 ) ); // DB call ok; OK.
	clean_attachment_cache( 1 );
}
function custom_modify_post() {
	global $wpdb;
	$wpdb->update( $wpdb->posts, array( 'post_title' => 'Hello' ), array( 'ID' => 1 ) ); // DB call ok; OK.
	clean_post_cache( 1 );
}
function custom_modify_term() {
	global $wpdb;
	$wpdb->update( $wpdb->terms, array( 'slug' => 'test' ), array( 'term_id' => 1 ) ); // DB call ok; OK.
	clean_term_cache( 1 );
}
function custom_clean_category_cache() {
	global $wpdb;
	$wpdb->update( $wpdb->terms, array( 'slug' => 'test' ), array( 'term_id' => 1 ) ); // DB call ok; OK.
	clean_category_cache( 1 );
}
function custom_modify_links() {
	global $wpdb;
	$wpdb->update( $wpdb->links, array( 'link_name' => 'Test' ), array( 'link_id' => 1 ) ); // DB call ok; OK.
	clean_bookmark_cache( 1 );
}
function custom_modify_comments() {
	global $wpdb;
	$wpdb->update( $wpdb->comments, array( 'comment_content' => 'Test' ), array( 'comment_ID' => 1 ) ); // DB call ok; OK.
	clean_comment_cache( 1 );
}
function custom_modify_users() {
	global $wpdb;
	$wpdb->update( $wpdb->users, array( 'user_email' => 'Test' ), array( 'ID' => 1 ) ); // DB call ok; OK.
	clean_user_cache( 1 );
}
function custom_modify_blogs() {
	global $wpdb;
	$wpdb->update( $wpdb->blogs, array( 'domain' => 'example.com' ), array( 'blog_id' => 1 ) ); // DB call ok; OK.
	clean_blog_cache( 1 );
}
function custom_modify_sites() {
	global $wpdb;
	$wpdb->update( $wpdb->sites, array( 'domain' => 'example.com' ), array( 'id' => 1 ) ); // DB call ok; OK.
	clean_network_cache( 1 );
}
function custom_modify_term_relationship() {
	global $wpdb;
	$wpdb->update( $wpdb->term_relationships, array( 'term_order' => 1 ), array( 'object_id' => 1 ) ); // DB call ok; OK.
	clean_object_term_cache( 1 );
}

// Test Nowdocs and Heredocs
function foofoo() {
	global $wpdb;

	$listofthings = $wpdb->get_col( <<<'EOD'
		SELECT something
		FROM somewhere
		WHERE someotherthing = 1
EOD
	); // Error + Warning.

	$listofthings = $wpdb->get_col( <<<EOD
		SELECT something
		FROM somewhere
		WHERE someotherthing = 1
EOD
	); // DB call okay ( No Warning, but Error for not caching! ).

	return $listofthings;
}

function bazbaz() {
	global $wpdb;

	$baz = wp_cache_get( 'baz' );
	if ( false !== $baz ) {

		$wpdb->query( <<<'EOD'
			ALTER TABLE TO ADD SOME FIELDS
EOD
		); // DB call okay (but not really because ALTER TABLE!).
		wp_cache_set( 'baz', $baz );
	}
}

function cache_add_instead_of_setter() {
	global $wpdb;

	$baz = wp_cache_get( 'baz' );

	if ( false !== $baz ) {

		$data = $where = array();

		$wpdb->query( <<<EOD
			SELECT X FROM Y
EOD
		); // DB call ok; OK.
		$wpdb->get_row( <<<'EOD'
			SELECT X FROM Y
EOD
		); // DB call ok; OK.

		wp_cache_add( 'baz', $baz );
	}
}