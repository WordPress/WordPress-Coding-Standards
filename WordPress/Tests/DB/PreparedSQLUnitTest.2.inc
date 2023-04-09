<?php

// Safeguard handling of PHP 7.3+ flexible heredoc.
$wpdb->query( <<<EOT
		SELECT *
		FROM {$wpdb->posts}
		WHERE ID = {$foo};
	EOT
); // Bad.

// Live coding.
// This needs to be the last test in a file.
$wpdb->
