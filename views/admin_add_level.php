<div class="wrap" id="swpm-level-page">

<form action="" method="post" name="swpm-create-level" id="swpm-create-level" class="validate"<?php do_action('level_new_form_tag');?>>
<input name="action" type="hidden" value="createlevel" />
<h3>Add Membership Level</h3>
<p><?= BUtils::_('Create new membership level.'); ?></p>
<?php wp_nonce_field( 'create-swpmlevel', '_wpnonce_create-swpmlevel' ) ?>
<table class="form-table">
    <tbody>
	<tr>
            <th scope="row"><label for="alias"><?= BUtils::_('Membership Level Name'); ?> <span class="description"><?= BUtils::_('(required)'); ?></span></label></th>
            <td><input class="regular-text validate[required]" name="alias" type="text" id="alias" value="" aria-required="true" /></td>
	</tr>
	<tr class="form-field form-required">
            <th scope="row"><label for="role"><?= BUtils::_('Default WordPress Role'); ?> <span class="description"><?= BUtils::_('(required)'); ?></span></label></th>
            <td><select  class="regular-text" name="role"><?php wp_dropdown_roles( 'subscriber' ); ?></select></td>
	</tr>
        <tr>
            <th scope="row"><label for="subscription_period"><?= BUtils::_('Access Duration'); ?> <span class="description"><?= BUtils::_('(required)'); ?></span></label>
            </th>
            <td>
                <p><input type="radio" checked="checked" value="<?= BMembershipLevel::NO_EXPIRY?>" name="subscription_duration_type" /> <?= BUtils::_('No Expiry (Access for this level will not expire until cancelled')?>)</p>                                
                <p><input type="radio" value="<?= BMembershipLevel::DAYS ?>" name="subscription_duration_type" /> <?= BUtils::_('Expire After')?> 
                    <input type="text" value="" name="subscription_period_<?= BMembershipLevel::DAYS ?>"> <?= BUtils::_('Days (Access expires after given number of days)')?></p>
                <p><input type="radio" value="<?= BMembershipLevel::WEEKS?>" name="subscription_duration_type" /> <?= BUtils::_('Expire After')?> 
                    <input type="text" value="" name="subscription_period_<?= BMembershipLevel::DAYS ?>"> <?= BUtils::_('Weeks (Access expires after given number of weeks')?></p>
                <p><input type="radio"  value="<?= BMembershipLevel::MONTHS?>" name="subscription_duration_type" /> <?= BUtils::_('Expire After')?> 
                    <input type="text" value="" name="subscription_period_<?= BMembershipLevel::MONTHS?>"> <?= BUtils::_('Months (Access expires after given number of months)')?></p>
                <p><input type="radio"  value="<?= BMembershipLevel::YEARS?>" name="subscription_duration_type" /> <?= BUtils::_('Expire After')?> 
                    <input type="text" value="" name="subscription_period_<?= BMembershipLevel::YEARS?>"> <?= BUtils::_('Years (Access expires after given number of years)')?></p>                
                <p><input type="radio" value="<?= BMembershipLevel::FIXED_DATE?>" name="subscription_duration_type" /> <?= BUtils::_('Fixed Date Expiry')?> 
                    <input type="text" class="swpm-date-picker" value="<?= date('Y-m-d');?>" name="subscription_period_<?= BMembershipLevel::FIXED_DATE?>"> <?= BUtils::_('(Access expires on a fixed date)')?></p>
            </td>        
        </tr>
<!--        
    <tr class="form-field">
        <th scope="row"><label for="role"><?= BUtils::_('Access to older posts'); ?></span></label></th>
        <td>
            <input type="checkbox" name="protect_older_posts" value="1" id="protect_older_posts" />
            <p class="description"><?= BUtils::_('Only allow access to posts published after the user\'s join date.')?></p>
        </td>
    </tr>   
-->
        <?= apply_filters('swpm_admin_add_membership_level_ui', '');?>
</tbody>
</table>
<?php submit_button( BUtils::_('Add New Membership Level '), 'primary', 'createswpmlevel', true, array( 'id' => 'createswpmlevelsub' ) ); ?>
</form>
</div>
<script>
jQuery(document).ready(function($){
    $("#swpm-create-level").validationEngine('attach');
    $('.swpm-date-picker').dateinput({'format':'yyyy-mm-dd',selectors: true,yearRange:[-100,100]});
});
</script>

