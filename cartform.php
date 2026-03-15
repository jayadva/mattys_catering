<?php
/**
 * Template Name: Mattys Cart Form
 * @package front
 */
?>
<?php get_header(); ?>
<?php
    while ( have_posts() ) :
        the_post();

        get_template_part( 'template-parts/content', 'cart' );

    endwhile; // End of the loop.
?>
<?php get_footer(); ?>