<?php
function mattys_scripts() {
    wp_enqueue_style( 'mattys-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css', 
        array(),
        wp_get_theme()->get( 'Version' )
    );
    wp_enqueue_style( 'mattys-simplebar', get_template_directory_uri() . '/assets/css/simplebar.css', 
        array(),
        wp_get_theme()->get( 'Version' )
    );
	wp_enqueue_style( 'mattys-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'mattys-style', 'rtl', 'replace' );


    wp_enqueue_script(
        'mattys-popper',
        'https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js',
        array( 'jquery' )
    );
    wp_enqueue_script(
        'mattys-bootstrap',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js',
        array( 'jquery' )
    );
    wp_enqueue_script(
        'mattys-simplebarjs',
        get_stylesheet_directory_uri() . '/assets/js/simplebar.min.js',
        array('jquery'), null
    ); 
    wp_enqueue_script(
        'mattys-validation',
        get_template_directory_uri() . '/assets/js/jquery.validate.min.js',
        array( 'jquery' )
    );
    wp_enqueue_script(
        'mattys-methodvalidation',
        get_template_directory_uri() . '/assets/js/additional-methods.min.js',
        array( 'jquery' )
    );
    wp_enqueue_script(
        'mattys-main',
        get_stylesheet_directory_uri() . '/assets/js/main.js',
        array('jquery'), null
    );

	wp_enqueue_script( 'mattys-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

    wp_localize_script( 'mattys-main', 'mattys_ajax',
        array( 'ajax_url' => admin_url( 'admin-ajax.php' ),
                'api_url' => get_home_url(),
                'assest_url' => get_stylesheet_directory_uri() . '/assets',
                'nonce'    => wp_create_nonce( 'mattys-nonce' ),
                'post_id' => get_the_ID(),
                'action' => 'newslisting_ajax_more_post',
		        'noPosts' => esc_html__('No older posts found', 'mattys-ajax'),
        ) 
    );
}
?>