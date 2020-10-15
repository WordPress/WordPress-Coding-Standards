<?php







serialize(); // Warning.
unserialize(); // Warning.

urlencode(); // Warning.
rawurlencode(); // Ok.

dl(); // Warning.
error_reporting(); // Warning.

ini_restore(); // Warning.
magic_quotes_runtime(); // Warning.
set_magic_quotes_runtime(); // Warning.
apache_setenv(); // Warning.
putenv(); // Warning.
set_include_path(); // Warning.
restore_include_path(); // Warning.

// Obfuscation functions. Warning.
base64_decode( 'VGhpcyBpcyBhbiBlbmNvZGVkIHN0cmluZw==');
base64_encode( 'This is an encoded string' );
convert_uudecode( "+22!L;W9E(%!(4\"$`\n`" );
convert_uuencode( "test\ntext text\r\n" );
str_rot13( 'The quick brown fox jumps over the lazy dog.' );

// PHP system calls are often disabled by server admins. Warning.
exec( 'whoami' );
passthru( 'cat myfile.zip', $err );
$process = proc_open( 'php', $descriptorspec, $pipes, $cwd, $env );
$output = shell_exec( 'ls -lart' );
$last_line = system( 'ls', $retval );
$handle = popen( '/bin/ls', 'r' );

// Issue #898.
class System {}
class Serialize {}
