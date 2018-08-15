<?php
/**
 * @see No confusion with @ used in comments.
 */
if (@in_array($array, $needle)) {
    echo '@';
    echo @some_userland_function( $param );
    $file = @MyClass::file_get_contents( $file );
    $file = @\MyNS\MyClass::file_get_contents( $file );
    $file = @\MyNS\file_get_contents( $file );
    $file = @$obj->file_get_contents( $file );
}

// File extension.
if ( @&file_exists( $filename ) && @ /*comment*/ is_readable( $filename ) ) {
	$file = @ \file( $filename );
}

$fp = @fopen('http://www.example.com', 'r', false);
@fpassthru($fp);
@fclose($fp);

// Directory extension.
if (@is_dir($dir)) {
    if ($dh = @\opendir($dir)) {
        while (($file = @readdir($dh)) !== false) {
            echo "filename: $file : filetype: " . @\filetype($dir . $file) . "\n";
        }
        @closedir($dh);
    }
}
$files1 = @ & scandir($dir);

// FTP extension.
$conn_id = @ftp_connect($ftp_server);
$login_result = @ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
if ( @ftp_fget($conn_id, $handle, $remote_file, FTP_ASCII, 0 ) ) {
	@ftp_rename($conn_id, $remote_file, $new_name);
}
@ftp_close($conn_id);

// @codingStandardsChangeSetting WordPress.PHP.NoSilencedErrors custom_whitelist fgetcsv,hex2bin
while ( ( $csvdata = @fgetcsv( $handle, 2000, $separator ) ) !== false ) {}
echo @some_userland_function( $param );
$decoded = @hex2bin( $data );
// @codingStandardsChangeSetting WordPress.PHP.NoSilencedErrors custom_whitelist false

$decoded = @hex2bin( $data );

$unserialized = @unserialize( $str );
