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

<?
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

echo filter_var( $bar, FILTER_VALIDATE_EMAIL );
echo filter_input( INPUT_GET, $bar );