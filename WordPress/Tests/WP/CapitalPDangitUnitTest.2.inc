<?php

/*
 * Ensure flexible heredoc/nowdocs are handled correctly.
 */

// OK.
$text = <<<EOD
        This is an $explain about WordPress.
    EOD;

// Bad.
$text = <<<'EOD'
    This is an explanation about word-press.
    EOD;
