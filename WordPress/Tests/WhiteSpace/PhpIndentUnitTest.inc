<?php

function hwl_home_pagesize( $query ) {
	// Bad, indented with spaces instead of tab
    if ( is_admin() || ! $query->is_main_query() )
		return;
}
add_action( 'pre_get_posts', 'hwl_home_pagesize', 1 );