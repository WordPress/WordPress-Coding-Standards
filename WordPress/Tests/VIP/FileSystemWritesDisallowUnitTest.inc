<?php

file_put_contents( $file, $text, FILE_APPEND ); // Bad.

$fp = fopen( $file, 'a+' );
stream_set_blocking( $fp, 0 );

while ( $count > $loop ) {
	if ( flock( $fp, LOCK_EX ) ) { // Bad.
		fwrite( $fp, $text ); // Bad.
	}
	flock( $fp, LOCK_UN ); // Bad.
}

fclose( $fp );

delete();
fputcsv();
fputs();
ftruncate();
is_writable();
is_writeable();
link();
rename();
symlink();
tempnam();
touch();
unlink();

mkdir();
rmdir();

chgrp();
chown();
chmod();
lchgrp();
lchown();

// @codingStandardsChangeSetting WordPress.VIP.FileSystemWritesDisallow exclude directory
mkdir();

// @codingStandardsChangeSetting WordPress.VIP.FileSystemWritesDisallow exclude false


// Issue #956.
use Fieldmanager_Link as Link;

namespace Fieldmanager\Link\Something;
namespace Fieldmanager\Link;
