<?php

add_filter('swpm_payment_button_shortcode_for_pp_buy_now', 'swpm_render_pp_buy_now_button_sc_output', 10, 2);

function swpm_render_pp_buy_now_button_sc_output($button_code, $args) {

    $button_id = isset($args['id']) ? $args['id'] : '';
    if (empty($button_id)) {
        return '<p style="color: red;">Error! swpm_render_pp_buy_now_button_sc_output() function requires the button ID value to be passed to it.</p>';
    }

    $settings = SwpmSettings::get_instance();
    $button_cpt = get_post($button_id); //Retrieve the CPT for this button

    $membership_level_id = get_post_meta($button_id, 'membership_level_id', true);
    $paypal_email = get_post_meta($button_id, 'paypal_email', true);
    $payment_amount = get_post_meta($button_id, 'payment_amount', true);
    if(!is_numeric($payment_amount)){
        return '<p style="color: red;">Error! The payment amount value of the button must be a numeric number. Example: 49.50 </p>';
    }
    $payment_amount = round($payment_amount, 2);//round the amount to 2 decimal place.   
    $payment_currency = get_post_meta($button_id, 'payment_currency', true);
    
    $sandbox_enabled = $settings->get_value('enable-sandbox-testing');
    $notify_url = SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL . '/?swpm_process_ipn=1';
    $return_url = get_post_meta($button_id, 'return_url', true);
    if(empty($return_url)){
        $return_url = SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL;
    }
    $cancel_url = SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL;
    
    $custom_field_value = 'subsc_ref=' . $membership_level_id;
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $custom_field_value .= '&user_ip='.$user_ip;
    if(SwpmMemberUtils::is_member_logged_in()){
        $custom_field_value .= '&swpm_id='.SwpmMemberUtils::get_logged_in_members_id();
    }
    $custom_field_value = apply_filters('swpm_custom_field_value_filter', $custom_field_value);

    /* === PayPal Buy Now Button Form === */
    $output = '';
    $output .= '<div class="swpm-button-wrapper swpm-pp-buy-now-wrapper">';
    if ($sandbox_enabled) {
        $output .= '<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">';
    } else {
        $output .= '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">';
    }

    $output .= '<input type="hidden" name="cmd" value="_xclick" />';
    $output .= '<input type="hidden" name="charset" value="utf-8" />';
    $output .= '<input type="hidden" name="bn" value="TipsandTricks_SP" />';    
    $output .= '<input type="hidden" name="business" value="' . $paypal_email . '" />';
    $output .= '<input type="hidden" name="amount" value="'.$payment_amount.'" />';
    $output .= '<input type="hidden" name="currency_code" value="'.$payment_currency.'" />';
    $output .= '<input type="hidden" name="item_number" value="'.$button_id.'" />';
    $output .= '<input type="hidden" name="item_name" value="' . htmlspecialchars($button_cpt->post_title) . '" />';
    
    $output .= '<input type="hidden" name="no_shipping" value="1" />';//Do not prompt for an address
    
    $output .= '<input type="hidden" name="notify_url" value="' . $notify_url . '" />';
    $output .= '<input type="hidden" name="return" value="' . $return_url . '" />';
    $output .= '<input type="hidden" name="cancel_return" value="' . $cancel_url . '" />';

    $output .= '<input type="hidden" name="custom" value="' . $custom_field_value . '" />';
    
    $button_image_url = get_post_meta($button_id, 'button_image_url', true);
    if (!empty($button_image_url)) {
        $output .= '<input type="image" src="' . $button_image_url . '" class="swpm-buy-now-button-submit" alt="' . SwpmUtils::_('Buy Now') . '"/>';
    } else {  
        $button_text = (isset($args['button_text']))?  $args['button_text'] : SwpmUtils::_('Buy Now');
        $output .= '<input type="submit" class="swpm-buy-now-button-submit" value="' . $button_text . '" />';
    }
    
    $output .= '</div>'; //End .swpm_button_wrapper

    return $output;
}