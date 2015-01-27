	<tr>
		<th scope="row"><label for="membership_level"><?= BUtils::_('Membership Level'); ?></label></th>
		<td><select class="regular-text" name="membership_level" id="membership_level">
            <?php foreach ($levels as $level):?>
            <option <?php echo ($level['id'] == $membership_level)? "selected='selected'": "";?> value="<?php echo $level['id'];?>"> <?php echo $level['alias']?></option>
            <?php endforeach;?>
			</select>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="subscription_starts"><?= BUtils::_('Access Starts') ?> </label></th>
		<td><input class="regular-text" name="subscription_starts" type="text" id="subscription_starts" value="<?php echo esc_attr($subscription_starts); ?>" /></td>
	</tr>  
        <tr>
		<th scope="row"><label for="first_name"><?= BUtils::_('First Name') ?> </label></th>
		<td><input class="regular-text" name="first_name" type="text" id="first_name" value="<?php echo esc_attr($first_name); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="last_name"><?= BUtils::_('Last Name') ?> </label></th>
		<td><input class="regular-text" name="last_name" type="text" id="last_name" value="<?php echo esc_attr($last_name); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="gender"><?= BUtils::_('Gender'); ?></label></th>
		<td><select class="regular-text" name="gender" id="gender">
				<?= BUtils::gender_dropdown($gender) ?>
			</select>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="phone"><?= BUtils::_('Phone') ?> </label></th>
		<td><input class="regular-text" name="phone" type="text" id="phone" value="<?php echo esc_attr($phone); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="address_street"><?= BUtils::_('Street') ?> </label></th>
		<td><input class="regular-text" name="address_street" type="text" id="address_street" value="<?php echo esc_attr($address_street); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="address_city"><?= BUtils::_('City') ?> </label></th>
		<td><input class="regular-text" name="address_city" type="text" id="address_city" value="<?php echo esc_attr($address_city); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="address_state"><?= BUtils::_('State') ?> </label></th>
		<td><input class="regular-text" name="address_state" type="text" id="address_state" value="<?php echo esc_attr($address_state); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="address_zipcode"><?= BUtils::_('Zipcode') ?> </label></th>
		<td><input class="regular-text" name="address_zipcode" type="text" id="address_zipcode" value="<?php echo esc_attr($address_zipcode); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="country"><?= BUtils::_('Country') ?> </label></th>
		<td><input class="regular-text" name="country" type="text" id="country" value="<?php echo esc_attr($country); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="company_name"><?= BUtils::_('Company') ?></label></th>
		<td><input name="company_name" type="text" id="company_name" class="code regular-text" value="<?php echo esc_attr($company_name); ?>" /></td>
	</tr>      
	<tr>
		<th scope="row"><label for="member_since"><?= BUtils::_('Member Since') ?> </label></th>
		<td><input class="regular-text" name="member_since" type="text" id="member_since" value="<?php echo esc_attr($member_since); ?>" /></td>
	</tr>
</tbody>
</table>
<script>
jQuery(document).ready(function($){
	$('#member_since').dateinput({'format':'yyyy-mm-dd',selectors: true,yearRange:[-100,100]});
	$('#subscription_starts').dateinput({'format':'yyyy-mm-dd',selectors: true,yearRange:[-100,100]});
});
</script>
