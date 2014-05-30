<div class="wrap" id="swpm-profile-page">
<?php //screen_icon(); ?>
<form action="" method="post" name="swpm-create-user" id="swpm-create-user" class="validate"<?php do_action('user_new_form_tag');?>>
<input name="action" type="hidden" value="createuser" />
<?php wp_nonce_field( 'create-swpmuser', '_wpnonce_create-swpmuser' ) ?>
<h3>Add Member</h3>
<p><?php _e('Create a brand new user and add it to this site.'); ?></p>
<table class="form-table">
    <tbody>
	<tr class="form-required">
		<th scope="row"><label for="user_name"><?php _e('Username'); ?> <span class="description"><?php _e('(required)'); ?></span></label></th>
		<td><input class="regular-text validate[required,custom[SWPMUserName],minSize[4],ajax[ajaxUserCall]]" name="user_name" type="text" id="user_name" value="<?php echo esc_attr($user_name); ?>" aria-required="true" /></td>
	</tr>
	<tr class="form-required">
		<th scope="row"><label for="email"><?php _e('E-mail'); ?> <span class="description"><?php _e('(required)'); ?></span></label></th>
		<td><input name="email"  class="regular-text validate[required,custom[email],ajax[ajaxEmailCall]]"  type="text" id="email" value="<?php echo esc_attr($email); ?>" /></td>
	</tr>
<?php include('admin_member_form_common_part.php');?>
<?php submit_button( __( 'Add New Member '), 'primary', 'createswpmuser', true, array( 'id' => 'createswpmusersub' ) ); ?>
</form>
</div>
<script>
jQuery(document).ready(function($){
	$.validationEngineLanguage.allRules['ajaxUserCall']['url']= '<?php echo admin_url('admin-ajax.php');?>';
	$.validationEngineLanguage.allRules['ajaxEmailCall']['url']= '<?php echo admin_url('admin-ajax.php');?>';
	$("#swpm-create-user").validationEngine('attach');
});
</script>
