<?php

/*
 * Test cases where get_callback_parameter() should return false.
 */

/* testNotArrayWalkingFunction */
array_filter( $array );

/* testCallbackParamMissing */
array_map();

/*
 * Test cases where get_callback_parameter() should return the callback parameter.
 */

/* testArrayMapCallback */
array_map( 'sanitize_text_field', $array );

/* testMapDeepMixedCase */
Map_Deep( $array, 'esc_html' );
