<div class="wrap" id="swpm-level-page">
<form action="" method="post" name="swpm-edit-level" id="swpm-edit-level" class="validate"<?php do_action('level_edit_form_tag');?>>
<input name="action" type="hidden" value="editlevel" />
<?php wp_nonce_field( 'edit-swpmlevel', '_wpnonce_edit-swpmlevel' ) ?>
<h3><?php echo  BUtils::_('Edit membership level'); ?></h3>
<p><?php echo  BUtils::_('Edit membership level.'); ?></p>
<table class="form-table">
    <tbody>
	<tr>
		<th scope="row"><label for="alias"><?php echo  BUtils::_('Membership Level Name'); ?> <span class="description"><?php echo  BUtils::_('(required)'); ?></span></label></th>
		<td><input class="regular-text validate[required]" name="alias" type="text" id="alias" value="<?php echo stripslashes($alias);?>" aria-required="true" /></td>
	</tr>
	<tr class="form-field form-required">
		<th scope="row"><label for="role"><?php echo  BUtils::_('Default WordPress Role'); ?> <span class="description"><?php echo  BUtils::_('(required)'); ?></span></label></th>
		<td><select  class="regular-text" name="role"><?php wp_dropdown_roles( $role ); ?></select></td>
	</tr>
    <tr>
        <th scope="row"><label for="subscription_period"><?php echo  BUtils::_('Access Duration'); ?> <span class="description"><?php echo  BUtils::_('(required)'); ?></span></label>
        </th>
        <td>
                <p><input type="radio" <?php echo  checked(BMembershipLevel::NO_EXPIRY,$subscription_duration_type,false)?> value="<?php echo  BMembershipLevel::NO_EXPIRY?>" name="subscription_duration_type" /> <?php echo  BUtils::_('No Expiry (Access for this level will not expire until cancelled)')?></p>                                
                <p><input type="radio" <?php echo  checked(BMembershipLevel::DAYS,$subscription_duration_type,false)?> value="<?php echo  BMembershipLevel::DAYS ?>" name="subscription_duration_type" /> <?php echo  BUtils::_('Expire After')?> 
                    <input type="text" value="<?php echo  checked(BMembershipLevel::DAYS,$subscription_duration_type,false)? $subscription_period: "";?>" name="subscription_period_<?php echo  BMembershipLevel::DAYS ?>"> <?php echo  BUtils::_('Days (Access expires after given number of days)')?></p>
                
                <p><input type="radio" <?php echo  checked(BMembershipLevel::WEEKS,$subscription_duration_type,false)?> value="<?php echo  BMembershipLevel::WEEKS?>" name="subscription_duration_type" /> <?php echo  BUtils::_('Expire After')?> 
                    <input type="text" value="<?php echo  checked(BMembershipLevel::WEEKS,$subscription_duration_type,false)? $subscription_period: "";?>" name="subscription_period_<?php echo  BMembershipLevel::WEEKS ?>"> <?php echo  BUtils::_('Weeks (Access expires after given number of weeks)')?></p>
                
                <p><input type="radio" <?php echo  checked(BMembershipLevel::MONTHS,$subscription_duration_type,false)?> value="<?php echo  BMembershipLevel::MONTHS?>" name="subscription_duration_type" /> <?php echo  BUtils::_('Expire After')?> 
                    <input type="text" value="<?php echo  checked(BMembershipLevel::MONTHS,$subscription_duration_type,false)? $subscription_period: "";?>" name="subscription_period_<?php echo  BMembershipLevel::MONTHS?>"> <?php echo  BUtils::_('Months (Access expires after given number of months)')?></p>
                
                <p><input type="radio" <?php echo  checked(BMembershipLevel::YEARS,$subscription_duration_type,false)?> value="<?php echo  BMembershipLevel::YEARS?>" name="subscription_duration_type" /> <?php echo  BUtils::_('Expire After')?> 
                    <input type="text" value="<?php echo  checked(BMembershipLevel::YEARS,$subscription_duration_type,false)? $subscription_period: "";?>" name="subscription_period_<?php echo  BMembershipLevel::YEARS?>"> <?php echo  BUtils::_('Years (Access expires after given number of years)')?></p>                
                
                <p><input type="radio" <?php echo  checked(BMembershipLevel::FIXED_DATE,$subscription_duration_type,false)?> value="<?php echo  BMembershipLevel::FIXED_DATE?>" name="subscription_duration_type" /> <?php echo  BUtils::_('Fixed Date Expiry')?> 
                    <input type="text" class="swpm-date-picker" value="<?php echo  checked(BMembershipLevel::FIXED_DATE,$subscription_duration_type,false)? $subscription_period: "";?>" name="subscription_period_<?php echo  BMembershipLevel::FIXED_DATE?>" id="subscription_period_<?php echo  BMembershipLevel::FIXED_DATE?>"> <?php echo  BUtils::_('(Access expires on a fixed date)')?></p>                                
        </td>        
    </tr>
    <?php echo  apply_filters('swpm_admin_edit_membership_level_ui', '', $id);?>
</tbody>
</table>
<?php submit_button(BUtils::_('Edit Membership Level '), 'primary', 'editswpmlevel', true, array( 'id' => 'editswpmlevelsub' ) ); ?>
</form>
</div>
<script>
jQuery(document).ready(function($){
    $('.swpm-date-picker').dateinput({'format':'yyyy-mm-dd',selectors: true,yearRange:[-100,100]});
    $("#swpm-edit-level").validationEngine('attach');
});
</script>
