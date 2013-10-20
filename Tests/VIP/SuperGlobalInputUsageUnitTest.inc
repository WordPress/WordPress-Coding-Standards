<?php

foo( $_GET['bar'] );

foo( $_GET['whitelisted'] ); // input var okay

if ( $_GET['test'] && foo() && $bar ) { // input var okay
	taz();
}