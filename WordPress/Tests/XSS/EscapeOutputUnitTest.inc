<?php
// Using auto-escaped functions in Loop
while ( have_posts() ) {
	the_tags(); // OK
	the_category(); // OK

	// OK
	?>
	<a href="<?php the_permalink(); ?>" alt="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
	<?php

	the_content(); // OK
	the_date(); // OK
}
?>

<h2><?php echo $title; // Bad ?></h2>
<h2><?php echo esc_html( $title ); // OK ?></h2>
<h2><?php echo apply_filters( 'the_title', $title ); // Bad, no sanitizing function ?></h2>

<?php
// issue:#53
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
echo $foo; // Bad, should have escaping function
echo 'Some Raw String';  // Good

echo '' . $bad; // Bad, should not validate
echo "this is $bad"; // Bad
echo esc_html( $good . $better ) . $foo; // Bad, should escape all concatenated elements
echo esc_html( $food . 'include'  ); // Good, eveything inside the escaping/sanitizing function should pass
echo esc_html( strtoupper( $ok ) ) . $foo; // Bad, again
echo esc_html( strtoupper( $ok ) ) . ' ' . esc_html( strtolower( $ok ) ); // OK

_e( $some_nasty_var ); // Bad

echo filter_var( $bar, FILTER_VALIDATE_EMAIL );
echo filter_input( INPUT_GET, $bar, FILTER_SANITIZE_SPECIAL_CHARS );

echo '<input type="checkbox" name="' . esc_attr( 'field[' . $id . ']' ) . '" value="on" ' . checked( $current, 'on', false ) . '> ';

echo ent2ncr( $text );

echo number_format ( 1024 );

echo ent2ncr( esc_html( $_data ) );

echo $foo ? $foo : 'no foo'; // Bad
echo empty( $foo ) ? 'no foo' : $foo; // Bad
echo $foo ? esc_html( $foo ) : 'no foo'; // OK

echo 4; // OK

exit( $foo ); // Bad
exit( esc_html( $foo ) ); // OK

die( $foo ); // Bad
die( esc_html( $foo ) ); // OK

printf( 'Hello %s', $foo ); // Bad
printf( 'Hello %s', esc_html( $foo ) ); // OK
printf( 'Hello %s! Hi %s!', esc_html( $foo ), $bar ); // Bad

vprintf( 'Hello %s', array( $foo ) ); // Bad
vprintf( 'Hello %s', array( esc_html( $foo ) ) ); // OK

// The below checks that functions which are marked as needed further sanitization
// don't spill over into later arguments when nested in a function call. There was
// a bug which would cause line 84 to be marked as needing sanitization because _x()
// is marked as needing sanitization.
do_something(
	_x( 'Some string', 'context', 'domain' )
	, array( $foo ) // OK
);

// There was a bug where an empty exit followed by other code would give an error.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // OK
} else {
	other();
}

printf(
	/* translators: this comment is just for you. */
	esc_html__( 'Hello %s.', 'domain' )
	, 'world'
	// There were other long arguments down here "in real life", which is why this was multi-line.
);

wp_die( $message ); // Bad
wp_die( esc_html( $message ) ); // OK
wp_die( esc_html( $message ), $title ); // Bad
wp_die( esc_html( $message ), '', array( 'back_link' => $link_text ) ); // Bad
wp_die( esc_html( $message ), '', array( 'back_link' => esc_html( $link_text ) ) ); // OK
wp_die( esc_html( $message ), '', array( 'response' => 200 ) ); // OK

echo '<h2>', esc_html( $foo ), '</h2>'; // OK
echo 'a', 'b'; // OK
echo 'Hello, ', $foo; // Bad
echo esc_html( $foo ), $bar; // Bad
echo (int) $foo, $bar; // Bad
echo (int) get_post_meta( $post_id, SOME_NUMBER, true ), do_something( $else ); // Bad

wp_die( -1 ); // OK

?>
<p class="notice"><?php echo esc_html( $message ) ?></p> <!-- OK -->
<input type="submit" name="sync-progress" class="button button-primary button-large" value="<?php esc_attr_e( 'Start Sync', 'foo' ); ?>" /><!-- OK -->
<input type="hidden" name="sync-action" class="sync-action" value="<?php echo esc_attr( $continue_sync ? 'sync_progress' : '' ); ?>" /><!-- OK -->
<?php

// Testing whitelisting comments.
echo $html_fragment; // Bad
echo $html_fragment; // xss OK.
echo $html_fragment; // WPCS: XSS whitelist.
?><?php echo $html_fragment; // XSS pass ?><?php

_deprecated_function( __FUNCTION__, '1.3.0', 'another_func' ); // OK
_deprecated_function( __FUNCTION__, '1.3.0', $another_func ); // Bad
_deprecated_function( __FUNCTION__, '1.3.0', esc_html( $another_func ) ); // OK
_deprecated_file( __FILE__, '1.3.0' ); // OK
_deprecated_argument( __METHOD__, '1.3.0', 'The $arg is deprecated.' ); // OK
_doing_it_wrong( __METHOD__, "Invalid value for the 'bob' argument {$args['bob']}." ); // Bad
_doing_it_wrong( __METHOD__, "Invalid value for the 'bob' argument " . esc_html( $args['bob'] ) . "." ); // OK

trigger_error( "There was an error: {$message}", E_USER_NOTICE ); // Bad
trigger_error( "There was an error: " . esc_html( $message ), E_USER_NOTICE ); // OK

echo '<p>' . sprintf( esc_html__( 'Some text -> %sLink text%s', 'textdomain' ), '<a href="' . esc_url( add_query_arg( array( 'page' => 'my_page' ), admin_url( 'admin.php' ) ) ) . '">', '</a>' ). '</p>'; // OK

echo '<br/><strong>' . sprintf( esc_html__( 'Found %d results', 'textdomain' ), (int) $result_count ) . '</strong><br/><br/>'; // OK

echo sprintf( 'Hello %s', $foo ); // Bad
echo sprintf( 'Hello %s', esc_html( $foo ) ); // OK
echo sprintf( 'Hello %s! Hi %s!', esc_html( $foo ), $bar ); // Bad

echo vsprintf( 'Hello %s', array( $foo ) ); // Bad
echo vsprintf( 'Hello %s', array( esc_html( $foo ) ) ); // OK

echo sprintf( __( 'Welcome to Genesis %s', 'genesis' ), PARENT_THEME_BRANCH ); // Bad x 2
echo sprintf( esc_html__( 'Welcome to Genesis %s', 'genesis' ), esc_html( PARENT_THEME_BRANCH ) ); // OK

echo esc_html( strval( $_var ) ? $_var : gettype( $_var ) ); // OK
echo ( $is_hidden ) ? ' style="display:none;"' : ''; // OK
echo sprintf( 'Howdy, %s', esc_html( $name ? $name : __( 'Partner' ) ) ); // OK

_e( 'Something' ); // Bad
esc_html_e( 'Something' ); // OK
