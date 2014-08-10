<?php
if ( $true == true || $false == false ) {
	echo 'True';
} elseif ( false == $true && true == $false ) {
	echo 'False';
}

if ( $true == true ) {
	echo 'True';
} elseif ( $true == false ) {
	echo 'False';
}

if ( $true == strtolower( $check ) ) {
	echo 'True';
} elseif ( $true == strtolower( ) ) {
	echo 'False';
}

if ( true == (bool) $true ) {
	echo 'True';
} elseif ( false == $true ) {
	echo 'False';
}

if ( $true == 'true' ) {
	echo 'True';
} elseif ( 'false' == $true ) {
	echo 'False';
}

if ( $true == 0 ) {
	echo 'True';
} elseif ( 1 == $false ) {
	echo 'False';
}
