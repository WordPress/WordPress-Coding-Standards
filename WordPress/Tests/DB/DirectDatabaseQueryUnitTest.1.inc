<?php

function foo() {
	global $wpdb;
	return $wpdb->get_col( 'SELECT something FROM somewhere WHERE someotherthing = 1' ); // Warning x 2.
}

function bar() {
	global $wpdb;

	if ( ! ( $listofthings = wp_cache_get( $foo ) ) ) {
		$listofthings = $wpdb->get_col( 'SELECT something FROM somewhere WHERE someotherthing = 1' ); // Warning direct DB call.
		wp_cache_set( 'foo', $listofthings );
	}

	return $listofthings;
}




function baz() {
	global $wpdb;
	$baz = WP_CACHE_GET( 'baz' );
	if ( false !== $baz ) {
		$wpdb->query( 'ALTER TABLE TO ADD SOME FIELDS' ); // Warning x 2.
		$wpdb->query( $wpdb->prepare( 'CREATE TABLE ' ) ); // Warning x 2.
		$wpdb->query( 'SELECT QUERY' ); // Warning.
		$baz = $wpdb->get_results( $wpdb->prepare( 'SELECT X FROM Y ' ) ); // Warning.
		WP_cache_SET( 'baz', $baz );
	}
}

function quux() {
	global $wpdb;
	$quux = wp_cache_get( 'quux' );
	if ( false !== $quux ) {
		$quux = $wpdb->get_results( $wpdb->prepare( 'SELECT X FROM Y ' ) ); // Bad, no wp_cache_set, results in Warning x 2.
	}
}

function barzd() {
	global $wpdb;
	$autoload = $wpdb->get_var( $wpdb->prepare( "SELECT autoload FROM $wpdb->options WHERE option_name = %s", $option_name ) ); // Warning x 2.
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
	$wpdb->update( $wpdb->users, $data, $where ); // Warning direct DB call.
	$wpdb->replace( $wpdb->users, $data, $where ); // Warning direct DB call.
	$wpdb->delete( $wpdb->users, $data, $where ); // Warning direct DB call.
	$wpdb->query( 'SELECT X FROM Y' ); // Warning direct DB call.

	$wpdb->get_results( 'SELECT X FROM Y' ); // Warning x 2.
	$wpdb->get_row( 'SELECT X FROM Y' ); // Warning x 2.
	$wpdb->get_col( 'SELECT X FROM Y' ); // Warning x 2.

	WP_CACHE_DELETE( 'key', 'group' );
}

// It is OK to use the wp_cache_add() function instead of wp_cache_set().
function cache_add_instead_of_set() {
	global $wpdb;

	$baz = wp_cache_get( 'baz' );
	if ( false !== $baz ) {
		$data = $where = array();

		$wpdb->update( $wpdb->users, $data, $where ); // Warning direct DB call.
		$wpdb->replace( $wpdb->users, $data, $where ); // Warning direct DB call.
		$wpdb->delete( $wpdb->users, $data, $where ); // Warning direct DB call.
		$wpdb->query( 'SELECT X FROM Y' ); // Warning direct DB call.
		$wpdb->get_row( 'SELECT X FROM Y' ); // Warning direct DB call.
		$wpdb->get_col( 'SELECT X FROM Y' ); // Warning direct DB call.
		$baz = $wpdb->get_results( $wpdb->prepare( 'SELECT X FROM Y ' ) ); // Warning direct DB call.

		wp_cache_add( 'baz', $baz );
	}
}

// Database calls in a closure.
$b = function () {
	global $wpdb;

	if ( ! ( $listofthings = \Wp_Cache_Get( $foo ) ) ) {
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
		$wpdb->get_results( 'SELECT X FROM Y' ); // Warning direct DB call.
		my_cacheset( 'key', 'group' );
	}
}

function cache_customB() {
	global $wpdb;
	$quux = my_cacheget( 'quux' );
	if ( false !== $quux ) {
		$wpdb->get_results( 'SELECT X FROM Y' ); // Warning direct DB call.
		my_other_cacheset( 'key', 'group' );
	}
}

function cache_customC() {
	global $wpdb;
	$wpdb->query( 'SELECT X FROM Y' ); // Warning direct DB call.
	my_cachedel( 'key', 'group' );
}

// phpcs:set WordPress.DB.DirectDatabaseQuery customCacheSetFunctions[] my_cacheset
// phpcs:set WordPress.DB.DirectDatabaseQuery customCacheDeleteFunctions[]

