<?php
//Render the edit payment button tab

include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/payments/payment-gateway/admin_paypal_buy_now_button.php');
include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/payments/payment-gateway/admin_paypal_subscription_button.php');

do_action('swpm_edit_payment_button_process_submission'); //Addons can use this hook to save the data after the form submit.
?>

<div style="background: #DDDDDD;border: 1px solid #CCCCCC;color: #383838;margin: 10px 0;padding: 5px 5px 5px 10px;text-shadow: 1px 1px #FFFFFF;">
    <p>
        <?php echo SwpmUtils::_('You can edit a payment button using this interface.'); ?>
    </p>
</div>

<?php
//Fire the action hook. The addons can render the payment button edit interface
//Button type (button_type) and Button id (button_id) must be present in the REQUEST
$button_type = strip_tags($_REQUEST['button_type']);
$button_id = strip_tags($_REQUEST['button_id']);
do_action('swpm_edit_payment_button_for_' . $button_type, $button_id);

