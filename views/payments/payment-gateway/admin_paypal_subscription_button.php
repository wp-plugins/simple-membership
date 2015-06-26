<?php
/* * ***************************************************************
 * Render the new PayPal Subscription payment button creation interface
 * ************************************************************** */
add_action('swpm_create_new_button_for_pp_subscription', 'swpm_create_new_pp_subscription_button');

function swpm_create_new_pp_subscription_button() {
    ?>
    <div class="postbox">
        <h3><label for="title"><?php echo SwpmUtils::_('PayPal Subscription Button Configuration'); ?></label></h3>
        <div class="inside">

<?php
//TODO - Need to work on the subscription button fields
echo '<p>This feature will be coming in the next version of the plugin.</p>';
echo '</div></div>';
return;
?>
            <form id="pp_button_config_form" method="post">
                <input type="hidden" name="button_type" value="<?php echo strip_tags($_REQUEST['button_type']); ?>">
                <input type="hidden" name="swpm_button_type_selected" value="1">

                <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">

                    <tr valign="top">
                        <th scope="row"><?php echo SwpmUtils::_('Button Title'); ?></th>
                        <td>
                            <input type="text" size="50" name="button_name" value="" required />
                            <p class="description">Give this membership payment button a name. Example: Gold membership payment</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo SwpmUtils::_('Membership Level'); ?></th>
                        <td>
                            <select id="membership_level_id" name="membership_level_id">
                                <?php echo SwpmUtils::membership_level_dropdown(); ?>
                            </select>
                            <p class="description">Select the membership level this payment button is for.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo SwpmUtils::_('Payment Amount'); ?></th>
                        <td>
                            <input type="text" size="6" name="payment_amount" value="" required />
                            <p class="description">Enter payment amount. Example values: 10.00 or 19.50 or 299.95 etc (do not put currency symbol).</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo SwpmUtils::_('Payment Currency'); ?></th>
                        <td>
                            <select id="payment_currency" name="payment_currency">
                                <option selected="selected" value="USD">US Dollars ($)</option>
                                <option value="EUR">Euros (€)</option>
                                <option value="GBP">Pounds Sterling (£)</option>
                                <option value="AUD">Australian Dollars ($)</option>
                                <option value="BRL">Brazilian Real (R$)</option>
                                <option value="CAD">Canadian Dollars ($)</option>
                                <option value="CNY">Chinese Yuan</option>
                                <option value="CZK">Czech Koruna</option>
                                <option value="DKK">Danish Krone</option>
                                <option value="HKD">Hong Kong Dollar ($)</option>
                                <option value="HUF">Hungarian Forint</option>
                                <option value="INR">Indian Rupee</option>
                                <option value="IDR">Indonesia Rupiah</option>
                                <option value="ILS">Israeli Shekel</option>
                                <option value="JPY">Japanese Yen (¥)</option>
                                <option value="MYR">Malaysian Ringgits</option>
                                <option value="MXN">Mexican Peso ($)</option>
                                <option value="NZD">New Zealand Dollar ($)</option>
                                <option value="NOK">Norwegian Krone</option>
                                <option value="PHP">Philippine Pesos</option>
                                <option value="PLN">Polish Zloty</option>
                                <option value="SGD">Singapore Dollar ($)</option>
                                <option value="ZAR">South African Rand (R)</option>
                                <option value="KRW">South Korean Won</option>
                                <option value="SEK">Swedish Krona</option>
                                <option value="CHF">Swiss Franc</option>
                                <option value="TWD">Taiwan New Dollars</option>
                                <option value="THB">Thai Baht</option>
                                <option value="TRY">Turkish Lira</option>
                                <option value="VND">Vietnamese Dong</option>
                            </select>
                            <p class="description">Select the currency for this payment button.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo SwpmUtils::_('Return URL'); ?></th>
                        <td>
                            <input type="text" size="100" name="return_url" value="" />
                            <p class="description">This is the URL the user will be redirected to after a successful payment. Enter the URL of your Thank You page here.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo SwpmUtils::_('PayPal Email'); ?></th>
                        <td>
                            <input type="text" size="50" name="paypal_email" value="" required />
                            <p class="description">Enter your PayPal email address. The payment will go to this PayPal account.</p>
                        </td>
                    </tr>                    

                    <tr valign="top">
                        <th scope="row"><?php echo SwpmUtils::_('Button Image URL'); ?></th>
                        <td>
                            <input type="text" size="100" name="button_image_url" value="" />
                            <p class="description">If you want to customize the look of the button using an image then enter the URL of the image.</p>
                        </td>
                    </tr> 
                    
                </table>

                <p class="submit">
                    <input type="submit" name="swpm_pp_subscription_save_submit" class="button-primary" value="<?php echo SwpmUtils::_('Save Payment Data'); ?>" >
                </p>

            </form>

        </div>
    </div>
    <?php
}

/*
 * Process submission and save the new PayPal Subscription payment button data
 */
add_action('swpm_create_new_button_process_submission', 'swpm_save_new_pp_subscription_button_data');

function swpm_save_new_pp_subscription_button_data() {
    if (isset($_REQUEST['swpm_pp_subscription_save_submit'])) {
        //This is a PayPal subscription button save event. Process the submission.
        //TODO - Do some basic validation check??
        
        
        //TODO Save the button data
//        $button_id = wp_insert_post(
//                array(
//                    'post_title' => strip_tags($_REQUEST['button_name']),
//                    'post_type' => 'swpm_payment_button',
//                    'post_content' => '',
//                    'post_status' => 'publish'
//                )
//        );
//
//        $button_type = strip_tags($_REQUEST['button_type']);
//        add_post_meta($button_id, 'button_type', $button_type);
//        add_post_meta($button_id, 'membership_level_id', strip_tags($_REQUEST['membership_level_id']));
//        add_post_meta($button_id, 'payment_amount', trim(strip_tags($_REQUEST['payment_amount'])));
//        add_post_meta($button_id, 'payment_currency', strip_tags($_REQUEST['payment_currency']));
//        add_post_meta($button_id, 'return_url', trim(strip_tags($_REQUEST['return_url'])));
//        add_post_meta($button_id, 'paypal_email', trim(strip_tags($_REQUEST['paypal_email'])));
//        add_post_meta($button_id, 'button_image_url', trim(strip_tags($_REQUEST['button_image_url'])));
//
//        //Redirect to the edit interface of this button with $button_id        
//        $url = admin_url() . 'admin.php?page=simple_wp_membership_payments&tab=edit_button&button_id=' . $button_id . '&button_type=' . $button_type;
//        SwpmMiscUtils::redirect_to_url($url);
    }
}

/* * **********************************************************************
 * End of new PayPal subscription payment button stuff
 * ********************************************************************** */