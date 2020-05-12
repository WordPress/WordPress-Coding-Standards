<?php
// phpcs:set WordPress.Commenting.HooksInlineDocs allowed_extra_versions[] MU

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

// Has a since tag with an wrong value.
/**
 * This is a description.
 *
 * @since Test
 */
apply_filters( 'hook', 'function' );

/*
 * Set 4
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

// Complete docblock with a "exceptional" version number declared in the XML.
/**
 * This is a description.
 *
 * @since MU
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

// Complete docblock with a "exceptional" version number declared in the XML with extra text.
/**
 * This is a description.
 *
 * @since MU Additional text.
 */
apply_filters( 'hook', 'function' );

// "Already documented" example of an action.
/** This action is documented in file.php */
do_action( 'hook ' );

// "Already documented" example of a filter.
/** This filter is documented in file.php */
apply_filters(' hook', 'function' );

// phpcs:set WordPress.Commenting.HooksLineDocs allowed_extra_versions[]
