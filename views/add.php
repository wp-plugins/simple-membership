<div class="swpm-registration-widget-form">
<form id="swpm-registration-form" name="swpm-registration-form" method="post" action="">
	<table>
		<tr>
			<td><label for="user_name">User Name</label></td>
			<td><input type="text" id="user_name" class="validate[required,custom[SWPMUserName],minSize[4],ajax[ajaxUserCall]]" value="<?php echo $user_name;?>" tabindex="1" size="50" name="user_name" /></td>
		</tr>
		<tr>
			<td><label for="email">Email</label></td>
			<td><input type="text" id="email" class="validate[required,custom[email],ajax[ajaxEmailCall]]" value="<?php echo $email;?>" tabindex="2" size="50" name="email" /></td>
		</tr>
		<tr>
			<td><label for="password">Password</label></td>
			<td><input type="password" id="password" value="" tabindex="3" size="50" name="password" /></td>
		</tr>
		<tr>
			<td><label for="password_re">Repeat Password</label></td>
			<td><input type="password" id="password_re" value="" tabindex="4" size="50" name="password_re" /></td>
		</tr>
		<tr>
			<td><label for="first_name">First Name</label></td>
			<td><input type="text" id="first_name" value="<?php echo $first_name;?>" tabindex="5" size="50" name="first_name" /></td>
		</tr>
		<tr>
			<td><label for="last_name">Last Name</label></td>
			<td><input type="text" id="last_name" value="<?php echo $last_name;?>" tabindex="6" size="50" name="last_name" /></td>
		</tr>
                <tr>
		<td><label for="gender"><?php _e('Gender'); ?></label></td>
		<td><select name="gender" id="gender">
				<?= bUtils::gender_dropdown() ?>
			</select>
		</td>
                </tr>
		<tr>
			<td><label for="phone">Phone</label></td>
			<td><input type="text" id="phone" value="<?php echo $phone;?>" tabindex="7" size="50" name="phone" /></td>
		</tr>
		<tr>
			<td><label for="address_street">Street</label></td>
			<td><input type="text" id="address_street" value="<?php echo $address_street;?>" tabindex="8" size="50" name="address_street" /></td>
		</tr>
		<tr>
			<td><label for="address_city">City</label></td>
			<td><input type="text" id="address_city" value="<?php echo $address_city;?>" tabindex="9" size="50" name="address_city" /></td>
		</tr>
		<tr>
			<td><label for="address_state">State</label></td>
			<td><input type="text" id="address_state" value="<?php echo $address_state;?>" tabindex="10" size="50" name="address_state" /></td>
		</tr>
		<tr>
			<td><label for="address_zipcode">Zipcode</label></td>
			<td><input type="text" id="address_zipcode" value="<?php echo $address_zipcode;?>" tabindex="11" size="50" name="address_zipcode" /></td>
		</tr>
		<tr>
			<td><label for="country">Country</label></td>
			<td><input type="text" id="country" value="<?php echo $country;?>" tabindex="12" size="50" name="country" /></td>
		</tr>
                <tr>
                        <td ><label for="company_name"><?php _e('Company') ?></label></td>
                        <td><input name="company_name" type="text" id="company_name" tabindex="13" size="50"  value="<?php echo esc_attr($company_name); ?>" /></td>
                </tr>
		<tr>
			<td><label for="membership_level">Membership Level</label></td>
			<td>
			<?php echo $membership_level_alias;?>
			<input type="hidden" value="<?php echo $membership_level;?>" size="50" name="membership_level" id="membership_level" />
			</td>
		</tr>
	</table>
	<p align="center"><input type="submit" value="Register" tabindex="6" id="submit" name="swpm_registration_submit" /></p>
	<input type="hidden" name="action" value="custom_posts" />
	<?php wp_nonce_field( 'name_of_my_action','name_of_nonce_field' ); ?>
</form>
</div>
<script>
jQuery(document).ready(function($){
	$.validationEngineLanguage.allRules['ajaxUserCall']['url']= '<?php echo admin_url('admin-ajax.php');?>';
        $.validationEngineLanguage.allRules['ajaxEmailCall']['url']= '<?php echo admin_url('admin-ajax.php');?>';
	$("#swpm-registration-form").validationEngine('attach');
});
</script>
