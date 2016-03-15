<?php

__( "hell$varo", 'domain' ); // Bad, shouldn't use a string with variables

__( "hell\$varo", 'domain' ); // OK, Variable is not interpolated.
__( "hell\\$varo", 'domain' ); // Bad, is interpolated.
__( "hell\\\$varo", 'domain' ); // OK, variable is escaped.

__( $var, 'domain' ); // Bad, shouldn't use variables

__( 'string', SOMETHING ); // Bad, shouldn't use CONSTANTS

__( 'string' . $var, 'domain' ); // Bad, shouldn't use variable for string

__( $var . 'string', 'domain' ); // Bad, shouldn't use variable for string

__( SOMETHING, 'domain' ); // Bad, shouldn't use constant for string

__( 'string' . SOMETHING, 'domain' ); // Bad, shouldn't use constant for string

__( SOMETHING . 'string', 'domain' ); // Bad, shouldn't use variable for string

__( 'string', $domain ); // Bad, shouldn't use variable for domain

__( 'string', 'my' . $domain ); // Bad, shouldn't use variable for domain

__( 'string', $domain . 'domain' ); // Bad, shouldn't use variable for domain

__( 'string', 'domain' ); // Good

_x( 'string', 'context', 'domain' ); // Good

_x( 'string', $var, 'domain' ); // Bad, shouldn't use variable for context

_x( 'string', 'context' . $var, 'domain' ); // Bad, shouldn't use variable for context

_x( 'string', $var . 'context', 'domain' ); // Bad, shouldn't use variable for context

_x( 'string', SOMETHING, 'domain' ); // Bad, shouldn't use constant for context

_x( 'string', SOMETHING . 'context', 'domain' ); // Bad, shouldn't use constant for context

_x( 'string', 'context' . SOMETHING, 'domain' ); // Bad, shouldn't use constant for context

_n( 'I have a cat.', 'I have cats.', $number ); // Bad, no text domain.
_n( 'I have a cat.', 'I have cats.', $number, 'my-slug' ); // OK.
_n( 'I have a cat.', 'I have cats.', $number, "illegal $string" ); // Bad.
_n( 'I have a cat.', 'I have cats.', $number, SOMETHING ); // Bad.

_n_noop( 'I have a cat.', 'I have cats.', $number ); // Bad, no text domain.
_n_noop( 'I have a cat.', 'I have cats.', $number, 'my-slug' ); // OK.
_n_noop( 'I have a cat.', 'I have cats.', $number, "illegal $string" ); // Bad.
_n_noop( 'I have a cat.', 'I have cats.', $number, SOMETHING ); // Bad.

_nx( 'I have a cat.', 'I have cats.', $number, 'Not really.' ); // Bad, no text domain.
_nx( 'I have a cat.', 'I have cats.', $number, $context ); // Bad.
_nx( 'I have a cat.', 'I have cats.', $number, 'Not really.', 'my-slug' ); // OK.
_nx( 'I have a cat.', 'I have cats.', $number, $context, 'my-slug' ); // Bad.
_nx( 'I have a cat.', 'I have cats.', $number, 'Not really.', "illegal $string" ); // Bad.
_nx( 'I have a cat.', 'I have cats.', $number, 'Not really.', SOMETHING ); // Bad.

_nx_noop( 'I have a cat.', 'I have cats.', $number, 'Not really.' ); // Bad, no text domain.
_nx_noop( 'I have a cat.', 'I have cats.', $number, $context ); // Bad, no text domain, variable context.
_nx_noop( 'I have a cat.', 'I have cats.', $number, 'Not really.', 'my-slug' ); // OK.
_nx_noop( 'I have a cat.', 'I have cats.', $number, $context, 'my-slug' ); // Bad.
_nx_noop( 'I have a cat.', 'I have cats.', $number, 'Not really.', "illegal $string" ); // Bad.
_nx_noop( 'I have a cat.', 'I have cats.', $number, 'Not really.', SOMETHING ); // Bad.

translate( 'foo', 'bar' ); // Bad, low-level API function.
translate_with_gettext_context( 'foo', 'bar', 'baz' ); // Bad, low-level API function.

_( 'foo', 'bar' );
