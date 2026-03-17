<?php
if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

// INCLUDES
require get_template_directory() . '/inc/init.php';
require get_template_directory() . '/inc/enqueue.php';
require get_template_directory() . '/inc/custom-header.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/shortcodes/productlisting.php';
require get_template_directory() . '/inc/cpt-orders.php';
require get_template_directory() . '/inc/emailorder.php';

// ACTION AND FILTER HOOKS
add_action( 'wp_enqueue_scripts', 'mattys_scripts' );

if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}
?>