<?php
// Using auto-escaped functions in Loop.
while ( have_posts() ) {
	the_tags(); // Ok.
	the_category(); // Ok.

	// Ok.
	?>
	<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
	<?php

	the_content(); // Ok.
	the_date(); // Ok.
}
?>

<h2><?php echo $title; // Bad. ?></h2>
<h2><?php echo esc_HTML( $title ); // OK. ?></h2>
<h2><?php echo apply_filters( 'the_title', $title ); // Bad, no escaping function. ?></h2>

<?php
// Issue:#53.
function custom_column_display( $column, $post_id )
{
    global $post;
    switch ( $column ) {
        case 'some_number' :
            echo (int) $test;
            echo (int) get_post_meta( $post_id, SOME_NUMBER, true );
        break;
    }
}


$foo = 'abc';
echo $foo; // Bad, should have escaping function.
echo 'Some Raw String';  // Good.

echo '' . $bad; // Bad, should not validate.
echo "this is $bad"; // Bad.
echo esc_html( $good . $better ) . $foo; // Bad, should escape all concatenated elements.
echo esc_html( $food . 'include'  ); // Good, eveything inside the escaping/sanitizing function should pass.
echo esc_html( strtoupper( $ok ) ) . $foo; // Bad, again.
echo esc_html( strtoupper( $ok ) ) . ' ' . esc_html( strtolower( $ok ) ); // Ok.

_e( $some_nasty_var ); // Bad.

echo filter_var( $bar, FILTER_VALIDATE_EMAIL );
echo filter_input( INPUT_GET, $bar, FILTER_SANITIZE_SPECIAL_CHARS );

echo '<input type="checkbox" name="' . esc_attr( 'field[' . $id . ']' ) . '" value="on" ' . checked( $current, 'on', false ) . '> ';

echo ent2ncr( $text ); // Bad.

echo number_format( 1024 );

echo ent2ncr( esc_html( $_data ) );

echo $foo ? $foo : 'no foo'; // Bad.
echo empty( $foo ) ? 'no foo' : $foo; // Bad.
echo $foo ? esc_html( $foo ) : 'no foo'; // Ok.

echo 4; // Ok.

exit( $foo ); // Bad.
exit( esc_html( $foo ) ); // Ok.

die( $foo ); // Bad.
die( esc_html( $foo ) ); // Ok.

printf( 'Hello %s', $foo ); // Bad.
printf( 'Hello %s', esc_html( $foo ) ); // Ok.
printf( 'Hello %s! Hi %s!', esc_html( $foo ), $bar ); // Bad.

vprintf( 'Hello %s', array( $foo ) ); // Bad.
vprintf( 'Hello %s', array( esc_html( $foo ) ) ); // Ok.

// The below checks that functions which are marked as needed further sanitization
// don't spill over into later arguments when nested in a function call. There was
// a bug which would cause line 84 to be marked as needing sanitization because _x()
// is marked as needing sanitization.
do_something(
	_x( 'Some string', 'context', 'domain' )
	, array( $foo ) // Ok.
);

// There was a bug where an empty exit followed by other code would give an error.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Ok.
} else {
	other();
}

printf(
	/* translators: this comment is just for you. */
	esc_html__( 'Hello %s.', 'domain' )
	, 'world'
	// There were other long arguments down here "in real life", which is why this was multi-line.
);

wp_die( $message ); // Bad.
wp_die( esc_html( $message ) ); // Ok.
wp_die( esc_html( $message ), $title ); // Bad.
wp_die( esc_html( $message ), esc_html( $title ) ); // Ok.
wp_die( esc_html( $message ), '', array( 'back_link' => true ) ); // Ok.
wp_die( esc_html( $message ), '', array( 'back_link' => false ) ); // Ok.
wp_die( esc_html( $message ), '', array( 'response' => 200 ) ); // Ok.

echo '<h2>', esc_html( $foo ), '</h2>'; // Ok.
echo 'a', 'b'; // Ok.
echo 'Hello, ', $foo; // Bad.
echo esc_html( $foo ), $bar; // Bad.
echo (int) $foo, $bar; // Bad.
echo (int) get_post_meta( $post_id, SOME_NUMBER, true ), do_something( $else ); // Bad.

