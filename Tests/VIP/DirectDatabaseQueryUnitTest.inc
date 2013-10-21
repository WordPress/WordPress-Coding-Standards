<?php

function foo() {
	global $wpdb;

	$listofthings = $wpdb->get_col( 'SELECT something FROM somewhere WHERE someotherthing = 1' ); // Error

	$listofthings = $wpdb->get_col( 'SELECT something FROM somewhere WHERE someotherthing = 1' ); //db call okay

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
		$quux = $wpdb->get_results( $wpdb->prepare( 'SELECT X FROM Y ' ) ); // db call okay (but not really because no wp_cache_set later!)
	}

}
