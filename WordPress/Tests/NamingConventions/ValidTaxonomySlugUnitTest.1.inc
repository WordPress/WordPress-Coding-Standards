<?php

register_taxonomy( 'my-own-taxonomy', array() ); // OK.
register_taxonomy( 'my_own_taxonomy', array() ); // OK.
register_taxonomy( 'my-own-taxonomy-tooooooooooo-long', array() ); // Bad.
register_taxonomy( 'post_tag', array() ); // Bad. Reserved slug name.
register_taxonomy( 'My-Own-taxonomy', array() ); // Bad. Invalid chars: uppercase.
register_taxonomy( 'my/own/taxonomy', array() ); // Bad. Invalid chars: "/".

register_taxonomy( <<<EOD
my_own_taxonomy
EOD
); // OK.
register_taxonomy( <<<'EOD'
my_own_taxonomy
EOD
); // OK.

register_taxonomy( <<<'EOD'
my/own/taxonomy
EOD
); // Bad. Invalid chars: "/".

register_taxonomy( "my_taxonomy_{$i}" ); // Warning, taxonomy may or may not get too long with dynamic contents in the id.

// Non string literals.
register_taxonomy( sprintf( 'my_taxonomy_%d', $i ) ); // Non string literal. Warning with severity: 3
register_taxonomy( post ); // = lowercase global constant. Non string literal. Warning with severity: 3
register_taxonomy( self::ID ); // Non string literal. Warning with severity: 3
register_taxonomy( $taxonomy_name ); // Non string literal. Warning with severity: 3
register_taxonomy( $this->get_taxonomy_id() ); // Non string literal. Warning with severity: 3

register_taxonomy( null, array() ); // Non string literal. Warning with severity: 3
register_taxonomy( 1000, array() ); // Non string literal. Warning with severity: 3

register_taxonomy( 'wp_', array() ); // Bad. Reserved prefix.
register_taxonomy( 'wp_taxonomy', array() ); // Bad. Reserved prefix.

register_taxonomy( '', array() ); // Bad. Empty taxonomy slug.
register_taxonomy( /*comment*/, array() ); // Bad. No taxonomy slug.

register_taxonomy( 'taxonomy_1', array() ); // OK.

register_taxonomy( <<<EOD
my_here_doc_{$i}
EOD
); // Warning, post type may or may not get too long with dynamic contents in the id.

register_taxonomy( "my-own-taxonomy-toooooooooo-long-{$i}" ); // 1x Error, Too long. 1x Warning, post type may or may not get too long with dynamic contents in the id.
register_taxonomy( 'my/own/taxonomy/tooooooooooo/long', array() ); // Bad. Invalid chars: "/" and too long.

register_taxonomy( 'wp_block', array() ); // Bad. Must only error on reserved name, not invalid prefix.

// Test handling of more complex embedded variables and expressions.
register_taxonomy("testing123-${(foo)}-test");
register_taxonomy("testing123-${foo["${bar['baz']}"]}-test");

// Test ignoring invalid function calls/live coding.
register_taxonomy(); // OK, ignore, presume live coding.

// Safeguard support for PHP 8.0+ named parameters.
register_taxonomy( args: array() ); // Bad. No taxonomy slug.
register_taxonomy( args: array(), taxonomy: 'my_own_taxonomy',  ); // OK.
register_taxonomy( args: array(), taxonomy: 'my-own-taxonomy-tooooooooooo-long' ); // Bad.

// Safeguard that the information displayed in the error message is cleaned of comments.
register_taxonomy(
	// 'Taxonomy' name.
	$name,// Non string literal. Warning with severity: 3
);
