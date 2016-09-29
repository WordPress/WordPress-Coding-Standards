<?php

eval( 'some_code' ); // Error.

dl(); // Error.
error_reporting(); // Error.
ini_alter(); // Error.
ini_restore(); // Error.
ini_set(); // Error.
magic_quotes_runtime(); // Error.
set_magic_quotes_runtime(); // Error.
apache_setenv(); // Error.
putenv(); // Error.
set_include_path(); // Error.
restore_include_path(); // Error.

// Obfuscation and evil functions.
base64_decode( 'VGhpcyBpcyBhbiBlbmNvZGVkIHN0cmluZw==');
base64_encode( 'This is an encoded string' );
convert_uudecode( "+22!L;W9E(%!(4\"$`\n`" );
convert_uuencode( "test\ntext text\r\n" );
str_rot13( 'The quick brown fox jumps over the lazy dog.' );
eval( "\$str = \"$str\";" );

// Should not change server PHP settings.
ini_set( 'memory_limit' );

// PHP system calls are often disabled by server admins and should not be in themes.
exec( 'whoami' );
passthru( 'cat myfile.zip', $err );
$process = proc_open( 'php', $descriptorspec, $pipes, $cwd, $env );
$output = shell_exec( 'ls -lart' );
$last_line = system( 'ls', $retval );
$handle = popen( '/bin/ls', 'r' );
