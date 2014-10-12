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