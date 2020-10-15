<?php

// These calls are slow and type casting is more performant. Warning.
intval( '10' );
strval( 100 );
floatval( '100' );
boolval( 1 );
doubleval( '100' );
$var = 100;
settype( $var, 'string' );
