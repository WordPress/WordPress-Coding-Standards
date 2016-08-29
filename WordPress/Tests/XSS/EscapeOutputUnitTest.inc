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
<h2><?php echo esc_html( $title ); // OK. ?></h2>
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

// Testing whitelisting comments.
echo $html_fragment; // Bad.
echo $html_fragment; // xss OK.
echo $html_fragment; // WPCS: XSS whitelist.
?><?php echo $html_fragment; // XSS pass. ?><?php

_deprecated_function( __FUNCTION__, '1.3.0', 'another_func' ); // Ok.
_deprecated_function( __FUNCTION__, '1.3.0', $another_func ); // Bad.
_deprecated_function( __FUNCTION__, '1.3.0', esc_html( $another_func ) ); // Ok.
_deprecated_file( __FILE__, '1.3.0' ); // Ok.
_deprecated_argument( __METHOD__, '1.3.0', 'The $arg is deprecated.' ); // Ok.
_doing_it_wrong( __METHOD__, "Invalid value for the 'bob' argument {$args['bob']}." ); // Bad.
_doing_it_wrong( __METHOD__, "Invalid value for the 'bob' argument " . esc_html( $args['bob'] ) . "." ); // Ok.

trigger_error( "There was an error: {$message}", E_USER_NOTICE ); // Bad.
trigger_error( "There was an error: " . esc_html( $message ), E_USER_NOTICE ); // Ok.

echo '<p>' . sprintf( esc_html__( 'Some text -> %sLink text%s', 'textdomain' ), '<a href="' . esc_url( add_query_arg( array( 'page' => 'my_page' ), admin_url( 'admin.php' ) ) ) . '">', '</a>' ). '</p>'; // Ok.

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
echo join( '<br>', array_map( 'esc_html', $items ) ); // Ok.

echo '<option name="' . esc_attr( $name ) . '"' .
     ( $name === $selected ? ' selected' : '' ) .
     '>' . esc_html( $value )
     . '</option>';

_deprecated_hook( 'some_filter', '1.3.0', esc_html__( 'The $arg is deprecated.' ), 'some_other_filter' ); // Ok.
_deprecated_hook( "filter_{$context}", '1.3.0', __( 'The $arg is deprecated.' ), sprintf( __( 'Some parsed message %s', $variable ) ) ); // Bad.
