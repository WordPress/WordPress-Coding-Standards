<?php
/**
 * @see No confusion with @ used in comments.
 */
if (@in_array($array, $needle)) { // Bad.
    echo '@';
    echo @some_userland_function( $param ); // Bad.
    $file = @MyClass::file_get_contents( $file ); // Bad.
    $file = @\MyNS\MyClass::file_get_contents( $file ); // Bad.
    $file = @\MyNS\file_get_contents( $file ); // Bad.
    $file = @$obj->file_get_contents( $file ); // Bad.
}

// File extension.
if ( @&file_exists( $filename ) && @ /*comment*/ is_readable( $filename ) ) {
	$file = @ \file( $filename );
}

$fp = @fopen('https://www.example.com', 'r', false);
@fpassthru($fp); // Bad.
@fclose($fp); // Bad.

// Directory extension.
if (@is_dir($dir)) {
    if ($dh = @\opendir($dir)) {
        while (($file = @readdir($dh)) !== false) { // Bad.
            echo "filename: $file : filetype: " . @\filetype($dir . $file) . "\n";
        }
        @closedir($dh); // Bad.
    }
}
$files1 = @ & scandir($dir);

// FTP extension.
$conn_id = @ftp_connect($ftp_server); // Bad.
$login_result = @ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
if ( @ftp_fget($conn_id, $handle, $remote_file, FTP_ASCII, 0 ) ) { // Bad.
	@ftp_rename($conn_id, $remote_file, $new_name);
}
@ftp_close($conn_id); // Bad.

// phpcs:set WordPress.PHP.NoSilencedErrors customAllowedFunctionsList[] fgetcsv,hex2bin
while ( ( $csvdata = @fgetcsv( $handle, 2000, $separator ) ) !== false ) {}
echo @some_userland_function( $param ); // Bad.
$decoded = @hex2bin( $data );
// phpcs:set WordPress.PHP.NoSilencedErrors customAllowedFunctionsList[]

$decoded = @hex2bin( $data ); // Bad.

$unserialized = @unserialize( $str );

/*
 * ... and test the same principle again, but now without using the PHP function allow list.
 */
// phpcs:set WordPress.PHP.NoSilencedErrors usePHPFunctionsList false

// File extension.
if ( @&file_exists( $filename ) && @ /*comment*/ is_readable( $filename ) ) { // Bad x2.
	$file = @ \file( $filename ); // Bad.
}

// Directory extension.
if (@is_dir($dir)) { // Bad.
    if ($dh = @\opendir($dir)) { // Bad.
        while (($file = @readdir($dh)) !== false) { // Bad.
            echo "filename: $file : filetype: " . @\filetype($dir . $file) . "\n"; // Bad.
        }
        @closedir($dh); // Bad.
    }
}
$files1 = @ & scandir($dir); // Bad.

/*
 * The custom allowed functions list will be respected even when `usePHPFunctionsList` is set to false.
 */
// phpcs:set WordPress.PHP.NoSilencedErrors customAllowedFunctionsList[] fgetcsv,hex2bin
while ( ( $csvdata = @fgetcsv( $handle, 2000, $separator ) ) !== false ) {}
echo @some_userland_function( $param ); // Bad.
$decoded = @hex2bin( $data );
// phpcs:set WordPress.PHP.NoSilencedErrors customAllowedFunctionsList[]

// phpcs:set WordPress.PHP.NoSilencedErrors usePHPFunctionsList true

// phpcs:set WordPress.PHP.NoSilencedErrors context_length 0
echo @some_userland_function( $param ); // Bad.
// phpcs:set WordPress.PHP.NoSilencedErrors context_length 6
