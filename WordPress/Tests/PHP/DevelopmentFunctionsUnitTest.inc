<?php

error_log(); // Error.
var_dump(); // Error.
var_export(); // Error.
print_r(); // Error.
trigger_error(); // Error.
set_error_handler(); // Error.
debug_backtrace(); // Error.
debug_print_backtrace(); // Error.
wp_debug_backtrace_summary(); // Error.

phpinfo(); // Error.

PHPINFO(); // Error.
error_reporting(); // Error.

/*
 * Test exclude property.
 */
// Exclude one group:
// @codingStandardsChangeSetting WordPress.PHP.DevelopmentFunctions exclude error_log
trigger_error(); // Ok - within excluded group.
phpinfo(); // Error.

// Exclude all groups:
// @codingStandardsChangeSetting WordPress.PHP.DevelopmentFunctions exclude error_log,prevent_path_disclosure
trigger_error(); // Ok - within excluded group.
phpinfo(); // Ok - within excluded group.

// Reset group exclusions.
// @codingStandardsChangeSetting WordPress.PHP.DevelopmentFunctions exclude false
trigger_error(); // Error.
phpinfo(); // Error.
