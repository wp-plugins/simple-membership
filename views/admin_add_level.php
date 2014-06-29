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
        <th scope="row"><label for="subscription_unit"><?= BUtils::_('Subscription Duration'); ?> <span class="description"><?= BUtils::_('(required)'); ?></span></label>
        </th>
        <td>
            <div class="color-option"><input name="subscript_duration_type" id="subscript_duration_noexpire" checked="checked" type="radio" value="0" class="tog">
                <table class="color-palette">
                <tbody>
                    <tr>
                    <td style="width: 60px;"><b><?= BUtils::_('No Expiry') ?></b></td>
                    </tr>
                </tbody></table>
            </div>
        </td>
        </tr>
        <tr>
            <th></th>
            <td>
                <div class="color-option"><input name="subscript_duration_type" id="subscript_duration_expire" type="radio" value="1" class="tog">
                <table class="color-palette">
                <tbody><tr>
                        <td style="background-color: #d1e5ee" title="fresh"><input type="text" class="validate[required]" size="3" id="subscription_period" name="subscription_period" value=""></td>
                        <td style="background-color: #cfdfe9" title="fresh">
                <select id="subscription_unit" name="subscription_unit">
                   <option value="Days">Days</option>
                   <option value="Weeks">Weeks</option>
                   <option value="Months">Months</option>
                   <option value="Years">Years</option>
                </select>
                </td>
                        </tr>
                </tbody></table>
            </div>
            </td>
        </tr>
        <?= apply_filters('swpm_admin_add_membership_level_ui', '');?>
</tbody>
</table>
<?php submit_button( BUtils::_('Add New Membership Level '), 'primary', 'createswpmlevel', true, array( 'id' => 'createswpmlevelsub' ) ); ?>
</form>
</div>
<script>
jQuery(document).ready(function($){
    $('.tog:radio').on('update_deps click',function(){
        if($(this).attr('checked')){
            $("#swpm-create-level").validationEngine('detach');
            if($(this).val() === 0)
                $('#subscription_period').removeClass('validate[required]');
            else if($(this).val() === 1)
                $('#subscription_period').addClass('validate[required]');
            $("#swpm-create-level").validationEngine('attach');
        }
    });
    $('.tog:radio').trigger('update_deps');
});
</script>

