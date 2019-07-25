<?php

esc_html( 'text' );
esc_html( $var );

esc_html( 'text', 'domain' ); // Warning.
esc_html( $foo, $bar ); // Warning.
esc_attr( 'text', MY_DOMAIN ); // Warning.
