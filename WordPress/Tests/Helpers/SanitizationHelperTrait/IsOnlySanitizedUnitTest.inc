<?php

/*
 * Test cases that should not be considered only sanitized.
 */

/* testNotSanitizedEcho */
echo $_POST['foo'];

/* testInnerFunctionNotSanitizing */
sanitize_text_field( strtolower( $_GET['name'] ) );

/* testOnlyUnslashed */
wp_unslash( $_REQUEST['content'] );

/* testCastNestedInFunction */
number_format( (float) $_POST['price'] );

/* testSanitizedNestedInFunction */
trim( sanitize_text_field( $_POST['name'] ) );

/*
 * Test cases that should be considered only sanitized.
 */

/* testSingleSanitizingFunction */
sanitize_text_field( $_POST['name'] );

/* testUnslashingSanitizingFunction */
absint( $_GET['id'] );

/* testArrayWalkingSanitizingCallback */
array_map( 'sanitize_text_field', $_REQUEST['items'] );

/* testInUnset */
unset( $_FILES['temp'] );

/* testSafeCast */
(int) $_POST['count'];

sanitize_text_field( /* testStringTokenSanitized */ get_input() ); // T_STRING entry token: not reached via the sniffs that call the method (they only ever pass a T_VARIABLE), but the method supports any token type.
