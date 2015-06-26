<?php
//Render the create new payment button tab

include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/payments/payment-gateway/admin_paypal_buy_now_button.php');
include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/payments/payment-gateway/admin_paypal_subscription_button.php');

do_action('swpm_create_new_button_process_submission');//Addons can use this hook to save the data after the form submit then redirect to the "edit" interface of that newly created button.
    
?>

<div style="background: #DDDDDD;border: 1px solid #CCCCCC;color: #383838;margin: 10px 0;padding: 5px 5px 5px 10px;text-shadow: 1px 1px #FFFFFF;">
    <p>
        <?php echo SwpmUtils::_('You can create new payment button for your memberships using this interface.'); ?>
    </p>
</div>

<?php
if (!isset($_REQUEST['swpm_button_type_selected'])) {
    //Button type hasn't been selected. Show the selection option.
    ?>
    <div class="postbox">
        <h3><label for="title"><?php echo SwpmUtils::_('Select Payment Button Type'); ?></label></h3>
        <div class="inside">
            <form action="" method="post">
                <input type="radio" name="button_type" value="pp_buy_now" checked>PayPal Buy Now
                <br />
                <input type="radio" name="button_type" value="pp_subscription">PayPal Subscription
                <br />
                <?php
                apply_filters('swpm_new_button_select_button_type', '');
                ?>

                <br />
                <input type="submit" name="swpm_button_type_selected" class="button-primary" value="<?php echo SwpmUtils::_('Next'); ?>" />
            </form>

        </div>
    </div><!-- end of .postbox -->
    <?php
} else {
    //Button type has been selected. Show the payment button configuration option.
    //Fire the action hook. The addons can render the payment button configuration option as appropriate.
    $button_type = strip_tags($_REQUEST['button_type']);
    do_action('swpm_create_new_button_for_'.$button_type);    
    //The payment addons will create the button from then redirect to the "edit" interface of that button after save.
    
}
?>