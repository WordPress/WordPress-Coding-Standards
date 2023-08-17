<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\WP;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\Arrays;
use PHPCSUtils\Utils\FunctionDeclarations;
use PHPCSUtils\Utils\Numbers;
use PHPCSUtils\Utils\PassedParameters;
use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\Helpers\ContextHelper;
use WordPressCS\WordPress\Sniff;

/**
 * Flag cron schedules less than 15 minutes.
 *
 * @link https://vip.wordpress.com/documentation/vip-go/code-review-blockers-warnings-notices/#cron-schedules-less-than-15-minutes-or-expensive-events
 *
 * @since 0.3.0
 * @since 0.11.0 - Extends the WordPressCS native `Sniff` class.
 *               - Now deals correctly with WP time constants.
 * @since 0.13.0 Class name changed: this class is now namespaced.
 * @since 0.14.0 The minimum cron interval tested against is now configurable.
 * @since 1.0.0  This sniff has been moved from the `VIP` category to the `WP` category.
 */
final class CronIntervalSniff extends Sniff {

	/**
	 * Minimum allowed cron interval in seconds.
	 *
	 * Defaults to 900 (= 15 minutes), which is the requirement for the VIP platform.
	 *
	 * @since 0.14.0
	 *
	 * @var int
	 */
	public $min_interval = 900;

