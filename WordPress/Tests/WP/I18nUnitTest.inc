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
