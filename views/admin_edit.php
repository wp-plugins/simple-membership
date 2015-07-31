<div class="wrap" id="swpm-profile-page" type="edit">
    <form action="" method="post" name="swpm-edit-user" id="swpm-edit-user" class="validate"<?php do_action('user_new_form_tag');?>>
    <input name="action" type="hidden" value="edituser" />
    <?php wp_nonce_field( 'edit-swpmuser', '_wpnonce_edit-swpmuser' ) ?>
    <h3><?php echo  SwpmUtils::_('Edit Member') ?></h3>
    <p><?php echo  SwpmUtils::_('Edit existing member details.'); ?></p>
    <table class="form-table">
        <tr class="form-field form-required">
            <th scope="row"><label for="user_name"><?php echo  SwpmUtils::_('Username'); ?> <span class="description"><?php echo  SwpmUtils::_('(required)'); ?></span></label></th>
            <td><?php echo esc_attr($user_name); ?></td>
        </tr>
        <tr class="form-required">
            <th scope="row"><label for="email"><?php echo  SwpmUtils::_('E-mail'); ?> <span class="description"><?php echo  SwpmUtils::_('(required)'); ?></span></label></th>
            <td><input name="email"  class="regular-text validate[required,custom[email],ajax[ajaxEmailCall]]"  type="text" id="email" value="<?php echo esc_attr($email); ?>" /></td>
        </tr>
        <tr class="">
            <th scope="row"><label for="password"><?php echo  SwpmUtils::_('Password'); ?> <span class="description"><?php /* translators: password input field */_e('(twice, leave empty to retain old password)'); ?></span></label></th>
            <td><input class="regular-text"  name="password" type="password" id="pass1" autocomplete="off" /><br />
            <input class="regular-text" name="password_re" type="password" id="pass2" autocomplete="off" />
            <br />
            <div id="pass-strength-result"><?php echo  SwpmUtils::_('Strength indicator'); ?></div>
            <p class="description indicator-hint"><?php echo  SwpmUtils::_('Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).'); ?></p>
            </td>
	</tr> 
	<tr>
            <th scope="row"><label for="account_state"><?php echo  SwpmUtils::_('Account Status'); ?></label></th>
            <td><select class="regular-text" name="account_state" id="account_state">
                        <?php echo  SwpmUtils::account_state_dropdown($account_state);?>
                    </select>
            </td>
	</tr>        
	<tr>
            <th scope="row"><label for="account_state_change"><?php echo  SwpmUtils::_('Notify User'); ?></label></th>
            <td><input type="checkbox" id="account_status_change" name="account_status_change" />
                <p class="description indicator-hint">You can use this option to send a quick notification email to this member (the email will be sent when you hit the save button below).</p>
            </td>
	</tr>
    <?php include('admin_member_form_common_part.php');?>
        <tr>
		<th scope="row"><label for="subscr_id"><?php echo  SwpmUtils::_('Subscriber ID/Reference') ?> </label></th>
		<td><input class="regular-text" name="subscr_id" type="text" id="subscr_id" value="<?php echo esc_attr($subscr_id); ?>" /></td>
	</tr>        
        <tr>
		<th scope="row"><label for="last_accessed_from_ip"><?php echo  SwpmUtils::_('Last Accessed From IP') ?> </label></th>
		<td><?php echo esc_attr($last_accessed_from_ip); ?></td>
	</tr> 
        
    </table>
    
    <?php include('admin_member_form_common_js.php');?>
    <?php echo  apply_filters('swpm_admin_custom_fields', '',$membership_level);?>
    <?php submit_button( SwpmUtils::_('Edit User '), 'primary', 'editswpmuser', true, array( 'id' => 'createswpmusersub' ) ); ?>
</form>
</div>
<script>
jQuery(document).ready(function($){    
    $.validationEngineLanguage.allRules['ajaxEmailCall']['url']= '<?php echo admin_url('admin-ajax.php');?>';
    $.validationEngineLanguage.allRules['ajaxEmailCall']['extraData']= '&action=swpm_validate_email&member_id=<?php echo $member_id;?>';
    $("#swpm-edit-user").validationEngine('attach');
    $('#account_status_change').change(function(){
        var target = $(this).closest('tr');
        var $body = '<textarea rows="5" cols="60" id="notificationmailbody" name="notificationmailbody">' + SwpmSettings.statusChangeEmailBody + '</textarea>';
        var $head = '<input type="text" size="60" id="notificationmailhead" name="notificationmailhead" value="' + SwpmSettings.statusChangeEmailHead + '" />';
        var content = '<tr><th scope="row">Mail Subject</th><td>' + $head + '</td></tr>';
        content += '<tr><th scope="row">Mail Body</th><td>' + $body + '</td></tr>';
        if (this.checked) {
            target.after(content);
        }
        else {
            if (target.next('tr').find('#notificationmailhead').length > 0) {
                target.next('tr').remove();
                target.next('tr').remove();
            }
        }
    });
});
</script>
