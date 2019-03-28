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
// phpcs:set WordPress.PHP.DevelopmentFunctions exclude[] error_log
trigger_error(); // Ok - within excluded group.
phpinfo(); // Error.

// Exclude all groups:
// phpcs:set WordPress.PHP.DevelopmentFunctions exclude[] error_log,prevent_path_disclosure
trigger_error(); // Ok - within excluded group.
phpinfo(); // Ok - within excluded group.

// Reset group exclusions.
// phpcs:set WordPress.PHP.DevelopmentFunctions exclude[]
trigger_error(); // Error.
phpinfo(); // Error.

Wrapper_Class::var_dump(); // OK, not the native PHP function.
$wrapper ->var_dump(); // OK, not the native PHP function.
namespace\var_dump(); // OK as long as the file is namespaced.
MyNamespace\var_dump(); // OK, namespaced function.
