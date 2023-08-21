<?php

// phpcs:set WordPress.NamingConventions.PrefixAllGlobals prefixes[] acronym,tgmpa

/*
 * This test safeguards yet another edge case to make sure the sniff bows out correctly
 * for unfinished global statements within functions (live coding/parse error).
 *
 * This test file should generate no errors or warnings.
 */

function acronym_unfinished_global_statement($param) {
	global $var1, $var2 // Deliberately missing semi-colon.
	
	foreach ($param as $var2) {}
}

// phpcs:set WordPress.NamingConventions.PrefixAllGlobals prefixes[]