wp_die( -1 ); // Ok.

?>
<p class="notice"><?php echo esc_html( $message ) ?></p> <!-- OK. -->
<input type="submit" name="sync-progress" class="button button-primary button-large" value="<?php esc_attr_e( 'Start Sync', 'foo' ); ?>" /><!-- OK. -->
<input type="hidden" name="sync-action" class="sync-action" value="<?php echo esc_attr( $continue_sync ? 'sync_progress' : '' ); ?>" /><!-- OK. -->
<?php

// Bad - ignored via old-style ignore comment.
echo $html_fragment; // Bad.
echo $html_fragment; // xss OK.
echo $html_fragment; // WPCS: XSS whitelist.
?><?php echo $html_fragment; // XSS pass. ?><?php

_deprecated_function( __FUNCTION__, '1.3.0', 'another_func' ); // Ok.
_Deprecated_Function( __FUNCTION__, '1.3.0', $another_func ); // Bad.
_deprecated_function( __FUNCTION__, '1.3.0', esc_html( $another_func ) ); // Ok.
_deprecated_file( __FILE__, '1.3.0' ); // Ok.
_deprecated_argument( __METHOD__, '1.3.0', 'The $arg is deprecated.' ); // Ok.
_doing_it_wrong( __METHOD__, "Invalid value for the 'bob' argument {$args['bob']}." ); // Bad.
_doing_it_wrong( __METHOD__, "Invalid value for the 'bob' argument " . esc_html( $args['bob'] ) . "." ); // Ok.

trigger_error( "There was an error: {$message}", E_USER_NOTICE ); // Bad.
trigger_error( "There was an error: " . esc_html( $message ), E_USER_NOTICE ); // Ok.

echo '<p>' . sprintf( esc_html__( 'Some text -> %sLink text%s', 'textdomain' ), '<a href="' . Esc_Url( add_query_arg( array( 'page' => 'my_page' ), admin_url( 'admin.php' ) ) ) . '">', '</a>' ). '</p>'; // Ok.

echo '<br/><strong>' . sprintf( esc_html__( 'Found %d results', 'textdomain' ), (int) $result_count ) . '</strong><br/><br/>'; // Ok.

echo sprintf( 'Hello %s', $foo ); // Bad.
echo sprintf( 'Hello %s', esc_html( $foo ) ); // Ok.
echo sprintf( 'Hello %s! Hi %s!', esc_html( $foo ), $bar ); // Bad.

echo vsprintf( 'Hello %s', array( $foo ) ); // Bad.
echo vsprintf( 'Hello %s', array( esc_html( $foo ) ) ); // Ok.

echo sprintf( __( 'Welcome to Genesis %s', 'genesis' ), PARENT_THEME_BRANCH ); // Bad x 2.
echo sprintf( esc_html__( 'Welcome to Genesis %s', 'genesis' ), esc_html( PARENT_THEME_BRANCH ) ); // Ok.

echo esc_html( strval( $_var ) ? $_var : gettype( $_var ) ); // Ok.
echo ( $is_hidden ) ? ' style="display:none;"' : ''; // Ok.
echo sprintf( 'Howdy, %s', esc_html( $name ? $name : __( 'Partner' ) ) ); // Ok.

_e( 'Something' ); // Bad.
esc_html_e( 'Something' ); // Ok.

echo $something // Bad.
     . esc_attr( 'baz-' // Rest is OK.
	         . $r
	         . ( $r === $active_round ? ' foo' : '' )
	         . ( $r < $active_round ? ' bar' : '' )
	) . 'something';

echo implode( '<br>', $items ); // Bad.
echo implode( '<br>', urlencode_deep( $items ) ); // Ok.
echo implode( '<br>', array_map( 'esc_html', $items ) ); // Ok.
echo implode( '<br>', array_map( 'foo', $items ) ); // Bad.
echo join( '<br>', $items ); // Bad.
echo join( '<br>', Array_Map( 'esc_html', $items ) ); // Ok.

echo '<option name="' . esc_attr( $name ) . '"' .
     ( $name === $selected ? ' selected' : '' ) .
     '>' . esc_html( $value )
     . '</option>';

