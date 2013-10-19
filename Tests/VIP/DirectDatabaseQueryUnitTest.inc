<?

function bar() {
	global $wpdb;

	$listofthings = $wpdb->get_col( 'SELECT something FROM somewhere WHERE someotherthing = 1' );

	$listofthings = $wpdb->get_col( 'SELECT something FROM somewhere WHERE someotherthing = 1' ); //db call okay

	return $listofthings;
}

function bar() {
	global $wpdb;

	if ( ! ( $listofthings = wp_cache_get( $foo ) ) ) {
		$listofthings = $wpdb->get_col( 'SELECT something FROM somewhere WHERE someotherthing = 1' );
		wp_cache_set( 'foo', $listofthings );
	}

	return $listofthings;
}
