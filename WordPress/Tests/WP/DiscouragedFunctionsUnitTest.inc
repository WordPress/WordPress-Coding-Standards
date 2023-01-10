<?php

query_posts(); // Warning, use WP_Query instead.
wp_reset_query(); // Warning, use wp_reset_postdata instead.

/*
 * Tests which are more specifically for the AbstractFunctionRestrictionsSniff class and Helper methods.
 */

// Ensure the sniff doesn't act on methods calls.
$obj->query_posts(); // OK, not the global function.
MyClass::wp_reset_query(); // OK, not the global function.
$obj?->query_posts(); // OK, not the global function.

// Ensure the sniff doesn't act on namespaced calls.
MyNamespace\query_posts(); // OK, not the global function.
namespace\query_posts(); // OK, not the global function.

// ... but does act on fully qualified function calls.
\query_posts(); // Warning.

// Ensure the sniff doesn't act on functions not listed in the target functions array.
query_post(); // OK, not one of the target functions.

// Ignore, not a function call.
class WP_Reset_Query {}
interface Query_Posts {}
trait WP_Reset_Query {}
enum Query_Posts {}

// Ensure the sniff flags function `use` statements, but not class `use` statements or aliases.
use Query_Posts; // OK.
use function wp_reset_query; // Warning.
use function wp_reset_query as myFunction; // Warning.
use function someOtherFunction as wp_reset_query; // OK, alias, not our target.

// Bug fix: prevent false positive for functions declared to return by reference.
function &query_posts() {} // OK, function declaration, not function use.

// Live coding/parse error.
// This has to be the last test in the file!!!
\query_posts
