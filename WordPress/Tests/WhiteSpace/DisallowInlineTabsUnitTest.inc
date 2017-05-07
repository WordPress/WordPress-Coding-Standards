<?php

/**
 * @param int    $var    Description.
 * @param string $string Another description.
 */

			$expected = ( $column - 1 );
			$found    = ( $this->tokens[ $closer ]['column'] - 1 );
			$error    = 'Array closer not aligned correctly; expected %s space(s) but found %s';
			$data     = array(
				$expected_value => 'data',
				$found          => 'more_data',
			);

/**
 * @param int	 $var	 Description - Bad: alignment using tabs.
 * @param string $string Another description.
 */

			$expected = ( $column - 1 );
			$found	  = ( $this->tokens[ $closer ]['column'] - 1 ); // Bad.
			$error	  = 'Array closer not aligned correctly; expected %s space(s) but found %s'; // Bad.
			$data	  = array( // Bad.
				$expected_value => 'data',
				$found			=> 'more_data', // Bad.
			);
