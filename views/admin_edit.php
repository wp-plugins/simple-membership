<div class="wrap" id="swpm-profile-page" type="edit">
    <form action="" method="post" name="swpm-edit-user" id="swpm-edit-user" class="validate"<?php do_action('user_new_form_tag');?>>
    <input name="action" type="hidden" value="edituser" />
    <?php wp_nonce_field( 'edit-swpmuser', '_wpnonce_edit-swpmuser' ) ?>
    <h3><?= BUtils::_('Edit Member') ?></h3>
    <p><?= BUtils::_('Edit existing member details.'); ?></p>
    <table class="form-table">
        <tr class="form-field form-required">
            <th scope="row"><label for="user_name"><?= BUtils::_('Username'); ?> <span class="description"><?= BUtils::_('(required)'); ?></span></label></th>
            <td><?php echo esc_attr($user_name); ?></td>
        </tr>
        <tr class="form-field form-required">
            <th scope="row"><label for="email"><?= BUtils::_('E-mail'); ?> <span class="description"><?= BUtils::_('(required)'); ?></span></label></th>
            <td><?php echo esc_attr($email); ?></td>
        </tr>
        <tr class="">
            <th scope="row"><label for="password"><?= BUtils::_('Password'); ?> <span class="description"><?php /* translators: password input field */_e('(twice, leave empty to retain old password)'); ?></span></label></th>
            <td><input class="regular-text"  name="password" type="password" id="pass1" autocomplete="off" /><br />
            <input class="regular-text" name="password_re" type="password" id="pass2" autocomplete="off" />
            <br />
            <div id="pass-strength-result"><?= BUtils::_('Strength indicator'); ?></div>
            <p class="description indicator-hint"><?= BUtils::_('Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).'); ?></p>
            </td>
	</tr>        
    <?php include('admin_member_form_common_part.php');?>
    <?= apply_filters('swpm_admin_custom_fields', '',$membership_level);?>
    <?php submit_button( BUtils::_('Edit User '), 'primary', 'editswpmuser', true, array( 'id' => 'createswpmusersub' ) ); ?>
</form>
</div>
<script>
jQuery(document).ready(function($){
    $("#swpm-edit-user").validationEngine('attach');
});
</script>
