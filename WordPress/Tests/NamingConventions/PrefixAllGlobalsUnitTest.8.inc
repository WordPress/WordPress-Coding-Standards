<?php

// phpcs:set WordPress.NamingConventions.PrefixAllGlobals prefixes[] acronym,tgmpa

/*
 * This test safeguards a few edge cases and makes sure the sniff bows out correctly when it finds:
 * - $GLOBALS without a key. Note: overwritting the $GLOBALS variable is no longer allowed since PHP 8.1.
 * - $GLOBALS without a key due to live coding.
 *
 * This test file should generate no errors or warnings.
 */

$GLOBALS = 'overwritten';

$GLOBALS

// phpcs:set WordPress.NamingConventions.PrefixAllGlobals prefixes[]
