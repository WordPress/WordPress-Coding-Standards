<?php

__( "hell$varo", 'domain' ); // Bad, shouldn't use a string with variables

__( "hell\$varo", 'domain' ); // Bad, shouldn't use a string with variables

__( $var, 'domain' ); // Bad, shouldn't use variables

__( 'string', SOMETHING ); // Bad, shouldn't use CONSTANTS
