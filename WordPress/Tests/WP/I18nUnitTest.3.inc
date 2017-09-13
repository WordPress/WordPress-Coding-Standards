<?php

/*
 * Test that all checks relating to the text domain still work.
 * Text domain for this test file is checked as if it were passed
 * from the command-line.
 */
__( 'string', 'something' ); // OK.

__( 'string' ); // Bad, no text domain.
_n( 'I have a cat.', 'I have two cats.', $number ); // Bad, no text domain.

__( 'string', SOMETHING ); // Bad, shouldn't use CONSTANTS.
__( 'string', $domain ); // Bad, shouldn't use variable for domain.
__( 'string', 'something' . $domain ); // Bad, shouldn't use variable for domain.
__( 'string', $domain . 'something' ); // Bad, shouldn't use variable for domain.
__( 'string', "$domain" ); // Bad, shouldn't use variable for domain.
__( 'string', "something-$domain" ); // Bad, shouldn't use variable for domain.

__( 'string', 'something-else' ); // Bad, text domain mismatch.
__( 'string', "something-else" ); // Bad, text domain mismatch.
