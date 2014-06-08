	<tr>
		<th scope="row"><label for="first_name"><?php _e('First Name') ?> </label></th>
		<td><input class="regular-text" name="first_name" type="text" id="first_name" value="<?php echo esc_attr($first_name); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="last_name"><?php _e('Last Name') ?> </label></th>
		<td><input class="regular-text" name="last_name" type="text" id="last_name" value="<?php echo esc_attr($last_name); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="gender"><?php _e('Gender'); ?></label></th>
		<td><select class="regular-text" name="gender" id="gender">
				<?= BUtils::gender_dropdown($gender) ?>
			</select>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="phone"><?php _e('Phone') ?> </label></th>
		<td><input class="regular-text" name="phone" type="text" id="phone" value="<?php echo esc_attr($phone); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="address_street"><?php _e('Street') ?> </label></th>
		<td><input class="regular-text" name="address_street" type="text" id="address_street" value="<?php echo esc_attr($address_street); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="address_city"><?php _e('City') ?> </label></th>
		<td><input class="regular-text" name="address_city" type="text" id="address_city" value="<?php echo esc_attr($address_city); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="address_state"><?php _e('State') ?> </label></th>
		<td><input class="regular-text" name="address_state" type="text" id="address_state" value="<?php echo esc_attr($address_state); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="address_zipcode"><?php _e('Zipcode') ?> </label></th>
		<td><input class="regular-text" name="address_zipcode" type="text" id="address_zipcode" value="<?php echo esc_attr($address_zipcode); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="country"><?php _e('Country') ?> </label></th>
		<td><input class="regular-text" name="country" type="text" id="country" value="<?php echo esc_attr($country); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="company_name"><?php _e('Company') ?></label></th>
		<td><input name="company_name" type="text" id="company_name" class="code regular-text" value="<?php echo esc_attr($company_name); ?>" /></td>
	</tr>
	<tr class="form-required">
		<th scope="row"><label for="password"><?php _e('Password'); ?> <span class="description"><?php /* translators: password input field */_e('(twice, required)'); ?></span></label></th>
		<td><input class="regular-text" name="password" type="password" id="pass1" autocomplete="off" />
		<br />
		<input class="regular-text" name="password_re" type="password" id="pass2" autocomplete="off" />
		<br />
		<div id="pass-strength-result"><?php _e('Strength indicator'); ?></div>
		<p class="description indicator-hint"><?php _e('Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).'); ?></p>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="membership_level"><?php _e('Membership Level'); ?></label></th>
		<td><select class="regular-text" name="membership_level" id="membership_level">
            <?php foreach ($levels as $level):?>
            <option <?php echo ($level['id'] == $membership_level)? "selected='selected'": "";?> value="<?php echo $level['id'];?>"> <?php echo $level['alias']?></option>
            <?php endforeach;?>
			</select>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="account_state"><?php _e('Account Status'); ?></label></th>
		<td><select class="regular-text" name="account_state" id="account_state">
				<option value="active">Active</option>
				<option value="inactive">Inactive</option>
				<option value="pending">Pending</option>
				<option value="expired">Expired</option>
			</select>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="member_since"><?php _e('Member Since') ?> </label></th>
		<td><input class="regular-text" name="member_since" type="text" id="member_since" value="<?php echo esc_attr($member_since); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="subscription_starts"><?php _e('Subscription Starts') ?> </label></th>
		<td><input class="regular-text" name="subscription_starts" type="text" id="subscription_starts" value="<?php echo esc_attr($subscription_starts); ?>" /></td>
	</tr>
</tbody>
</table>
<script>
jQuery(document).ready(function($){
	$('#member_since').dateinput({'format':'yyyy-mm-dd',selectors: true,yearRange:[-100,100]});
	$('#subscription_starts').dateinput({'format':'yyyy-mm-dd',selectors: true,yearRange:[-100,100]});
});
</script>
