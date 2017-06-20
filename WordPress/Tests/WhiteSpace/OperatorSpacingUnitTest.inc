<?php

// All OK.
if ( 'bb' !== 'bb' ) {
	if (
		empty( $_GET['refid'] ) &&
		empty( $_GET['nolinks'] ) &&
		! is_page_template( 'page_strategy-center.php' ) &&
		! is_page_template( 'page_confirmation.php' ) &&
		! is_page_template( 'page_debartolo.php' ) &&
		! is_singular( 'offer' )
	) {
		hello();
	}
}

// Good.
if ( ! $var ) {
	// ...
}

// Bad.
if (!$var ) {
	// ...
}

// Bad.
if (  !   $var ) {
	// ...
}
