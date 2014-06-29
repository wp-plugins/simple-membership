<div class="wrap" id="swpm-level-page">
<form action="" method="post" name="swpm-edit-level" id="swpm-edit-level" class="validate"<?php do_action('level_edit_form_tag');?>>
<input name="action" type="hidden" value="editlevel" />
<?php wp_nonce_field( 'edit-swpmlevel', '_wpnonce_edit-swpmlevel' ) ?>
<h3><?= BUtils::_('Edit membership level'); ?></h3>
<p><?= BUtils::_('Edit membership level.'); ?></p>
<table class="form-table">
    <tbody>
	<tr>
		<th scope="row"><label for="alias"><?= BUtils::_('Membership Level Name'); ?> <span class="description"><?= BUtils::_('(required)'); ?></span></label></th>
		<td><input class="regular-text validate[required]" name="alias" type="text" id="alias" value="<?php echo stripslashes($alias);?>" aria-required="true" /></td>
	</tr>
	<tr class="form-field form-required">
		<th scope="row"><label for="role"><?= BUtils::_('Default WordPress Role'); ?> <span class="description"><?= BUtils::_('(required)'); ?></span></label></th>
		<td><select  class="regular-text" name="role"><?php wp_dropdown_roles( $role ); ?></select></td>
	</tr>
    <tr>
        <th scope="row"><label for="subscription_unit"><?= BUtils::_('Subscription Duration'); ?> <span class="description"><?= BUtils::_('(required)'); ?></span></label>
        </th>
        <td>
            <fieldset>
            <div class="color-option">
                <input name="subscript_duration_type" id="subscript_duration_noexpire"
                <?php echo $noexpire?'checked="checked"': ""; ?>   type="radio" value="0" class="tog">
	            <table class="color-palette">
	            <tbody><tr>
		            <td style="width: 60px;"><b><?= BUtils::_('No Expiry') ?></b></td>
		            </tr>
	            </tbody></table>
            </div>
	            <div class="color-option">
                <input name="subscript_duration_type" id="subscript_duration_expire"
                <?php echo !$noexpire?'checked="checked"': ""; ?> type="radio" value="1" class="tog">
	            <table class="color-palette">
	            <tbody><tr>
		            <td style="background-color: #d1e5ee" title="fresh">
                    <input type="text" class="validate[required]" size="3" id="subscription_period" name="subscription_period"
                        value="<?php echo $noexpire?'':$subscription_period;?>"></td>
		            <td style="background-color: #cfdfe9" title="fresh">
                    <select id="subscription_unit" name="subscription_unit">
                   <?= BUtils::subscription_unit_dropdown($subscription_unit)?>
                    </select>
                    </td>
		            </tr>
	            </tbody></table>
            </div>
	            </fieldset>

        </td>
    </tr>
     <?= apply_filters('swpm_admin_edit_membership_level_ui', '', $id);?>
</tbody>
</table>
<?php submit_button(BUtils::_('Edit Membership Level '), 'primary', 'editswpmlevel', true, array( 'id' => 'editswpmlevelsub' ) ); ?>
</form>
</div>
<script>
jQuery(document).ready(function($){
    $('.tog:radio').on('update_deps click',function(){
        if($(this).attr('checked')){
            $("#swpm-edit-level").validationEngine('detach');
            if($(this).val()==0)
                $('#subscription_period').removeClass('validate[required]');
            else if($(this).val()==1)
                $('#subscription_period').addClass('validate[required]');
            $("#swpm-edit-level").validationEngine('attach');
        }
    });
    $('.tog:radio').trigger('update_deps');
});
</script>
