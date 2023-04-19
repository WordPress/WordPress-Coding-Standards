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
