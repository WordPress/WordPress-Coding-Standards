<?php

// phpcs:set WordPress.NamingConventions.PrefixAllGlobals prefixes[] acronym,tgmpa

/*
 * This test safeguards an edge case and makes sure the sniff bows out correctly during live coding with variable variables.
 * This test file should generate no errors or warnings.
 */

echo ${$testing_non_assignment_variable_variable}['deref']

// phpcs:set WordPress.NamingConventions.PrefixAllGlobals prefixes[]
