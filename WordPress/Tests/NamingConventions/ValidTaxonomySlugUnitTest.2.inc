<?php

/*
 * Tests with PHP 7.3+ flexible heredoc/nowdoc.
 */
register_taxonomy( <<<EOD
    my_{$custom}_taxonomy
    EOD
); // 1x Warning, taxonomy may or may not get too long with dynamic contents in the id.

register_taxonomy( <<<'EOD'
		my_own_taxonomy
	EOD
); // OK.

register_taxonomy( <<<'EOD'
		my*own+taxo&nomy
	EOD
); // Bad. Invalid chars: "/".
