<?php

/*
 * The below should result in a false return value.
 */

/* testNotAHookFunction */
printf( 'my_action' );

/* testHookNameParamMissing */
apply_filters_ref_array( args: $args );

/*
 * The below should result in the information on the hook name param being returned.
 */

/* testLowercaseName */
do_action( 'my_action' );

/* testMixedCaseName */
aPpLy_FiLtErS( 'my_filter', $value );

/* testNamedParameter */
do_action_deprecated( args: $args, hook_name: 'my_action' );
