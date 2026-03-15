<?php
add_action('wp_ajax_mattysorderemail', 'mattysorderemail');
add_action('wp_ajax_nopriv_mattysorderemail', 'mattysorderemail');

function mattysorderemail() {
    if ( ! wp_verify_nonce( $_POST['nonce'], 'mattys-nonce') ) {
        wp_send_json(['success' => false, 'errormessage' => 'Invalid security token.']);
        wp_die();
    }

    // Sanitize form inputs
    $fullname = sanitize_text_field($_POST['fullname']);
    $phonenumber = sanitize_text_field($_POST['phonenumber']);
    $emailaddress = sanitize_email($_POST['emailaddress']);
    $companyorganization = sanitize_text_field($_POST['companyorganization']);
    $totalpeoplecatering = sanitize_text_field($_POST['totalpeoplecatering']);
    $deliverydate = sanitize_text_field($_POST['deliverydate']);
    $deliverytime = sanitize_text_field($_POST['deliverytime']);
    $pickupdelivery = sanitize_text_field($_POST['pickupdelivery']);
    $dietaryrequirements = sanitize_textarea_field($_POST['dietaryrequirements']);
    $specialrequest = sanitize_textarea_field($_POST['specialrequest']);
    $deliveryAddress = sanitize_text_field($_POST['deliveryAddress']);
    
    // Handle beverage add-ons array
    $beverageaddons = '';
    if (isset($_POST['beverageaddons']) && is_array($_POST['beverageaddons'])) {
        $beverageaddons = implode(', ', array_map('sanitize_text_field', $_POST['beverageaddons']));
    }
    
    // Handle cart items (JSON string from localStorage)
    $cartitems = [];
    if (isset($_POST['cartitems']) && !empty($_POST['cartitems'])) {
        $cartitems = json_decode(stripslashes($_POST['cartitems']), true);
    }

    // Build cart items HTML
    $cartitemshtml = '';
    $cartitemshtmldrinks = '';
    
    if (!empty($cartitems) && is_array($cartitems)) {
        $cartitemshtml .= '<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">';
        $cartitemshtml .= '<thead>';
        $cartitemshtml .= '<tr style="background-color: #013F42; color: white;">';
        $cartitemshtml .= '<th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Product</th>';
        $cartitemshtml .= '<th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Portion Size</th>';
        $cartitemshtml .= '<th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Price</th>';
        $cartitemshtml .= '<th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Cut Preference</th>';
        $cartitemshtml .= '<th style="padding: 12px; text-align: center; border: 1px solid #ddd;">Qty</th>';
        $cartitemshtml .= '<th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Modifications</th>';
        $cartitemshtml .= '</tr>';
        $cartitemshtml .= '</thead>';
        $cartitemshtml .= '<tbody>';
        
        foreach ($cartitems as $item) {
            $modifications = [];
            if (!empty($item['modify_no_sandwich']) && is_array($item['modify_no_sandwich'])) {
                $modifications = array_merge($modifications, $item['modify_no_sandwich']);
            }
            if (!empty($item['modify_addon_sandwich']) && is_array($item['modify_addon_sandwich'])) {
                $modifications = array_merge($modifications, $item['modify_addon_sandwich']);
            }
            $modificationsStr = !empty($modifications) ? implode(', ', $modifications) : '-';
            
            if($item['addondrinks'] == 0) {
                $cartitemshtml .= '<tr>';
                $cartitemshtml .= '<td style="padding: 12px; border: 1px solid #ddd;">' . esc_html($item['producttitle']) . '</td>';
                $cartitemshtml .= '<td style="padding: 12px; border: 1px solid #ddd;">' . esc_html($item['portion_size'] ?: '-') . '</td>';
                $cartitemshtml .= '<td style="padding: 12px; border: 1px solid #ddd;">' . esc_html($item['portion_size_price'] ?: '-') . '</td>';
                $cartitemshtml .= '<td style="padding: 12px; border: 1px solid #ddd;">' . esc_html($item['cut_preference'] ?: '-') . '</td>';
                $cartitemshtml .= '<td style="padding: 12px; text-align: center; border: 1px solid #ddd;">' . esc_html($item['quantity']) . '</td>';
                $cartitemshtml .= '<td style="padding: 12px; border: 1px solid #ddd;">' . esc_html($modificationsStr) . '</td>';
                $cartitemshtml .= '</tr>';
            }
        }
        
        $cartitemshtml .= '</tbody>';
        $cartitemshtml .= '</table>';

        $cartitemshtml .= '<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">';
        $cartitemshtml .= '<tr>';
        $cartitemshtml .= '<td style="padding: 12px; border: 1px solid #ddd; font-size:18px; text-align: left;"><strong>TOTAL AMOUNT</strong></td>';
        $cartitemshtml .= '<td style="padding: 12px; border: 1px solid #ddd; font-size:18px; text-align: right;"><strong>' . $_POST['totalamount'] . '</strong></td>';
        $cartitemshtml .= '</tr>';
        $cartitemshtml .= '</table>';

        $getTotalDrinks = count(array_filter($cartitems, function($item) { return $item['addondrinks'] == 1; }));

        if ($getTotalDrinks > 0) {
            $cartitemshtmldrinks .= '<h2 style="color: #013F42; border-bottom: 2px solid #013F42; padding-bottom: 10px;">Add On Drinks</h2>';
            $cartitemshtmldrinks .= '<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">';
            $cartitemshtmldrinks .= '<thead>';
            $cartitemshtmldrinks .= '<tr style="background-color: #013F42; color: white;">';
            $cartitemshtmldrinks .= '<th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Product</th>';
            $cartitemshtmldrinks .= '<th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Category Name</th>'; 
            $cartitemshtmldrinks .= '<th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Portion Size</th>';
            $cartitemshtmldrinks .= '<th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Price</th>';
            $cartitemshtmldrinks .= '<th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Cut Preference</th>';
            $cartitemshtmldrinks .= '<th style="padding: 12px; text-align: center; border: 1px solid #ddd;">Qty</th>';
            $cartitemshtmldrinks .= '<th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Modifications</th>';
            $cartitemshtmldrinks .= '</tr>';
            $cartitemshtmldrinks .= '</thead>';
            $cartitemshtmldrinks .= '<tbody>';
            
            foreach ($cartitems as $item) {
                $modifications = [];
                if (!empty($item['modify_no_sandwich']) && is_array($item['modify_no_sandwich'])) {
                    $modifications = array_merge($modifications, $item['modify_no_sandwich']);
                }
                if (!empty($item['modify_addon_sandwich']) && is_array($item['modify_addon_sandwich'])) {
                    $modifications = array_merge($modifications, $item['modify_addon_sandwich']);
                }
                $modificationsStr = !empty($modifications) ? implode(', ', $modifications) : '-';
                
                if($item['addondrinks'] == 1) {
                    $cartitemshtmldrinks .= '<tr>';
                    $cartitemshtmldrinks .= '<td style="padding: 12px; border: 1px solid #ddd;">' . esc_html($item['producttitle']) . '</td>';
                    $cartitemshtmldrinks .= '<td style="padding: 12px; border: 1px solid #ddd;">' . esc_html($item['catname']) . '</td>';
                    $cartitemshtmldrinks .= '<td style="padding: 12px; border: 1px solid #ddd;">' . esc_html($item['portion_size'] ?: '-') . '</td>';
                    $cartitemshtmldrinks .= '<td style="padding: 12px; border: 1px solid #ddd;">' . esc_html($item['portion_size_price'] ?: '-') . '</td>';
                    $cartitemshtmldrinks .= '<td style="padding: 12px; border: 1px solid #ddd;">' . esc_html($item['cut_preference'] ?: '-') . '</td>';
                    $cartitemshtmldrinks .= '<td style="padding: 12px; text-align: center; border: 1px solid #ddd;">' . esc_html($item['quantity']) . '</td>';
                    $cartitemshtmldrinks .= '<td style="padding: 12px; border: 1px solid #ddd;">' . esc_html($modificationsStr) . '</td>';
                    $cartitemshtmldrinks .= '</tr>';
                }
            }
            
            $cartitemshtmldrinks .= '</tbody>';
            $cartitemshtmldrinks .= '</table>';
        }

    }

    
    if (isset($deliveryAddress) && !empty($deliveryAddress)) {
        $deliveryAddDisplay = '<tr>
            <td style="padding: 8px 0;"><strong>Delivery Address:</strong></td>
            <td style="padding: 8px 0;">' . esc_html($deliveryAddress) . '</td>
        </tr>';
    }    
    // Build email HTML
    $emailbody = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>New Catering Order</title>
    </head>
    <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 800px; margin: 0 auto; padding: 20px;">
        <div style="background-color: #013F42; padding: 20px; text-align: center;">
            <h1 style="color: white; margin: 0;">New Catering Order</h1>
        </div>
        
        <div style="background-color: #f9f9f9; padding: 20px; border: 1px solid #ddd;">
            <h2 style="color: #013F42; border-bottom: 2px solid #013F42; padding-bottom: 10px;">Customer Details</h2>
            
            <table style="width: 100%; margin-bottom: 20px;">
                <tr>
                    <td style="padding: 8px 0; width: 200px;"><strong>Full Name:</strong></td>
                    <td style="padding: 8px 0;">' . esc_html($fullname) . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Phone Number:</strong></td>
                    <td style="padding: 8px 0;">' . esc_html($phonenumber) . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Email Address:</strong></td>
                    <td style="padding: 8px 0;"><a href="mailto:' . esc_attr($emailaddress) . '">' . esc_html($emailaddress) . '</a></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Company/Organization:</strong></td>
                    <td style="padding: 8px 0;">' . esc_html($companyorganization ?: '-') . '</td>
                </tr>
            </table>
            
            <h2 style="color: #013F42; border-bottom: 2px solid #013F42; padding-bottom: 10px;">Order Details</h2>
            
            <table style="width: 100%; margin-bottom: 20px;">
                <tr>
                    <td style="padding: 8px 0; width: 200px;"><strong>People Catering For:</strong></td>
                    <td style="padding: 8px 0;">' . esc_html($totalpeoplecatering) . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Delivery Date:</strong></td>
                    <td style="padding: 8px 0;">' . esc_html($deliverydate) . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Delivery Time:</strong></td>
                    <td style="padding: 8px 0;">' . esc_html($deliverytime) . ' (Added 3 hours from the original time.)</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Pick-up or Delivery:</strong></td>
                    <td style="padding: 8px 0;">' . esc_html($pickupdelivery) . '</td>
                </tr>
                '.$deliveryAddDisplay.'
            </table>
            
            <h2 style="color: #013F42; border-bottom: 2px solid #013F42; padding-bottom: 10px;">Order Items</h2>
            ' . $cartitemshtml . '
            
            ' . $cartitemshtmldrinks . '
            
            <h2 style="color: #013F42; border-bottom: 2px solid #013F42; padding-bottom: 10px;">Additional Information</h2>
            
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 8px 0; width: 200px; vertical-align: top;"><strong>Dietary Requirements:</strong></td>
                    <td style="padding: 8px 0;">' . nl2br(esc_html($dietaryrequirements ?: '-')) . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; vertical-align: top;"><strong>Special Request:</strong></td>
                    <td style="padding: 8px 0;">' . nl2br(esc_html($specialrequest ?: '-')) . '</td>
                </tr>
            </table>
        </div>
        
        <div style="background-color: #333; color: white; padding: 15px; text-align: center; margin-top: 20px;">
            <p style="margin: 0;">This order was submitted from ' . get_bloginfo('name') . '</p>
        </div>
    </body>
    </html>';

    // Email settings
    // $to = array(get_option('admin_email'), 'xuan.lee@adva.group'); // Send to admin email, you can change this
    $to = array(get_option('admin_email'), 'jayson.albano+123@adva.group'); 
    $subject = 'New Catering Order from ' . $fullname;
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: Matty\'s Sandwiches <' . get_option('admin_email') . '>',
        'Reply-To: ' . $fullname . ' <' . $emailaddress . '>'
    );

    // Send the email
    $sent = wp_mail($to, $subject, $emailbody, $headers);

    // Also send confirmation to customer
    $customer_subject = 'Your Catering Order Confirmation - Matty\'s Sandwiches';
    $customer_headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: Matty\'s Sandwiches <' . get_option('admin_email') . '>'
    );
    
    $customer_sent = wp_mail($emailaddress, $customer_subject, $emailbody, $customer_headers);

    if ($sent) {
        wp_send_json([
            'success' => true,
            'message' => 'Your order has been submitted successfully! We will contact you shortly.',
            'redirect' => home_url() // Optional: redirect to thank you page
        ]);
    } else {
        wp_send_json([
            'success' => false,
            'errormessage' => 'There was an error sending your order. Please try again or contact us directly.'
        ]);
    }

    wp_die();
}
?>