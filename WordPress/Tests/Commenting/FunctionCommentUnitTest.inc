<?php
/**
 * Function description.
 *
 * @return void
 */
function with_incorrect_return_void() {
	echo 'string';
}

/**
 * Function description.
 *
 * @return void With a comment.
 */
function with_incorrect_return_void_with_comment() {
	echo 'string';
}

/**
 * Function description.
 */
function with_correct_omitted_return() {
	echo 'string';
}

/**
 * Function description.
 *
 * @return string|void
 */
function with_correct_return_void() {
	if ( foo() ) {
		return 'bar';
	}

	echo 'baz';
}

/**
 * Function description.
 *
 * @return string|void With a comment.
 */
function with_correct_return_void_with_comment() {
	if ( foo() ) {
		return 'bar';
	}

	echo 'baz';
}
