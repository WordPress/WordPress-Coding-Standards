<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\VIP;

use WordPress\Sniff;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Flag cron schedules less than 15 minutes.
 *
 * @link    https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#cron-schedules-less-than-15-minutes-or-expensive-events
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.11.0 - Extends the WordPress_Sniff class.
 *                 - Now deals correctly with WP time constants.
 * @since   0.13.0 Class name changed: this class is now namespaced.
 */
class CronIntervalSniff extends Sniff {

	/**
	 * Known WP Time constant names and their value.
	 *
	 * @var array
	 */
	protected $wp_time_constants = array(
		'MINUTE_IN_SECONDS' => 60,
		'HOUR_IN_SECONDS'   => 3600,
		'DAY_IN_SECONDS'    => 86400,
		'WEEK_IN_SECONDS'   => 604800,
		'MONTH_IN_SECONDS'  => 2592000,
		'YEAR_IN_SECONDS'   => 31536000,
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_CONSTANT_ENCAPSED_STRING,
			T_DOUBLE_QUOTED_STRING,
		);

	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void
	 */
	public function process_token( $stackPtr ) {
		$token  = $this->tokens[ $stackPtr ];

		if ( 'cron_schedules' !== $this->strip_quotes( $token['content'] ) ) {
			return;
		}

		// If within add_filter.
		$functionPtr = $this->phpcsFile->findPrevious( T_STRING, key( $token['nested_parenthesis'] ) );
		if ( false === $functionPtr || 'add_filter' !== $this->tokens[ $functionPtr ]['content'] ) {
			return;
		}

		$callback = $this->get_function_call_parameter( $functionPtr, 2 );
		if ( false === $callback ) {
			return;
		}

		// Detect callback function name.
		$callbackArrayPtr = $this->phpcsFile->findNext( Tokens::$emptyTokens, $callback['start'], ( $callback['end'] + 1 ), true );

		// If callback is array, get second element.
		if ( false !== $callbackArrayPtr
			&& ( T_ARRAY === $this->tokens[ $callbackArrayPtr ]['code']
				|| T_OPEN_SHORT_ARRAY === $this->tokens[ $callbackArrayPtr ]['code'] )
		) {
			$callback = $this->get_function_call_parameter( $callbackArrayPtr, 2 );

			if ( false === $callback ) {
				$this->confused( $stackPtr );
				return;
			}
		}

		unset( $functionPtr );

		// Search for the function in tokens.
		$callbackFunctionPtr = $this->phpcsFile->findNext( array( T_CONSTANT_ENCAPSED_STRING, T_DOUBLE_QUOTED_STRING, T_CLOSURE ), $callback['start'], ( $callback['end'] + 1 ) );

		if ( false === $callbackFunctionPtr ) {
			$this->confused( $stackPtr );
			return;
		}

		if ( T_CLOSURE === $this->tokens[ $callbackFunctionPtr ]['code'] ) {
			$functionPtr = $callbackFunctionPtr;
		} else {
			$functionName = $this->strip_quotes( $this->tokens[ $callbackFunctionPtr ]['content'] );

			for ( $ptr = 0; $ptr < $this->phpcsFile->numTokens; $ptr++ ) {
				if ( T_FUNCTION === $this->tokens[ $ptr ]['code'] ) {
					$foundName = $this->phpcsFile->getDeclarationName( $ptr );
					if ( $foundName === $functionName ) {
						$functionPtr = $ptr;
						break;
					} elseif ( isset( $this->tokens[ $ptr ]['scope_closer'] ) ) {
						// Skip to the end of the function definition.
						$ptr = $this->tokens[ $ptr ]['scope_closer'];
					}
				}
			}
		}

		if ( ! isset( $functionPtr ) ) {
			$this->confused( $stackPtr );
			return;
		}

		if ( ! isset( $this->tokens[ $functionPtr ]['scope_opener'], $this->tokens[ $functionPtr ]['scope_closer'] ) ) {
			return;
		}

		$opening = $this->tokens[ $functionPtr ]['scope_opener'];
		$closing = $this->tokens[ $functionPtr ]['scope_closer'];
		for ( $i = $opening; $i <= $closing; $i++ ) {

			if ( in_array( $this->tokens[ $i ]['code'], array( T_CONSTANT_ENCAPSED_STRING, T_DOUBLE_QUOTED_STRING ), true ) ) {
				if ( 'interval' === $this->strip_quotes( $this->tokens[ $i ]['content'] ) ) {
					$operator = $this->phpcsFile->findNext( T_DOUBLE_ARROW, $i, null, false, null, true );
					if ( false === $operator ) {
						$this->confused( $stackPtr );
						return;
					}

					$valueStart = $this->phpcsFile->findNext( T_WHITESPACE, ( $operator + 1 ), null, true, null, true );
					$valueEnd   = $this->phpcsFile->findNext( array( T_COMMA, T_CLOSE_PARENTHESIS ), ( $valueStart + 1 ) );
					$value      = $this->phpcsFile->getTokensAsString( $valueStart, ( $valueEnd - $valueStart ) );

					if ( is_numeric( $value ) ) {
						$interval = $value;
						break;
					}

					// Deal correctly with WP time constants.
					$value = str_replace( array_keys( $this->wp_time_constants ), array_values( $this->wp_time_constants ), $value );

					// If all digits and operators, eval!
					if ( preg_match( '#^[\s\d+*/-]+$#', $value ) > 0 ) {
						$interval = eval( "return ( $value );" ); // @codingStandardsIgnoreLine - No harm here.
						break;
					}

					$this->confused( $stackPtr );
					return;
				}
			}
		}

		if ( isset( $interval ) && $interval < 900 ) {
			$this->phpcsFile->addError( 'Scheduling crons at %s sec ( less than 15 min ) is prohibited.', $stackPtr, 'CronSchedulesInterval', array( $interval ) );
			return;
		}

	} // End process_token().

	/**
	 * Add warning about unclear cron schedule change.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 */
	public function confused( $stackPtr ) {
		$this->phpcsFile->addWarning(
			'Detected changing of cron_schedules, but could not detect the interval value.',
			$stackPtr,
			'ChangeDetected'
		);
	}

} // End class.
