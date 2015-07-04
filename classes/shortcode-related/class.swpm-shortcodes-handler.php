<?php

class SwpmShortcodesHandler {

    public function __construct() {
        //Register all the shortcodes here
        add_shortcode('swpm_payment_button', array(&$this, 'swpm_payment_button_sc'));
    }

    public function swpm_payment_button_sc($args) {
        extract(shortcode_atts(array(
        'id' => '',
        'button_text' => '',
        'new_window' => '',
        ), $args));
        
        if(empty($id)){
            return '<p style="color: red;">Error! You must specify a button ID with this shortcode. Check the usage documentation.</p>';
        }
        
        $button_id = $id;
        $button = get_post($button_id); //Retrieve the CPT for this button
        $button_type = get_post_meta($button_id, 'button_type', true);
        
        include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/payments/payment-gateway/paypal_button_shortcode_view.php');
        
        $button_code = '';
        $button_code = apply_filters('swpm_payment_button_shortcode_for_'.$button_type, $button_code, $args);
        
        $output = '';
        $output .= '<div class="swpm-payment-button">'. $button_code .'</div>';
                
        return $output;
    }    

}