function cache_customD() {
	global $wpdb;
	$quux = my_cacheget( 'quux' );
	if ( false !== $quux ) {
		$wpdb->get_results( 'SELECT X FROM Y' ); // Warning direct DB call.
		my_cacheset( 'key', 'group' );
	}
}

function cache_customE() {
	global $wpdb;
	$quux = my_cacheget( 'quux' );
	if ( false !== $quux ) {
		$wpdb->get_results( 'SELECT X FROM Y' ); // Warning x 2.
		my_other_cacheset( 'key', 'group' );
	}
}

function cache_customF() {
	global $wpdb;
	$wpdb->query( 'SELECT X FROM Y' ); // Warning x 2.
	my_cachedel( 'key', 'group' );
}

// phpcs:set WordPress.DB.DirectDatabaseQuery customCacheGetFunctions[]
// phpcs:set WordPress.DB.DirectDatabaseQuery customCacheSetFunctions[]

function cache_customG() {
	global $wpdb;
	$quux = my_cacheget( 'quux' );
	if ( false !== $quux ) {
		$quux = $wpdb->get_results( 'SELECT X FROM Y' ); // Warning x 2.
		my_cacheset( 'key', 'group' );
	}
}

function custom_modify_attachment() {
	global $wpdb;
	$wpdb->update( $wpdb->posts, array( 'post_title' => 'Hello' ), array( 'ID' => 1 ) ); // Warning direct DB call.
	clean_attachment_cache( 1 );
}
function custom_modify_post() {
	global $wpdb;
	$wpdb->update( $wpdb->posts, array( 'post_title' => 'Hello' ), array( 'ID' => 1 ) ); // Warning direct DB call.
	clean_post_cache( 1 );
}
function custom_modify_term() {
	global $wpdb;
	$wpdb->update( $wpdb->terms, array( 'slug' => 'test' ), array( 'term_id' => 1 ) ); // Warning direct DB call.
	clean_term_cache( 1 );
}
function custom_clean_category_cache() {
	global $wpdb;
	$wpdb->update( $wpdb->terms, array( 'slug' => 'test' ), array( 'term_id' => 1 ) ); // Warning direct DB call.
	clean_category_cache( 1 );
}
function custom_modify_links() {
	global $wpdb;
	$wpdb->update( $wpdb->links, array( 'link_name' => 'Test' ), array( 'link_id' => 1 ) ); // Warning direct DB call.
	clean_bookmark_cache( 1 );
}
function custom_modify_comments() {
	global $wpdb;
	$wpdb->update( $wpdb->comments, array( 'comment_content' => 'Test' ), array( 'comment_ID' => 1 ) ); // Warning direct DB call.
	clean_comment_cache( 1 );
}
function custom_modify_users() {
	global $wpdb;
	$wpdb->update( $wpdb->users, array( 'user_email' => 'Test' ), array( 'ID' => 1 ) ); // Warning direct DB call.
	clean_user_cache( 1 );
}
function custom_modify_blogs() {
	global $wpdb;
	$wpdb->update( $wpdb->blogs, array( 'domain' => 'example.com' ), array( 'blog_id' => 1 ) ); // Warning direct DB call.
	clean_blog_cache( 1 );
}
function custom_modify_sites() {
	global $wpdb;
	$wpdb->update( $wpdb->sites, array( 'domain' => 'example.com' ), array( 'id' => 1 ) ); // Warning direct DB call.
	clean_network_cache( 1 );
}
function custom_modify_term_relationship() {
	global $wpdb;
	$wpdb->update( $wpdb->term_relationships, array( 'term_order' => 1 ), array( 'object_id' => 1 ) ); // Warning direct DB call.
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
	); // Warning x 2.

	$listofthings = $wpdb->get_col( <<<EOD
		SELECT something
		FROM somewhere
		WHERE someotherthing = 1
EOD
	); // Warning x 2.

	return $listofthings;
}

function bazbaz() {
	global $wpdb;

	$baz = wp_cache_get( 'baz' );
	if ( false !== $baz ) {

		$wpdb->query( <<<'EOD'
			ALTER TABLE TO ADD SOME FIELDS
EOD
		); // Warning on line 273 + 274.
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
		); // Warning direct DB call.
		$wpdb->get_row( <<<'EOD'
			SELECT X FROM Y
EOD
		);// Warning direct DB call.

		wp_cache_add( 'baz', $baz );
	}
}

