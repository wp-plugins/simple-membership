<div class="wrap" id="swpm-profile-page" type="add">
<?php //screen_icon(); ?>
<form action="" method="post" name="swpm-create-user" id="swpm-create-user" class="validate"<?php do_action('user_new_form_tag');?>>
<input name="action" type="hidden" value="createuser" />
<?php wp_nonce_field( 'create-swpmuser', '_wpnonce_create-swpmuser' ) ?>
<h3><?= BUtils::_('Add Member') ?></h3>
<p><?= BUtils::_('Create a brand new user and add it to this site.'); ?></p>
<table class="form-table">
    <tbody>
	<tr class="form-required">
            <th scope="row"><label for="user_name"><?= BUtils::_('User name'); ?> <span class="description"><?= BUtils::_('(required)'); ?></span></label></th>
            <td><input class="regular-text validate[required,custom[noapostrophe],custom[SWPMUserName],minSize[4],ajax[ajaxUserCall]]" name="user_name" type="text" id="user_name" value="<?php echo esc_attr(stripslashes($user_name)); ?>" aria-required="true" /></td>
	</tr>
	<tr class="form-required">
            <th scope="row"><label for="email"><?= BUtils::_('E-mail'); ?> <span class="description"><?= BUtils::_('(required)'); ?></span></label></th>
            <td><input name="email"  class="regular-text validate[required,custom[email],ajax[ajaxEmailCall]]"  type="text" id="email" value="<?php echo esc_attr($email); ?>" /></td>
	</tr>
	<tr class="form-required">
            <th scope="row"><label for="password"><?= BUtils::_('Password'); ?> <span class="description"><?php /* translators: password input field */_e('(twice, required)'); ?></span></label></th>
            <td><input class="regular-text"  name="password" type="password" id="pass1" autocomplete="off" />
            <br />
            <input class="regular-text" name="password_re" type="password" id="pass2" autocomplete="off" />
            <br />
            <div id="pass-strength-result"><?= BUtils::_('Strength indicator'); ?></div>
            <p class="description indicator-hint"><?= BUtils::_('Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).'); ?></p>
            </td>
	</tr>        
<?php include('admin_member_form_common_part.php');?>
<?php submit_button( BUtils::_('Add New Member '), 'primary', 'createswpmuser', true, array( 'id' => 'createswpmusersub' ) ); ?>
</form>
</div>
<script>
jQuery(document).ready(function($){
	$.validationEngineLanguage.allRules['ajaxUserCall']['url']= '<?php echo admin_url('admin-ajax.php');?>';
	$.validationEngineLanguage.allRules['ajaxEmailCall']['url']= '<?php echo admin_url('admin-ajax.php');?>';
	$("#swpm-create-user").validationEngine('attach');
});
</script>
