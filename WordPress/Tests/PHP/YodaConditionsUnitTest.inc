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
} else {
	echo 'Neither True or False';
}

if ( true == (bool) $true ) {
	echo 'True';
} elseif ( false == $true ) {
	echo 'False';
} else {
	echo 'Neither True or False';
}