// Non-cachable call should bow out after `DirectQuery` warning.
function non_cachable() {
	global $wpdb;
	$wpdb->insert( 'table', array( 'column' => 'foo', 'field' => 'bar' ) ); // Warning direct DB call.
}

// Safeguard recognition of PHP 8.0+ nullsafe object operator.
function nullsafe_object_operator() {
	global $wpdb;
	$listofthings = $wpdb?->get_col( 'SELECT something FROM somewhere WHERE someotherthing = 1' ); // Warning x 2.
	$last = $wpdb?->insert( $wpdb->users, $data, $where ); // Warning x 1.
}

function stabilize_token_walking($other_db) {
	global $wpdb;
	// Overwriting $wpdb is bad, of course, but that's not the concern of this sniff.
	// This test is making sure that the `$other_db->query` code is not flagged as if it were a call to a `$wpdb` method.
	$wpdb = $other_db->query;
}

function ignore_comments() {
	global $wpdb;
	$wpdb-> // Let's pretend this is a method-chain (this is the comment which should not confuse the sniff).
		insert( 'table', array( 'column' => 'foo', 'field' => 'bar' ) ); // Warning direct DB call.
}

function correctly_determine_end_of_statement() {
	global $wpdb;
	$wpdb->get_col( $query, $col ) ?><-- Warning x 2 -->
	<?php
	$next_query = 'ALTER TABLE TO ADD SOME FIELDS' ); // Should not be flagged as not in a call to $wpdb.
}

function stay_silent_for_truncate_query() {
	global $wpdb;
	$wpdb->query(
		$wpdb->prepare(
			'TRUNCATE TABLE `%1$s`',
			plugin_get_table_name( 'Name' )
		)
	);
}

function stay_silent_for_truncate_query_lowercase_sql_keywords() {
	global $wpdb;
	$wpdb->query(
		$wpdb->prepare(
			'truncate table `%1$s`',
			plugin_get_table_name( 'Name' )
		)
	);
}

function method_names_are_caseinsensitive() {
	global $wpdb;
	$autoload = $wpdb->Get_Var( $wpdb->Prepare( "SELECT autoload FROM $wpdb->options WHERE option_name = %s", $option_name ) ); // Warning x 2.
}

// phpcs:set WordPress.DB.DirectDatabaseQuery customCacheGetFunctions[] MY_cacheget
// phpcs:set WordPress.DB.DirectDatabaseQuery customCacheSetFunctions[] my_CACHESET
// phpcs:set WordPress.DB.DirectDatabaseQuery customCacheDeleteFunctions[] MY_cachedel
function cache_custom_mixed_case_A() {
	global $wpdb;
	$quux = my_cacheget( 'quux' );
	if ( false !== $quux ) {
		$wpdb->get_results( 'SELECT X FROM Y' ); // Warning direct DB call.
		my_cacheset( 'key', 'group' );
	}
}

function cache_custom_mixed_case_B() {
	global $wpdb;
	$wpdb->query( 'SELECT X FROM Y' ); // Warning direct DB call.
	my_cachedel( 'key', 'group' );
}

// phpcs:set WordPress.DB.DirectDatabaseQuery customCacheGetFunctions[]
// phpcs:set WordPress.DB.DirectDatabaseQuery customCacheSetFunctions[]
// phpcs:set WordPress.DB.DirectDatabaseQuery customCacheDeleteFunctions[]

// Protect against false negatives where the cache function names are used as the content
// of a T_STRING token that is not a function call.
function notCacheFunctionCalls() {
    global $wpdb;

    $bar->wp_cache_get = 'something';
    $listofthings = $wpdb->get_col( 'SELECT something FROM somewhere WHERE someotherthing = 1' ); // Warning x 2.
    $foo = wp_cache_set;

    return $listofthings;
}

// The sniff deliberately does not distinguish between calls to cache functions and calls to methods with the same name as the functions,
// as those method calls are likely custom cache functions.
function methodNamesSameAsCacheFunctions() {
	global $wpdb, $bar;

	if ( ! ( $listofthings = $bar->wp_cache_get( 'foo' ) ) ) {
		$listofthings = $wpdb->get_col( 'SELECT something FROM somewhere WHERE someotherthing = 1' ); // Warning direct DB call.
		$bar->wp_cache_set( 'foo', $listofthings );
	}

	return $listofthings;
}