	/**
	 * Known WP Time constant names and their value.
	 *
	 * @since 0.11.0
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
	 * Function within which the hook should be found.
	 *
	 * @var array
	 */
	protected $valid_functions = array(
		'add_filter' => true,
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return Tokens::$stringTokens;
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void
	 */
	public function process_token( $stackPtr ) {
		$token = $this->tokens[ $stackPtr ];

		if ( 'cron_schedules' !== TextStrings::stripQuotes( $token['content'] ) ) {
			return;
		}

		// Check if the text was found within a function call to add_filter().
		$functionPtr = ContextHelper::is_in_function_call( $this->phpcsFile, $stackPtr, $this->valid_functions );
		if ( false === $functionPtr ) {
			return;
		}

		$callback = PassedParameters::getParameter( $this->phpcsFile, $functionPtr, 2, 'callback' );
		if ( false === $callback ) {
			return;
		}

		if ( $stackPtr >= $callback['start'] && $stackPtr <= $callback['end'] ) {
			// "cron_schedules" found in the second parameter, not the first.
			return;
		}

		// Detect callback function name.
		$callbackArrayPtr = $this->phpcsFile->findNext( Tokens::$emptyTokens, $callback['start'], ( $callback['end'] + 1 ), true );

		// If callback is array, get second element.
		if ( false !== $callbackArrayPtr
			&& ( \T_ARRAY === $this->tokens[ $callbackArrayPtr ]['code']
				|| ( isset( Collections::shortArrayListOpenTokensBC()[ $this->tokens[ $callbackArrayPtr ]['code'] ] )
					&& Arrays::isShortArray( $this->phpcsFile, $callbackArrayPtr ) === true )
				)
		) {
			$callback = PassedParameters::getParameter( $this->phpcsFile, $callbackArrayPtr, 2 );

			if ( false === $callback ) {
				$this->confused( $stackPtr );
				return;
			}
		}

		unset( $functionPtr );

		// Search for the function in tokens.
		$search                = Tokens::$stringTokens;
		$search[ \T_CLOSURE ]  = \T_CLOSURE;
		$search[ \T_FN ]       = \T_FN;
		$search[ \T_ELLIPSIS ] = \T_ELLIPSIS;
		$callbackFunctionPtr   = $this->phpcsFile->findNext( $search, $callback['start'], ( $callback['end'] + 1 ) );

		if ( false === $callbackFunctionPtr ) {
			$this->confused( $stackPtr );
			return;
		}

		if ( \T_CLOSURE === $this->tokens[ $callbackFunctionPtr ]['code']
			|| \T_FN === $this->tokens[ $callbackFunctionPtr ]['code']
		) {
			$functionPtr = $callbackFunctionPtr;
		} elseif ( \T_ELLIPSIS === $this->tokens[ $callbackFunctionPtr ]['code'] ) {
			// Check if this is a PHP 8.1 first class callable.
			$before = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, ( $callbackFunctionPtr - 1 ), null, true );
			$after  = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $callbackFunctionPtr + 1 ), null, true );
			if ( ( false !== $before && \T_OPEN_PARENTHESIS === $this->tokens[ $before ]['code'] )
				&& ( false !== $after && \T_CLOSE_PARENTHESIS === $this->tokens[ $after ]['code'] )
			) {
				// Ok, now see if we can find the function name.
				$beforeOpen = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, ( $before - 1 ), null, true );
				if ( false !== $beforeOpen && \T_STRING === $this->tokens[ $beforeOpen ]['code'] ) {
					$found_function = $this->find_function_by_name( $this->tokens[ $beforeOpen ]['content'] );
					if ( false !== $found_function ) {
						$functionPtr = $found_function;
					}
				}
			}
			unset( $before, $after, $beforeOpen );
		} else {
			$functionName   = TextStrings::stripQuotes( $this->tokens[ $callbackFunctionPtr ]['content'] );
			$found_function = $this->find_function_by_name( $functionName );
			if ( false !== $found_function ) {
				$functionPtr = $found_function;
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

			if ( isset( Tokens::$stringTokens[ $this->tokens[ $i ]['code'] ] ) === true ) {
				if ( 'interval' === TextStrings::stripQuotes( $this->tokens[ $i ]['content'] ) ) {
					$operator = $this->phpcsFile->findNext( \T_DOUBLE_ARROW, $i, null, false, null, true );
					if ( false === $operator ) {
						$this->confused( $stackPtr );
						return;
					}

					$valueStart        = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $operator + 1 ), null, true, null, true );
					$valueEnd          = $this->phpcsFile->findNext( array( \T_COMMA, \T_CLOSE_PARENTHESIS ), ( $valueStart + 1 ) );
					$value             = '';
					$parentheses_count = 0;
					for ( $j = $valueStart; $j <= $valueEnd; $j++ ) {
						if ( isset( Tokens::$emptyTokens[ $this->tokens[ $j ]['code'] ] ) ) {
							continue;
						}

						if ( \T_NS_SEPARATOR === $this->tokens[ $j ]['code'] ) {
							$value .= ' ';
							continue;
						}

						if ( $j === $valueEnd && \T_COMMA === $this->tokens[ $j ]['code'] ) {
							break;
						}

						// Make sure that PHP 7.4 numeric literals and PHP 8.1 explicit octals don't cause problems.
						if ( \T_LNUMBER === $this->tokens[ $j ]['code']
							|| \T_DNUMBER === $this->tokens[ $j ]['code']
						) {
							$number_info = Numbers::getCompleteNumber( $this->phpcsFile, $j );
							$value      .= $number_info['decimal'];
							$j           = $number_info['last_token'];
							continue;
						}

						if ( \T_OPEN_PARENTHESIS === $this->tokens[ $j ]['code'] ) {
							$value .= $this->tokens[ $j ]['content'];
							++$parentheses_count;
							continue;
						}

						if ( \T_CLOSE_PARENTHESIS === $this->tokens[ $j ]['code'] ) {
							// Only add a close parenthesis if there are open parentheses.
							if ( $parentheses_count > 0 ) {
								$value .= $this->tokens[ $j ]['content'];
								--$parentheses_count;
							}
							continue;
						}

						$value .= $this->tokens[ $j ]['content'];
					}

					if ( $parentheses_count > 0 ) {
						// Make sure all open parenthesis are closed.
						$value .= str_repeat( ')', $parentheses_count );
					}

					if ( is_numeric( $value ) ) {
						$interval = $value;
						break;
					}

					// Deal correctly with WP time constants.
					$value = str_replace( array_keys( $this->wp_time_constants ), array_values( $this->wp_time_constants ), $value );

					// If all parentheses, digits and operators, eval!
					if ( preg_match( '#^[\s\d()+*/-]+$#', $value ) > 0 ) {
						$interval = eval( "return ( $value );" ); // phpcs:ignore Squiz.PHP.Eval -- No harm here.
						break;
					}

					$this->confused( $stackPtr );
					return;
				}
			}
		}

		$this->min_interval = (int) $this->min_interval;

		if ( isset( $interval ) && $interval < $this->min_interval ) {
			$minutes = round( ( $this->min_interval / 60 ), 1 );
			$this->phpcsFile->addWarning(
				'Scheduling crons at %s sec ( less than %s minutes ) is discouraged.',
				$stackPtr,
				'CronSchedulesInterval',
				array(
					$interval,
					$minutes,
				)
			);
			return;
		}
	}

	/**
	 * Find a declared function in a file based on the function name.
	 *
	 * @param string $functionName The name of the function to find.
	 *
	 * @return int|false Integer stack pointer to the function keyword token or
	 *                   false if not found.
	 */
	private function find_function_by_name( $functionName ) {
		$functionPtr = false;
		for ( $ptr = 0; $ptr < $this->phpcsFile->numTokens; $ptr++ ) {
			if ( \T_FUNCTION === $this->tokens[ $ptr ]['code'] ) {
				$foundName = FunctionDeclarations::getName( $this->phpcsFile, $ptr );
				if ( $foundName === $functionName ) {
					$functionPtr = $ptr;
					break;
				} elseif ( isset( $this->tokens[ $ptr ]['scope_closer'] ) ) {
					// Skip to the end of the function definition.
					$ptr = $this->tokens[ $ptr ]['scope_closer'];
				}
			}
		}

		return $functionPtr;
	}

	/**
	 * Add warning about unclear cron schedule change.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void
	 */
	public function confused( $stackPtr ) {
		$this->phpcsFile->addWarning(
			'Detected changing of cron_schedules, but could not detect the interval value.',
			$stackPtr,
			'ChangeDetected'
		);
	}
}
