<div class="wrap" id="swpm-profile-page">
<form action="" method="post" name="swpm-edit-user" id="swpm-edit-user" class="validate"<?php do_action('user_new_form_tag');?>>
<input name="action" type="hidden" value="edituser" />
<?php wp_nonce_field( 'edit-swpmuser', '_wpnonce_edit-swpmuser' ) ?>
<h3>Edit Member</h3> 
<p><?php _e('Edit existing member details.'); ?></p>
<table class="form-table">
	<tr class="form-field form-required">
		<th scope="row"><label for="user_name"><?php _e('Username'); ?> <span class="description"><?php _e('(required)'); ?></span></label></th>
		<td><?php echo esc_attr($user_name); ?></td>
	</tr>
	<tr class="form-field form-required">
		<th scope="row"><label for="email"><?php _e('E-mail'); ?> <span class="description"><?php _e('(required)'); ?></span></label></th>
		<td><?php echo esc_attr($email); ?></td>
	</tr>
<?php include('admin_member_form_common_part.php');?>
<?php submit_button( __( ' Edit User '), 'primary', 'editswpmuser', true, array( 'id' => 'createswpmusersub' ) ); ?>
</form>
</div>
<script>
jQuery(document).ready(function($){
	$("#swpm-edit-user").validationEngine('attach');
});
</script>
