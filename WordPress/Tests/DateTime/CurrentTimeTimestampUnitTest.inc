<?php

current_time(); // OK. Well not really, but not our concern.
current_time( 'mysql', true ); // OK.
current_time( 'Y-m-d' ); // OK.
current_time( self::get_date_format() ); // OK.
current_time( $format, $gmt ); // OK.

current_time( 'timestamp', true ); // Error.

current_Time( <<<'EOD'
U
EOD
, 1 ); // Error.

// Test multi-line function call + interlaced comments handling.
current_time( // Error.
	"timestamp", // Timestamp format.
	true // Use GMT timezone.
);

current_time( 'timestamp', $gmt ); // Warning.
current_time( 'timestamp', false ); // Warning.
current_time( 'U', 0 ); // Warning.
current_time( 'U' ); // Warning.

// Safeguard support for PHP 8.0+ named parameters.
current_time( gmt: false ); // OK. Well not really (missing required $type), but not our concern.
current_time( gmt: true, type: 'mysql', ); // OK.
current_time( type: 'Y-m-d' ); // OK.
current_time( gmt: true, type: 'timestamp' ); // Error.
current_time( gmt: 0, type : 'U' ); // Warning.