_deprecated_hook( 'some_filter', '1.3.0', esc_html__( 'The $arg is deprecated.' ), 'some_other_filter' ); // Ok.
_deprecated_hook( "filter_{$context}", '1.3.0', __( 'The $arg is deprecated.' ), sprintf( __( 'Some parsed message %s', $variable ) ) ); // Bad.



/*
 * Test using custom properties, setting & unsetting (resetting).
 */
// phpcs:set WordPress.Security.EscapeOutput customPrintingFunctions[] to_screen,my_print
to_screen( $var1, esc_attr( $var2 ) ); // Bad x 1.
my_print( $var1, $var2 ); // Bad x 2.

// phpcs:set WordPress.Security.EscapeOutput customEscapingFunctions[] esc_form_field
// phpcs:set WordPress.Security.EscapeOutput customAutoEscapedFunctions[] post_info,cpt_info

echo esc_form_field( $var ); // Ok.
echo post_info( $post_id, 'field' ); // Ok.
echo cpt_info( $post_type, 'query' ); // Ok.
to_screen( esc_form_field( $var1), esc_attr( $var2 ) ); // Ok.

// phpcs:set WordPress.Security.EscapeOutput customPrintingFunctions[]
// phpcs:set WordPress.Security.EscapeOutput customEscapingFunctions[]
// phpcs:set WordPress.Security.EscapeOutput customAutoEscapedFunctions[]

echo esc_form_field( $var ); // Bad.
echo post_info( $post_id, 'field' ); // Bad.
echo cpt_info( $post_type, 'query' ); // Bad.

echo (unset) $var; // Ok.

// Nowdocs are OK.
echo <<<'EOD'
Some Raw String
EOD;

echo 1.234; // Ok.

echo ( 1.234 + 10 + 2.5 ); // Ok.
echo 10 % 2; // Ok.
echo 8 * 1.2; // Ok.

?>
<?= $var ?><!-- Bad. -->
<?= esc_html( $var ); ?><!-- Ok. -->
<?= $var['foo']; ?><!-- Bad. -->
<?= $var->foo ?><!-- Bad. -->
<?php

// Issue #933. OK.
function do_footer_nav() {
	echo \wp_kses_post(
		\genesis_get_nav_menu(
			[
				'menu_class'     => 'menu genesis-nav-menu menu-footer',
				'theme_location' => 'footer',
			]
		)
	);
}

?><?php echo $html_fragment // XSS pass. ?><?php

echo // WPCS: XSS ok.
	esc_html( $something ),
	$something_else,
	esc_html( $something_more );

echo esc_html( $something ),
	$something_else,
	esc_html( $something_more ); // WPCS: XSS ok.

_ex( 'Something', 'context' ); // Bad.
_ex( $some_nasty_var, 'context' ); // Bad.
echo esc_html_x( 'Something', 'context' ); // Ok.
echo esc_html_x( $some_nasty_var, 'context' ); // Ok.

?>
	<input type="hidden" name="some-action" value="<?php echo esc_attr_x( 'none', 'context' ); ?>" /><!-- OK. -->
<?php

echo PHP_VERSION_ID, PHP_VERSION, PHP_EOL, PHP_EXTRA_VERSION; // OK.

trigger_error( 'DEBUG INFO - ' . __METHOD__ . '::internal_domains: domain = ' . $domain ); // Bad.
Trigger_ERROR( $domain ); // Bad.

vprintf( 'Hello %s', [ $foo ] ); // Bad.
vprintf( 'Hello %s', [ esc_html( $foo ) ] ); // Ok.

function testIt( $obj ) {
	$obj->print = new \Printer();
	$obj->exit->customExit();
	return $obj->print->transform( 'something' );
}

class Silly {
	function echo() {}
	function print() {}
}

echo  // phpcs:ignore WP.Secur1ty.EscapeOutput -- WPCS: XSS ok. (sniff name mangled on purpose).
	esc_html( $something ),
	$something_else,
	esc_html( $something_more );

echo esc_html( $something ),
	$something_else,
	esc_html( $something_more ); // phpcs:ignore WP.Secur1ty.EscapeOutput -- WPCS: XSS ok. (sniff name mangled on purpose).

echo get_the_title(); // Bad.
echo wp_kses_post( get_the_title() ); // Ok.
echo esc_html( get_the_title() ); // Ok.

