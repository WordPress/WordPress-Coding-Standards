<?php
/**
 * Flag returning high or infinite posts_per_page
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Shady Sharaf <shady@x-team.com>
 */
class WordPress_Sniffs_VIP_PostsPerPageSniff extends WordPress_Sniffs_Arrays_ArrayAssignmentRestrictionsSniff
{

	/**
	 * Groups of variables to restrict
	 * This should be overridden in extending classes.
	 *
	 * Example: groups => array(
	 * 	'wpdb' => array(
	 * 		'type' => 'error' | 'warning',
	 * 		'message' => 'Dont use this one please!',
	 * 		'variables' => array( '$val', '$var' ),
	 * 		'object_vars' => array( '$foo->bar', .. ),
	 * 		'array_members' => array( '$foo['bar']', .. ),
	 * 	)
	 * )
	 *
	 * @return array
	 */
	public function getGroups() {
		return array(
			'posts_per_page' => array(
				'type' => 'error',
				'keys' => array(
					'posts_per_page',
					'nopaging',
					'numberposts',
					),
				)
			);
	}

	/**
	 * Callback to process each confirmed key, to check value
	 * This must be extended to add the logic to check assignment value
	 * 
	 * @param  string   $key   Array index / key
	 * @param  mixed    $val   Assigned value
	 * @param  int      $line  Token line
	 * @param  array    $group Group definition
	 * @return mixed           FALSE if no match, TRUE if matches, STRING if matches with custom error message passed to ->process()
	 */
	public function callback( $key, $val, $line, $group ) {
		$key = strtolower( $key );
		if (
			( $key == 'nopaging' && ( $val == 'true' || $val == 1 ) )
			||
			( in_array( $key, array( 'numberposts', 'posts_per_page' ) ) && $val == '-1' )
			) {
			return 'Disabling pagination is prohibited in VIP context, do not set `%s` to `%s` ever.';
		}
		elseif (
			in_array( $key, array( 'posts_per_page', 'numberposts' ) )
			) {

			if ( $val > 50 ) {
				return 'Detected high pagination limit, `%s` is set to `%s`';
			}
		}
	}


}//end class
