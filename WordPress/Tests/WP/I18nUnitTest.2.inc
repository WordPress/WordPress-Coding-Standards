<?php
/*
 * Test sniffing for translator comments.
 */
// phpcs:set WordPress.WP.I18n text_domain[] my-slug

/* Basic test ****************************************************************/
__( 'No placeholders here.', 'my-slug' ); // Ok, no placeholders, so no translators comment needed.
__( 'There are %1$d monkeys in the %2$s', 'my-slug' ); // Bad - no translators comment.

/* Testing different comment styles ******************************************/

/* translators: %d: number of cats. */
_n_noop( 'I have %d cat.', "I have %d cats.", 'my-slug' ); // OK - single line /* */ style.

		/* translators: %d: number of cats. */
		_n_noop( 'I have %d cat.', "I have %d cats.", 'my-slug' ); // OK - single line /* */ style, indented code.

// translators: %d: number of cats.
_n_noop( 'I have %d cat.', "I have %d cats.", 'my-slug' ); // OK - single line // style.

/* translators:
   - number of monkeys,
   - location. */
esc_html__( 'There are %1$d monkeys in the %2$s', 'my-slug' ); // OK - multi-line /* */ style.

/*
 * translators: %d: number of cats.
 */
_n_noop( 'I have %d cat.', "I have %d cats.", 'my-slug' ); // OK - multi-line /* */ style.

/*
 * translators: %d: number of cats.
 * This is a multiline comment,
 * But it also has * at the start
 of some lines ;-)
*/
_n_noop( 'I have %d cat.', "I have %d cats.", 'my-slug' ); // OK - inconsistent multi-line /* */ style.

/**
 * translators: %d: number of cats.
 */
_n_noop( 'I have %d cat.', "I have %d cats.", 'my-slug' ); // Bad - docblock style.


/* Testing comment content ****************************************************/

/* %d: number of cats. */
_n_noop( 'I have %d cat.', "I have %d cats.", 'my-slug' ); // Bad - doesn't start with 'translators: '.

/* this is for translators: %d: number of cats. */
_n_noop( 'I have %d cat.', "I have %d cats.", 'my-slug' ); // Bad - doesn't *start* with 'translators:'

/* Translators: %d: number of cats. */
_n_noop( 'I have %d cat.', "I have %d cats.", 'my-slug' ); // OK - Capitalized translators.

/* TRANSLATORS: %d: number of cats. */
_n_noop( 'I have %d cat.', "I have %d cats.", 'my-slug' ); // OK - All caps translators.


/* Testing comment placement ***************************************************/

/* translators: %d: number of cats. */


_n_noop( 'I have %d cat.', "I have %d cats.", 'my-slug' ); // OK - only whitespace between.

/* Some other comment. */
/* translators: %d: number of cats. */
_n_noop( 'I have %d cat.', "I have %d cats.", 'my-slug' ); // OK.

// translators: 1: number; 2: string.
// Some other comment.
esc_attr_e( 'Text to translate to %1$d languages. Another %2$s placeholder', 'my-slug' ); // Bad - translators comment has to be the first comment before the function call.

printf(
	/* translators: number of monkeys, location. */
	__( 'There are %1$d monkeys in the %2$s', 'my-slug' ),
	(int) $number,
	esc_html( $string )
); // Ok.

/* translators: number of monkeys, location. */
printf(
	__( 'There are %1$d monkeys in the %2$s', 'my-slug' ),
	(int) $number,
	esc_html( $string )
); // Bad - comment not directly before line containing the gettext call.

/* translators: number of monkeys, location. */
printf( __( 'There are %1$d monkeys in the %2$s', 'my-slug' ), intval( $number ), esc_html( $string ) ); // Ok - comment is directly before line containing the gettext call.

/* translators: number of monkeys, location. */
printf( __(
	'There are %1$d monkeys in the %2$s', 'my-slug' ),
	intval( $number ),
	esc_html( $string )
); // Ok - comment is directly before line containing the gettext call.

// Issue 776 - regex issue.
__( 'foo 100% bar', 'my-slug' ); // Ok, not a placeholder.

// Issue #830.
_e(); // Bad.

/* translators: number of monkeys, location. */
// phpcs:ignore Standard.Category.Sniff -- testing that the PHPCS annotations are handled correctly.
printf( __( 'There are %1$d monkeys in the %2$s', 'my-slug' ), intval( $number ), esc_html( $string ) ); // Bad.

// phpcs:set WordPress.WP.I18n text_domain[]
