<?php

register_post_type( 'my-own-post-type', array() ); // OK.
register_post_type( 'my_own_post_type', array() ); // OK.
register_post_type( 'my-own-post-type-too-long', array() ); // Bad.
register_post_type( 'author', array() ); // Bad. Reserved slug name.
register_post_type( 'My-Own-Post-Type', array() ); // Bad. Invalid chars: uppercase.
register_post_type( 'my/own/post/type', array() ); // Bad. Invalid chars: "/".

register_post_type( <<<EOD
my_own_post_type
EOD
); // OK.
register_post_type( <<<'EOD'
my_own_post_type
EOD
); // OK.

register_post_type( <<<'EOD'
my/own/post/type
EOD
); // Bad. Invalid chars: "/".

register_post_type( "my_post_type_{$i}" ); // Warning, post type may or may not get too long with dynamic contents in the id.

// Non string literals.
register_post_type( sprintf( 'my_post_type_%d', $i ) ); // Non string literal. Warning with severity: 3
register_post_type( post ); // = lowercase global constant. Non string literal. Warning with severity: 3
register_post_type( self::ID ); // Non string literal. Warning with severity: 3
register_post_type( $post_type_name ); // Non string literal. Warning with severity: 3
register_post_type( $this->get_post_type_id() ); // Non string literal. Warning with severity: 3

register_post_type( null, array() ); // Non string literal. Warning with severity: 3
register_post_type( 1000, array() ); // Non string literal. Warning with severity: 3

register_post_type( 'wp_', array() ); // Bad. Reserved prefix.
register_post_type( 'wp_post_type', array() ); // Bad. Reserved prefix.

register_post_type( '', array() ); // Bad. Empty post type slug.
register_post_type( /*comment*/, array() ); // Bad. No post type slug.

register_post_type( 'post_type_1', array() ); // OK.

register_post_type( <<<EOD
my_here_doc_{$i}
EOD
); // Warning, post type may or may not get too long with dynamic contents in the id.

register_post_type( "my-own-post-type-too-long-{$i}" ); // 1x Error, Too long. 1x Warning, post type may or may not get too long with dynamic contents in the id.
register_post_type( 'my/own/post/type/too/long', array() ); // Bad. Invalid chars: "/" and too long.

register_post_type( 'wp_block', array() ); // Bad. Must only error on reserved keyword, not invalid prefix.
