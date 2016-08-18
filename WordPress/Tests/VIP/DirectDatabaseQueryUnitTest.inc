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