/*
 * Safeguard correct handling of namespaced function calls. The sniff deliberately does not distinguish between calls to
 * WP global cache functions and calls to namespaced functions that mirror the name of the WP global cache functions as
 * those are likely custom cache functions. This is consistent with the behavior for method calls (see the
 * methodNamesSameAsCacheFunctions() test above).
 */
function callToFullyQualifiedCacheGetSet() {
    global $wpdb;

    if ( ! ( $listofthings = \wp_cache_get( $foo ) ) ) {
        $listofthings = $wpdb->get_col( 'SELECT something FROM somewhere WHERE someotherthing = 1' ); // Warning direct DB call.
        \wp_cache_set( 'foo', $listofthings );
    }

    return $listofthings;
}

function callToQualifiedNamespacedCacheGetSet() {
	global $wpdb;

	if ( ! ( $listofthings = MyNamespace\wp_cache_get( $foo ) ) ) {
		$listofthings = $wpdb->get_col( 'SELECT something FROM somewhere WHERE someotherthing = 1' ); // Warning direct DB call.
		MyNamespace\wp_cache_add( 'foo', $listofthings );
	}

	return $listofthings;
}

function callToFullyQualifiedNamespacedCacheGetSet() {
	global $wpdb;

	if ( ! ( $listofthings = \MyNamespace\wp_cache_get( $foo ) ) ) {
		$listofthings = $wpdb->get_col( 'SELECT something FROM somewhere WHERE someotherthing = 1' ); // Warning direct DB call.
		\MyNamespace\wp_cache_set( 'foo', $listofthings );
	}

	return $listofthings;
}

function callToRelativeNamespacedCacheGetSet() {
	global $wpdb;

	if ( ! ( $listofthings = namespace\wp_cache_get( $foo ) ) ) {
		$listofthings = $wpdb->get_col( 'SELECT something FROM somewhere WHERE someotherthing = 1' ); // Warning direct DB call.
		namespace\wp_cache_add( 'foo', $listofthings );
	}

	return $listofthings;
}

function callToMultiLevelNamespaceRelativeCacheGetSet() {
	global $wpdb;

	if ( ! ( $listofthings = namespace\Sub\wp_cache_get( $foo ) ) ) {
		$listofthings = $wpdb->get_col( 'SELECT something FROM somewhere WHERE someotherthing = 1' ); // Warning direct DB call.
		namespace\Sub\wp_cache_set( 'foo', $listofthings );
	}

	return $listofthings;
}

function callToFullyQualifiedCacheDelete() {
    global $wpdb;

    $wpdb->query( 'SELECT X FROM Y' ); // Warning direct DB call.
    \wp_cache_delete( 'key', 'group' );
}

function callToQualifiedNamespacedCacheDelete() {
	global $wpdb;

	$wpdb->query( 'SELECT X FROM Y' ); // Warning direct DB call.
	MyNamespace\clean_attachment_cache( 1 );
}

function callToFullyQualifiedNamespacedCacheDelete() {
	global $wpdb;

	$wpdb->query( 'SELECT X FROM Y' ); // Warning direct DB call.
	\MyNamespace\CLEAN_POST_CACHE( 1 );
}

function callToRelativeNamespacedCacheDelete() {
	global $wpdb;

	$wpdb->query( 'SELECT X FROM Y' ); // Warning direct DB call.
	namespace\clean_user_cache( 1 );
}

function callToMultiLevelNamespaceRelativeCacheDelete() {
	global $wpdb;

	$wpdb->query( 'SELECT X FROM Y' ); // Warning direct DB call.
	namespace\Sub\clean_blog_cache( 1 );
}

// Handle more caching functions.
function cache_multiple() {
	global $wpdb;
	$data = wp_cache_get_multiple( ['keyA', 'keyB'] );
	if ( false !== $data ) {
		$data = $wpdb->get_results( $query ); // Warning direct DB call.
		\wp_cache_add_multiple( $data );
	}
}

function cache_multiple_salted() {
	global $wpdb;
	$data = \wp_cache_get_multiple_salted( ...$params );
	if ( false !== $data ) {
		$data = $wpdb->get_col( $query ); // Warning direct DB call.
		wp_cache_set_multiple_salted( ...$params );
	}
}

function cache_delete_multiple() {
	global $wpdb;
	$wpdb->replace( $query ); // Warning direct DB call.
	WP_cache_delete_multiple( ['keyA', 'keyB'] );
}

function cache_flush_group() {
	global $wpdb;
	$wpdb->delete( $query ); // Warning direct DB call.
	wp_cache_flush_group( 'group' );
}