echo implode( '<br>', map_deep( $items, 'esc_html' ) ); // Ok.
echo implode( '<br>', map_deep( $items, 'foo' ) ); // Bad.

_deprecated_file( basename( __FILE__ ), '1.3.0' ); // Ok.
_deprecated_file( $file, '1.3.0' ); // Error.

trigger_error(); // Ignore.
_deprecated_file(); // Ignore.

\_deprecated_file( \basename( __FILE__ ), '1.3.0' ); // Ok.

// Issue #1246.
echo antispambot( 'john.doe@mysite.com' ); // OK.
echo antiSpambot( esc_html( $email ) ); // OK.
echo antispambot( $email ); // Bad.

/*
 * Safeguard support for PHP 8.0+ named parameters for array walking functions.
 */
echo implode( '<br>', map_deep( callback: 'esc_html', value: $items ) ); // Ok.
echo implode( '<br>', map_deep( value: $items ) ); // Bad, missing callback param, so escaping can not be verified.
echo implode( '<br>', map_deep( call_back: 'esc_html', value: $items ) ); // Bad, wrong param name, so escaping can not be verified.
echo implode( '<br>', map_deep( callback: 'foo', value: $items, ) ); // Bad, non-escaping function as callback.

// Note: named params not supported due to the `...$arrays` in array_map()`, but that's not the concern of this sniff.
echo implode( '<br>', array_map( array: $items, callback: 'esc_html', ) ); // Ok.
echo implode( '<br>', array_map( array: $items, callback: 'foo', ) ); // Bad.

// Operators should be ignored.
print 10 ** 20;
print 10 & 20;
print 10 | 20;
print 10 ^ 20;
print 10 << 20;
print 10 >> 20;
print 10 == 20;
print 10 != 20;
print 10 === 20;
print 10 !== 20;
print 10 < 20;
print 10 > 20;
print 10 <= 20;
print 10 >= 20;
print 10 <=> 20;
print ! 'hello';
print 'hello' && 'world';
print 'hello' || 'world';
print 'hello' and 'world';
print 'hello' or 'world';
print 'hello' xor 'world';
print 10++;
print --10;

// This includes the PHP 7.0+ null coalesce operator.
echo $var ?? 'default'; // Bad.
echo esc_html( $var ?? 'default' ); // OK.

// Make sure the sniff does not get confused over constants/properties using the same name as one of the target functions.
$a = _ex; // OK, constant, not function call.
$a = $obj->wp_dropdown_pages; // OK, property access, not function call.
use function wp_dropdown_pages; // OK, import use statement, not function call.

// Make sure the sniff does not get confused over methods/namespaced functions etc vs global function calls.
$obj->_deprecated_file( $file, '1.3.0' ); // OK.
$obj?->_deprecated_file( $file, '1.3.0' ); // OK.
MyClass::_deprecated_file( $file, '1.3.0' ); // OK.
My\NS\_ex( $some_nasty_var, 'context' ); // OK.
class IgnoreFunctionDeclarations {
	function wp_die( $foo ) {} // OK.
	function &trigger_error( $foo ) {} // OK.
}
$obj = new User_Error( $foo ); // OK.

// Make sure special casing of select functions is handled case-insensitively.
Trigger_ERROR( 'This is fine', $second_param_should_be_ignored ); // OK.
_Deprecated_File( basename( __FILE__ ), '1.3.0' ); // OK.
_EX( 'all_params_should_be_ignored_if_function_is_reported_as_unsafe', 'another_param' ); // Bad x 1 for unsafe function.

// Allow for comments in the $file parameter.
_deprecated_file(
	/* comment */
	basename( __FILE__ ),
	'1.3.0'
); // Ok.

// Exit/die should only be examined when there are parentheses.
$var = (true || exit ) && empty( $bar ) ? $drink_alone : $drink_together; // OK, exit is not passing status.
$var = (true or die ) and empty( $bar ) ? $drink_alone : $drink_together; // OK, die is not passing status.
$var = exit( $var ? $ok : $error ); // Bad x 2.

// Print can be used in expressions, so end of statement can be all sorts.
if ( print "hello" ) // OK.
    $var = 'foo'; // OK, not part of the print.

