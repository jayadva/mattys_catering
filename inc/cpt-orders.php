<?php
/**
 * Custom Post Type: Orders
 *
 * Registers the 'mattys_order' custom post type for storing catering orders.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the Orders custom post type
 */
function mattys_register_orders_cpt() {
    $labels = array(
        'name'                  => _x( 'Orders', 'Post type general name', 'mattys' ),
        'singular_name'         => _x( 'Order', 'Post type singular name', 'mattys' ),
        'menu_name'             => _x( 'Orders', 'Admin Menu text', 'mattys' ),
        'name_admin_bar'        => _x( 'Order', 'Add New on Toolbar', 'mattys' ),
        'add_new'               => __( 'Add New', 'mattys' ),
        'add_new_item'          => __( 'Add New Order', 'mattys' ),
        'new_item'              => __( 'New Order', 'mattys' ),
        'edit_item'             => __( 'Edit Order', 'mattys' ),
        'view_item'             => __( 'View Order', 'mattys' ),
        'all_items'             => __( 'All Orders', 'mattys' ),
        'search_items'          => __( 'Search Orders', 'mattys' ),
        'parent_item_colon'     => __( 'Parent Orders:', 'mattys' ),
        'not_found'             => __( 'No orders found.', 'mattys' ),
        'not_found_in_trash'    => __( 'No orders found in Trash.', 'mattys' ),
        'featured_image'        => _x( 'Order Image', 'Overrides the "Featured Image" phrase', 'mattys' ),
        'set_featured_image'    => _x( 'Set order image', 'Overrides the "Set featured image" phrase', 'mattys' ),
        'remove_featured_image' => _x( 'Remove order image', 'Overrides the "Remove featured image" phrase', 'mattys' ),
        'use_featured_image'    => _x( 'Use as order image', 'Overrides the "Use as featured image" phrase', 'mattys' ),
        'archives'              => _x( 'Order archives', 'The post type archive label', 'mattys' ),
        'insert_into_item'      => _x( 'Insert into order', 'Overrides the "Insert into post" phrase', 'mattys' ),
        'uploaded_to_this_item' => _x( 'Uploaded to this order', 'Overrides the "Uploaded to this post" phrase', 'mattys' ),
        'filter_items_list'     => _x( 'Filter orders list', 'Screen reader text', 'mattys' ),
        'items_list_navigation' => _x( 'Orders list navigation', 'Screen reader text', 'mattys' ),
        'items_list'            => _x( 'Orders list', 'Screen reader text', 'mattys' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => false,
        'rewrite'            => false,
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 25,
        'menu_icon'          => 'dashicons-cart',
        'supports'           => array( 'title' ),
        'show_in_rest'       => false,
    );

    register_post_type( 'mattys_order', $args );
}
add_action( 'init', 'mattys_register_orders_cpt' );

/**
 * Register custom order statuses
 */
function mattys_register_order_statuses() {
    register_post_status( 'order-pending', array(
        'label'                     => _x( 'Pending', 'Order status', 'mattys' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>', 'mattys' ),
    ) );

    register_post_status( 'order-confirmed', array(
        'label'                     => _x( 'Confirmed', 'Order status', 'mattys' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Confirmed <span class="count">(%s)</span>', 'Confirmed <span class="count">(%s)</span>', 'mattys' ),
    ) );

    register_post_status( 'order-completed', array(
        'label'                     => _x( 'Completed', 'Order status', 'mattys' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'mattys' ),
    ) );

    register_post_status( 'order-cancelled', array(
        'label'                     => _x( 'Cancelled', 'Order status', 'mattys' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', 'mattys' ),
    ) );
}
add_action( 'init', 'mattys_register_order_statuses' );

/**
 * Save order to custom post type
 *
 * @param array $order_data Order data array
 * @return int|WP_Error Post ID on success, WP_Error on failure
 */
function mattys_save_order( $order_data ) {
    // Generate order number
    $order_number = mattys_generate_order_number();

    // Create order title
    $order_title = sprintf(
        'Order #%s - %s',
        $order_number,
        $order_data['fullname']
    );

    // Insert the order post
    $order_id = wp_insert_post( array(
        'post_type'   => 'mattys_order',
        'post_title'  => $order_title,
        'post_status' => 'order-pending',
        'post_author' => 1,
    ) );

    if ( is_wp_error( $order_id ) ) {
        return $order_id;
    }

    // Save order meta data
    update_post_meta( $order_id, '_order_number', $order_number );
    update_post_meta( $order_id, '_order_date', current_time( 'mysql' ) );

    // Customer details
    update_post_meta( $order_id, '_customer_fullname', $order_data['fullname'] );
    update_post_meta( $order_id, '_customer_phone', $order_data['phonenumber'] );
    update_post_meta( $order_id, '_customer_email', $order_data['emailaddress'] );
    update_post_meta( $order_id, '_customer_company', $order_data['companyorganization'] );

    // Order details
    update_post_meta( $order_id, '_total_people', $order_data['totalpeoplecatering'] );
    update_post_meta( $order_id, '_delivery_date', $order_data['deliverydate'] );
    update_post_meta( $order_id, '_delivery_time', $order_data['deliverytime'] );
    update_post_meta( $order_id, '_pickup_delivery', $order_data['pickupdelivery'] );
    update_post_meta( $order_id, '_delivery_address', $order_data['deliveryaddress'] );

    // Additional info
    update_post_meta( $order_id, '_dietary_requirements', $order_data['dietaryrequirements'] );
    update_post_meta( $order_id, '_special_request', $order_data['specialrequest'] );

    // Order totals
    update_post_meta( $order_id, '_order_total', $order_data['totalamount'] );

    // Cart items (stored as JSON)
    update_post_meta( $order_id, '_order_items', wp_json_encode( $order_data['cartitems'] ) );

    return $order_id;
}

/**
 * Generate unique order number
 *
 * @return string Order number
 */
function mattys_generate_order_number() {
    $prefix = 'MTY';
    $date_part = date( 'Ymd' );

    // Get the count of orders for today
    $today_start = date( 'Y-m-d 00:00:00' );
    $today_end = date( 'Y-m-d 23:59:59' );

    $orders_today = get_posts( array(
        'post_type'      => 'mattys_order',
        'post_status'    => array( 'order-pending', 'order-confirmed', 'order-completed', 'order-cancelled' ),
        'posts_per_page' => -1,
        'date_query'     => array(
            array(
                'after'     => $today_start,
                'before'    => $today_end,
                'inclusive' => true,
            ),
        ),
        'fields' => 'ids',
    ) );

    $sequence = count( $orders_today ) + 1;
    $sequence_padded = str_pad( $sequence, 3, '0', STR_PAD_LEFT );

    return $prefix . $date_part . $sequence_padded;
}

/**
 * Add custom columns to orders list
 */
function mattys_orders_columns( $columns ) {
    $new_columns = array();

    foreach ( $columns as $key => $value ) {
        if ( $key === 'title' ) {
            $new_columns[ $key ] = $value;
            $new_columns['order_number'] = __( 'Order #', 'mattys' );
            $new_columns['customer'] = __( 'Customer', 'mattys' );
            $new_columns['order_total'] = __( 'Total', 'mattys' );
            $new_columns['delivery_date'] = __( 'Delivery Date', 'mattys' );
            $new_columns['order_status'] = __( 'Status', 'mattys' );
        } elseif ( $key !== 'date' ) {
            $new_columns[ $key ] = $value;
        }
    }

    $new_columns['date'] = __( 'Order Date', 'mattys' );

    return $new_columns;
}
add_filter( 'manage_mattys_order_posts_columns', 'mattys_orders_columns' );

/**
 * Display custom column content
 */
function mattys_orders_column_content( $column, $post_id ) {
    switch ( $column ) {
        case 'order_number':
            echo esc_html( get_post_meta( $post_id, '_order_number', true ) );
            break;

        case 'customer':
            $name = get_post_meta( $post_id, '_customer_fullname', true );
            $email = get_post_meta( $post_id, '_customer_email', true );
            echo esc_html( $name ) . '<br><small>' . esc_html( $email ) . '</small>';
            break;

        case 'order_total':
            echo esc_html( get_post_meta( $post_id, '_order_total', true ) );
            break;

        case 'delivery_date':
            $date = get_post_meta( $post_id, '_delivery_date', true );
            $time = get_post_meta( $post_id, '_delivery_time', true );
            echo esc_html( $date ) . '<br><small>' . esc_html( $time ) . '</small>';
            break;

        case 'order_status':
            $status = get_post_status( $post_id );
            $status_labels = array(
                'order-pending'   => '<span style="color: #f39c12;">Pending</span>',
                'order-confirmed' => '<span style="color: #3498db;">Confirmed</span>',
                'order-completed' => '<span style="color: #27ae60;">Completed</span>',
                'order-cancelled' => '<span style="color: #e74c3c;">Cancelled</span>',
            );
            echo isset( $status_labels[ $status ] ) ? $status_labels[ $status ] : esc_html( $status );
            break;
    }
}
add_action( 'manage_mattys_order_posts_custom_column', 'mattys_orders_column_content', 10, 2 );

/**
 * Make columns sortable
 */
function mattys_orders_sortable_columns( $columns ) {
    $columns['order_number'] = 'order_number';
    $columns['order_total'] = 'order_total';
    $columns['delivery_date'] = 'delivery_date';
    return $columns;
}
add_filter( 'manage_edit-mattys_order_sortable_columns', 'mattys_orders_sortable_columns' );

/**
 * Add meta boxes for order details
 */
function mattys_add_order_meta_boxes() {
    add_meta_box(
        'mattys_order_details',
        __( 'Order Details', 'mattys' ),
        'mattys_order_details_meta_box',
        'mattys_order',
        'normal',
        'high'
    );

    add_meta_box(
        'mattys_order_items',
        __( 'Order Items', 'mattys' ),
        'mattys_order_items_meta_box',
        'mattys_order',
        'normal',
        'default'
    );

    add_meta_box(
        'mattys_order_status_box',
        __( 'Order Status', 'mattys' ),
        'mattys_order_status_meta_box',
        'mattys_order',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'mattys_add_order_meta_boxes' );

/**
 * Order details meta box callback
 */
function mattys_order_details_meta_box( $post ) {
    wp_nonce_field( 'mattys_order_meta_box', 'mattys_order_meta_box_nonce' );

    $order_number = get_post_meta( $post->ID, '_order_number', true );
    $fullname = get_post_meta( $post->ID, '_customer_fullname', true );
    $phone = get_post_meta( $post->ID, '_customer_phone', true );
    $email = get_post_meta( $post->ID, '_customer_email', true );
    $company = get_post_meta( $post->ID, '_customer_company', true );
    $total_people = get_post_meta( $post->ID, '_total_people', true );
    $delivery_date = get_post_meta( $post->ID, '_delivery_date', true );
    $delivery_time = get_post_meta( $post->ID, '_delivery_time', true );
    $pickup_delivery = get_post_meta( $post->ID, '_pickup_delivery', true );
    $delivery_address = get_post_meta( $post->ID, '_delivery_address', true );
    $dietary = get_post_meta( $post->ID, '_dietary_requirements', true );
    $special = get_post_meta( $post->ID, '_special_request', true );
    $total = get_post_meta( $post->ID, '_order_total', true );
    ?>
    <style>
        .mattys-order-details { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .mattys-order-section { background: #f9f9f9; padding: 15px; border-radius: 5px; }
        .mattys-order-section h4 { margin-top: 0; color: #013F42; border-bottom: 2px solid #013F42; padding-bottom: 10px; }
        .mattys-order-row { margin-bottom: 10px; }
        .mattys-order-label { font-weight: bold; color: #333; }
        .mattys-order-value { color: #666; }
        .mattys-order-total { font-size: 24px; color: #013F42; font-weight: bold; }
    </style>

    <div class="mattys-order-details">
        <div class="mattys-order-section">
            <h4><?php esc_html_e( 'Customer Information', 'mattys' ); ?></h4>
            <div class="mattys-order-row">
                <span class="mattys-order-label"><?php esc_html_e( 'Order Number:', 'mattys' ); ?></span>
                <span class="mattys-order-value"><?php echo esc_html( $order_number ); ?></span>
            </div>
            <div class="mattys-order-row">
                <span class="mattys-order-label"><?php esc_html_e( 'Full Name:', 'mattys' ); ?></span>
                <span class="mattys-order-value"><?php echo esc_html( $fullname ); ?></span>
            </div>
            <div class="mattys-order-row">
                <span class="mattys-order-label"><?php esc_html_e( 'Phone:', 'mattys' ); ?></span>
                <span class="mattys-order-value"><a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a></span>
            </div>
            <div class="mattys-order-row">
                <span class="mattys-order-label"><?php esc_html_e( 'Email:', 'mattys' ); ?></span>
                <span class="mattys-order-value"><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></span>
            </div>
            <div class="mattys-order-row">
                <span class="mattys-order-label"><?php esc_html_e( 'Company:', 'mattys' ); ?></span>
                <span class="mattys-order-value"><?php echo esc_html( $company ?: '-' ); ?></span>
            </div>
        </div>

        <div class="mattys-order-section">
            <h4><?php esc_html_e( 'Delivery Information', 'mattys' ); ?></h4>
            <div class="mattys-order-row">
                <span class="mattys-order-label"><?php esc_html_e( 'People Catering For:', 'mattys' ); ?></span>
                <span class="mattys-order-value"><?php echo esc_html( $total_people ); ?></span>
            </div>
            <div class="mattys-order-row">
                <span class="mattys-order-label"><?php esc_html_e( 'Delivery Date:', 'mattys' ); ?></span>
                <span class="mattys-order-value"><?php echo esc_html( $delivery_date ); ?></span>
            </div>
            <div class="mattys-order-row">
                <span class="mattys-order-label"><?php esc_html_e( 'Delivery Time:', 'mattys' ); ?></span>
                <span class="mattys-order-value"><?php echo esc_html( $delivery_time ); ?></span>
            </div>
            <div class="mattys-order-row">
                <span class="mattys-order-label"><?php esc_html_e( 'Type:', 'mattys' ); ?></span>
                <span class="mattys-order-value"><?php echo esc_html( ucfirst( $pickup_delivery ) ); ?></span>
            </div>
            <?php if ( $delivery_address ) : ?>
            <div class="mattys-order-row">
                <span class="mattys-order-label"><?php esc_html_e( 'Delivery Address:', 'mattys' ); ?></span>
                <span class="mattys-order-value"><?php echo esc_html( $delivery_address ); ?></span>
            </div>
            <?php endif; ?>
        </div>

        <div class="mattys-order-section">
            <h4><?php esc_html_e( 'Additional Information', 'mattys' ); ?></h4>
            <div class="mattys-order-row">
                <span class="mattys-order-label"><?php esc_html_e( 'Dietary Requirements:', 'mattys' ); ?></span>
                <p class="mattys-order-value"><?php echo esc_html( $dietary ?: '-' ); ?></p>
            </div>
            <div class="mattys-order-row">
                <span class="mattys-order-label"><?php esc_html_e( 'Special Request:', 'mattys' ); ?></span>
                <p class="mattys-order-value"><?php echo esc_html( $special ?: '-' ); ?></p>
            </div>
        </div>

        <div class="mattys-order-section">
            <h4><?php esc_html_e( 'Order Total', 'mattys' ); ?></h4>
            <div class="mattys-order-total"><?php echo esc_html( $total ); ?></div>
        </div>
    </div>
    <?php
}

/**
 * Order items meta box callback
 */
function mattys_order_items_meta_box( $post ) {
    $items_json = get_post_meta( $post->ID, '_order_items', true );
    $items = json_decode( $items_json, true );

    if ( empty( $items ) ) {
        echo '<p>' . esc_html__( 'No items in this order.', 'mattys' ) . '</p>';
        return;
    }
    ?>
    <style>
        .mattys-items-table { width: 100%; border-collapse: collapse; }
        .mattys-items-table th, .mattys-items-table td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .mattys-items-table th { background: #013F42; color: white; }
        .mattys-items-table tr:hover { background: #f5f5f5; }
        .mattys-addon-badge { background: #3498db; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; }
    </style>

    <table class="mattys-items-table">
        <thead>
            <tr>
                <th><?php esc_html_e( 'Product', 'mattys' ); ?></th>
                <th><?php esc_html_e( 'Portion Size', 'mattys' ); ?></th>
                <th><?php esc_html_e( 'Price', 'mattys' ); ?></th>
                <th><?php esc_html_e( 'Cut Preference', 'mattys' ); ?></th>
                <th><?php esc_html_e( 'Qty', 'mattys' ); ?></th>
                <th><?php esc_html_e( 'Modifications', 'mattys' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $items as $item ) :
                $modifications = array();
                if ( ! empty( $item['modify_no_sandwich'] ) && is_array( $item['modify_no_sandwich'] ) ) {
                    $modifications = array_merge( $modifications, $item['modify_no_sandwich'] );
                }
                if ( ! empty( $item['modify_addon_sandwich'] ) && is_array( $item['modify_addon_sandwich'] ) ) {
                    $modifications = array_merge( $modifications, $item['modify_addon_sandwich'] );
                }
                $modifications_str = ! empty( $modifications ) ? implode( ', ', $modifications ) : '-';
            ?>
            <tr>
                <td>
                    <?php echo esc_html( $item['producttitle'] ); ?>
                    <?php if ( ! empty( $item['addondrinks'] ) && $item['addondrinks'] == 1 ) : ?>
                        <span class="mattys-addon-badge"><?php esc_html_e( 'Add-on Drink', 'mattys' ); ?></span>
                    <?php endif; ?>
                </td>
                <td><?php echo esc_html( $item['portion_size'] ?: '-' ); ?></td>
                <td><?php echo esc_html( $item['portion_size_price'] ?: '-' ); ?></td>
                <td><?php echo esc_html( $item['cut_preference'] ?: '-' ); ?></td>
                <td><?php echo esc_html( $item['quantity'] ); ?></td>
                <td><?php echo esc_html( $modifications_str ); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}

/**
 * Order status meta box callback
 */
function mattys_order_status_meta_box( $post ) {
    $current_status = get_post_status( $post->ID );
    $statuses = array(
        'order-pending'   => __( 'Pending', 'mattys' ),
        'order-confirmed' => __( 'Confirmed', 'mattys' ),
        'order-completed' => __( 'Completed', 'mattys' ),
        'order-cancelled' => __( 'Cancelled', 'mattys' ),
    );
    ?>
    <select name="order_status" id="order_status" style="width: 100%; padding: 8px;">
        <?php foreach ( $statuses as $status => $label ) : ?>
            <option value="<?php echo esc_attr( $status ); ?>" <?php selected( $current_status, $status ); ?>>
                <?php echo esc_html( $label ); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <p class="description"><?php esc_html_e( 'Update the order status', 'mattys' ); ?></p>
    <?php
}

/**
 * Save order meta box data
 */
function mattys_save_order_meta( $post_id ) {
    // Check nonce
    if ( ! isset( $_POST['mattys_order_meta_box_nonce'] ) ) {
        return;
    }

    if ( ! wp_verify_nonce( $_POST['mattys_order_meta_box_nonce'], 'mattys_order_meta_box' ) ) {
        return;
    }

    // Check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check permissions
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Save order status
    if ( isset( $_POST['order_status'] ) ) {
        $new_status = sanitize_text_field( $_POST['order_status'] );
        $valid_statuses = array( 'order-pending', 'order-confirmed', 'order-completed', 'order-cancelled' );

        if ( in_array( $new_status, $valid_statuses, true ) ) {
            wp_update_post( array(
                'ID'          => $post_id,
                'post_status' => $new_status,
            ) );
        }
    }
}
add_action( 'save_post_mattys_order', 'mattys_save_order_meta' );

/**
 * Add order statuses to the status dropdown
 */
function mattys_append_order_status_list() {
    global $post;

    if ( $post->post_type !== 'mattys_order' ) {
        return;
    }

    $statuses = array(
        'order-pending'   => __( 'Pending', 'mattys' ),
        'order-confirmed' => __( 'Confirmed', 'mattys' ),
        'order-completed' => __( 'Completed', 'mattys' ),
        'order-cancelled' => __( 'Cancelled', 'mattys' ),
    );

    $current_status = $post->post_status;
    ?>
    <script>
    jQuery(document).ready(function($) {
        <?php foreach ( $statuses as $status => $label ) : ?>
        $('select#post_status').append('<option value="<?php echo esc_js( $status ); ?>" <?php selected( $current_status, $status ); ?>><?php echo esc_js( $label ); ?></option>');
        <?php endforeach; ?>

        <?php if ( array_key_exists( $current_status, $statuses ) ) : ?>
        $('#post-status-display').text('<?php echo esc_js( $statuses[ $current_status ] ); ?>');
        <?php endif; ?>
    });
    </script>
    <?php
}
add_action( 'admin_footer-post.php', 'mattys_append_order_status_list' );
add_action( 'admin_footer-post-new.php', 'mattys_append_order_status_list' );
