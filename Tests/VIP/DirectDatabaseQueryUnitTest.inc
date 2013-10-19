<?

function bar() {
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

function bar() {
	// global $wpdb;
	wp_cache_get();

	$wpdb->query( 'ALTER TABLE TO ADD SOME FIELDS' ); // db call okay

	$wpdb->query( $wpdb->prepare( 'CREATE TABLE ' ) ); // db call okay

	$wpdb->query( $wpdb->prepare( 'SELECT X FROM Y ' ) );

	$wpdb->query( 'SELECT QUERY' ); // db call okay
	
	wp_cache_set();
}