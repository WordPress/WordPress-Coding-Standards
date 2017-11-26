<?php
/**
 * Return types should not be void.
 *
 * @return void
 */
function no_return_void() {
	echo "test";
}

/**
 * Return types should not be void.
 *
 * @return void|string
 */
function no_return_void_string() {
	echo "test";
}

/**
 * Return types should not be void.
 *
 * @return string|void
 */
function no_return_string_void() {
	echo "test";
}

/**
 * Return types should not be void.
 *
 * @return array|string|void
 */
function no_return_array_string_void() {
	echo "test";
}

/**
 * Return types should not be void.
 *
 * @return string
 */
function return_string() {
	if ( foo() ) {
		return 'bar';
	}

	echo "test";
}
