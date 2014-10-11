<?php

function foo() {
	global $wpdb;

	$listofthings = $wpdb->get_col( 'SELECT something FROM somewhere WHERE someotherthing = 1' ); // Error + Warning

	$listofthings = $wpdb->get_col( 'SELECT something FROM somewhere WHERE someotherthing = 1' ); //db call okay ( No Warning, but Error for not caching! )

	return $listofthings;
}

function bar() {
	global $wpdb;

	if ( ! ( $listofthings = wp_cache_get( $foo ) ) ) {
		$listofthings = $wpdb->get_col( 'SELECT something FROM somewhere WHERE someotherthing = 1' ); // Warning
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

		$wpdb->query( 'ALTER TABLE TO ADD SOME FIELDS' ); // db call okay (but not really because ALTER TABLE!)

		$wpdb->query( $wpdb->prepare( 'CREATE TABLE ' ) ); // db call okay (but not really because CREATE TABLE!)

		$wpdb->query( 'SELECT QUERY' ); // db call okay

		$baz = $wpdb->get_results( $wpdb->prepare( 'SELECT X FROM Y ' ) );

		wp_cache_set( 'baz', $baz );
	}


}

function quux() {
	global $wpdb;
	$quux = wp_cache_get( 'quux' );
	if ( false !== $quux ) {
		$quux = $wpdb->get_results( $wpdb->prepare( 'SELECT X FROM Y ' ) ); // Bad, no wp_cache_set, results in Error + Warning
	}

}

function barzd() {
	global $wpdb;
	$autoload = $wpdb->get_var( $wpdb->prepare( "SELECT autoload FROM $wpdb->options WHERE option_name = %s", $option_name ) ); // db call ok; no-cache ok
}

function taz() {
	/* @var wpdb $wpdb */
	global $wpdb;
	echo $wpdb->insert_id; // Good, no actual call, and doesn't need any caching
}