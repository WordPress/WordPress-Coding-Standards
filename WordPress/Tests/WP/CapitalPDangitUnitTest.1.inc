<?php

/*
 * Test Nowdoc separately as the fixer breaks on this in PHP 5.2.
 */

// Bad.
$text = <<<'EOD'
This is an explanation about word-press.
EOD;
