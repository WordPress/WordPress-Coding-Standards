<?php
/**
 * Test file for the hooks DocBlocks.
 */

/*
 * Set 1
 *
 * Make sure we check each function. These should all fail as there are no inline docs.
 */

apply_filters( 'hook', 'function' );
apply_filters_ref_array( 'hook', 'function' );
apply_filters_deprecated( 'hook', 'function' );
do_action( 'hook', 'function' );
do_action_ref_array( 'hook', 'function' );
do_action_deprecated( 'hook', 'function' );

/*
 * Set 2
 *
 * Invalid comment styles. These should fail.
 */

// Wrong format.
apply_filters( 'hook', 'function' );

/*
 * Docblocks need the /** format
 */
apply_filters( 'hook', 'function' );

/*
 * Set 3
 *
 * Docblocks missing required values. These should fail.
 */

// Has a since tag with no value.
/**
 * This is a description.
 *
 * @since
 */
apply_filters( 'hook', 'function' );

// Has a since tag with an incomplete value.
/**
 * This is a description.
 *
 * @since 8.5
 */
apply_filters( 'hook', 'function' );

// Has a since tag with a wrong value.
/**
 * This is a description.
 *
 * @since Test
 */
apply_filters( 'hook', 'function' );

/*
 * Set 6
 *
 * "Real World Cases"
 */

// This should fail since the hook does not have a DocBlock.
/**
 * This is my function DocBlock.
 *
 * @since 0000.00000.0000000000
 */
function my_function_has_a_hook() {
	do_action( 'foo' );
}

// This should fail since the hook does not have a DocBlock.
/**
 * This is my function DocBlock.
 *
 * @since 0000.00000.0000000000
 */
function my_function_has_a_hook() {
	echo 'foo';
}
do_action( 'foo' );

// This should fail since the hook does not have a DocBlock.
/**
 * This is my function DocBlock.
 *
 * @since 0000.00000.0000000000
 */
function my_function_has_a_hook() { do_action( 'foo' ); }

/*
 * Set 5
 *
 * Valid comments. These should pass.
 */

// Complete docblock with a "normal" version number.
/**
 * This is a description.
 *
 * @since 1.0.0
 */
apply_filters( 'hook', 'function' );

// Complete docblock with a "normal" version number and extra text.
/**
 * This is a description.
 *
 * @since 1.0.0
 * @since 1.2.0 Added something.
 */
apply_filters( 'hook', 'function' );

// phpcs:set WordPress.Commenting.HooksInlineDocs allowed_extra_versions[] MU
// Complete docblock with a "exceptional" version number declared in the XML or via phpcs:set.
/**
 * This is a description.
 *
 * @since MU
 */
apply_filters( 'hook', 'function' );

// Complete docblock with a "exceptional" version number declared in the XML with extra text.
/**
 * This is a description.
 *
 * @since MU Additional text.
 */
apply_filters( 'hook', 'function' );

// phpcs:set WordPress.Commenting.HooksLineDocs allowed_extra_versions[]

// "Already documented" example of an action.
/** This action is documented in file.php */
do_action( 'hook ' );

// "Already documented" example of a filter.
/** This filter is documented in file.php */
apply_filters(' hook', 'function' );
