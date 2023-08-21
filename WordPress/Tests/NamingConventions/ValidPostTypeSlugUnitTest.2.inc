<?php

/*
 * Tests with PHP 7.3+ flexible heredoc/nowdoc.
 */
register_post_type( <<<EOD
    my_{$custom}_type
    EOD
); // 1x Warning, post type may or may not get too long with dynamic contents in the id.

register_post_type( <<<'EOD'
		my_own_post_type
	EOD
); // OK.

register_post_type( <<<'EOD'
		my*own+post&type
	EOD
); // Bad. Invalid chars: "/".
