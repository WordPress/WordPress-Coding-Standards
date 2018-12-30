<?php
/**
 * Simple file which should not have any issues when testing against the WPCS rulesets.
 *
 * Most - if not all - sniffs should be triggered by this file.
 *
 * Used to do a simple CI test on the rulesets.
 *
 * Currently covered - based on the rulesets as of July 24 2018:
 * - Every WPCS native sniff is triggered.
 * - Every WPCS + PHPCS sniff within the Core ruleset is triggered.
 *
 * @package WPCS\WordPressCodingStandards
 */

echo 'Hello world';

$a = function_call();

/**
 * Class docblock.
 */
class Ruleset_Test {
	/**
	 * Constant docblock.
	 *
	 * @var bool
	 */
	const A = false;

	/**
	 * Property docblock.
	 *
	 * @var int
	 */
	public $a = 123;

	/**
	 * Property docblock.
	 *
	 * @var array
	 */
	protected $array_prop = array(
		'a' => 1,
		'b' => 2,
	);

	/**
	 * Function docblock.
	 *
	 * @param int       $param_a Testing.
	 * @param bool|null $param_b Testing.
	 *
	 * @return void
	 */
	public function testing( $param_a, ?bool $param_b = false ): void {
		?>
		<a href="<?php echo esc_html( $param_a ); ?>"><?php echo (int) $param_b; ?></a>
		<?php

		$a = ( $cond ) ? true : false;

		$b = function ( $a ) use ( $param_a ) {
			if ( preg_match( '`' . preg_quote( $param_a, '`' ) . '`i', $param_b ) === 1 ) {
				echo esc_html( "doublequoted $string" );
			} elseif ( $this->a ) {
				$ab = $a % $b + $c / $d && $param_a || $param_b >> $bitshift;
			}
		};

		$c = $this->array_prop['a'];
		$d = new self();
		$e = apply_filter( 'filter_name', $d, $c );

		if ( $a == $b ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
			$f = isset( $_GET['nonce'] ) ? 1 : 2; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		// phpcs:ignore Squiz.PHP.Eval,WordPress.PHP.NoSilencedErrors
		$g = @eval( 'return true;' );

		switch ( $param_a ) {
			case 1:
				echo esc_html( self::A );
				break;
			case 2:
				include_once 'some-file.php';
				continue;
		}
	}

	/**
	 * Function docblock.
	 *
	 * @return void
	 */
	public function test_goto() {
		$i = 0;
		$j = 50;
		for ( ; $i < 100; $i++ ) {
			while ( $j-- ) {
				if ( 17 === $j ) {
					// phpcs:ignore Generic.PHP.DiscourageGoto.Found
					goto end;
				}
			}
		}

		// phpcs:ignore Generic.PHP.DiscourageGoto.Found
		end:
		echo 'This is a goto - it needs to be here to prevent parse errors';
	}

}