if ( print("hello") && $var ) // Bad x1, `$var`.
    $var = 'foo'; // OK, not part of the print.

// Bug #2209.
( 'auto' === $key ) ? print ' disabled ' : print ' enabled '; // OK.
( $fop === 1 ) ? print $foo : print $bar; // Bad x 2, each print statement should be examined separately.
( 1 === $foo ) ? print ( $foo ? 'ten' : $twenty ) : print $bar; // Bad x 2, $twenty & $bar.

// Ensure nesting levels are handled correctly.
( ( $fop === 1 ) ? print ( $var ? 10 : 20 ) : print 100 ); // OK.

// Ensure print statements with ternaries and without wrapping parentheses are handled correctly.
( 1 === 1 ) ? print ( $foo ? 'ten' : 'twenty' ) . $baz : print $bar; // Bad x 2, $baz and $bar.
$var = true && print $foo ? $bar : $baz; // Bad x 2, `$bar` and `$baz` should be flagged, not $foo.
print $foo ? 'ten' : 'twenty'; // OK.

// Ensure print statements with ternaries, without wrapping parentheses, but nested within parentheses are also handled correctly.
if ( print $foo ? 'ten' : 'twenty' ) {} // OK.
if ( print $foo ? $bar : $baz ) {} // Bad x 2, `$bar` and `$baz` should be flagged, not $foo.
$var = ( ( $fop === 1 ) ? ( print $foo ? 'ten' : $twenty ) : print $bar ); // Bad x 2, $twenty & $bar.

// Ternary statements can be chained and nested.
echo ( ! empty( $var ) && ( $var > 10 ? $foo : $bar ) ) ? 'go' : 'stop'; // OK.
echo isset( $var[ $keyA ? $keyA : $keyB ] ) ? 'go' : 'stop'; // OK.
echo '' !== implode( '', [ $valueA ? $valueA : $valueB, $valueC ? $valueD : $valueE ] ) ? 'go' : 'stop'; // OK.
echo '' !== ${true ? $foo : $bar} ? 'go' : 'stop'; // OK.

// Bug #677 (and #1507C).
echo ( ! empty( $my_bc_title ) ) ? wp_kses( $my_bc_title, allowed_tags() ) : (10 + 20); // OK.
echo ( ! empty( $my_bc_title ) ) ? wp_kses( $my_bc_title, allowed_tags() ) : get_the_title(); // Bad, should flag get_the_title(), not `!`.
// Without parentheses wrapping the empty(), this was already okay.
echo ! empty( $my_bc_title ) ? wp_kses( $my_bc_title, allowed_tags() ) : (10 + 20); // OK.
echo ( $is_mobile ) ? wp_json_encode( 'true' ) : wp_json_encode( 'false' ); // OK.

// Bug #1219.
echo ( $webinar->is_too_late_to_register ? '</a>' : '' ); // OK.
echo $webinar->is_too_late_to_register ? '</a>' : ''; // OK.
array_walk(
	$upcoming_webinars,
	function ( $webinar ) {
		echo ( $webinar->is_too_late_to_register ? '<a href="' . esc_url( $webinar->url ) . '">' : '' );
	}
); // OK.

// Bug #1617: code before a ternary should not be ignored if a short ternary is used.
echo $var ?: ''; // Bad, $var needs escaping.
echo $var ? /*comment*/ : ''; // Bad, $var needs escaping.
array_walk(
	$upcoming_webinars,
	function ( $webinar ) {
		echo ( $webinar->is_too_late_to_register ? : '' );
	}
); // Bad.

echo ESC_HTML . $var . unrelatedFunction( $var ); // Bad x 3.

// Bug #677#issuecomment-470407780: Parameters should be examined individually.
printf(
	'<li><a href="#%1$s" class="%2$s"%3$s%4$s>%5$s</a></li>',
	( '' !== $this->link_title ) ? ' title="' . esc_attr( $this->link_title ) . '"' : '',
	( '' !== $this->link_aria_label ) ? ' aria-label="' . esc_attr( $this->link_aria_label ) . '"' : '',
); // OK.

// The fix for #677 will also prevent false positives on parameter labels.
wp_die(
	title: 'label',
	message: 'error message',
);
