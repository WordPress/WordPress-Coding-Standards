<?php

/*
 * Tests with PHP 7.3+ flexible heredoc/nowdoc.
 * This must be a separate test case file as it is a parse error in PHP < 7.3.
 */

// Heredocs should only be flagged when they contain interpolated variables or expressions.
echo <<<EOD
	Some text without interpolation.
	EOD;

echo <<<"EOD"
    Some text without interpolation.
    EOD;

echo <<<EOD
		Some text $with interpolation.
		Some text $with interpolation.
		Some text $with interpolation.
	EOD; // Bad x 3.

echo <<<"EOD"
	Some text without interpolation.
	Some text {$with->interpolation}.
	Some text without interpolation.
  EOD; // Bad.
