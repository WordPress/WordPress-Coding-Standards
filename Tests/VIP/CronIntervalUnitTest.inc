<?php

function my_add_weekly( $schedules ) {
	$schedules['weekly'] = array(
		'interval' => 360,
		'display' => __( 'Once Weekly' )
	);
	return $schedules;
}
add_filter( 'cron_schedules', 'my_add_weekly');


function my_add_quickly( $schedules ) {
	$schedules['weekly'] = array(
		'interval' => 10 * 60 * 60,
		'display' => __( 'Once every Ten Minutes' )
	);
	return $schedules;
}
add_filter( 'cron_schedules', array( $this, 'my_add_quickly' ) );

add_filter( 'cron_schedules', array( $some_other_place, 'some_other_method' ) );
