<div class="swpm-login-widget-form">
<form id="swpm-login-form" name="swpm-login-form" method="post" action="">
<table width="95%" border="0" cellpadding="3" cellspacing="5" class="forms">
	    <tr>
	    	<td colspan="2"><label for="login_user_name" class="eMember_label"><?= BUtils::_('User Name')?></label></td>
	    </tr>
	    <tr>
	        <td colspan="2"><input type="text" class="swpm_text_field" id="swpm_user_name"  value="" size="30" name="swpm_user_name" /></td>
	    </tr>
	    <tr>
	    	<td colspan="2"><label for="login_pwd" class="eMember_label"><?= BUtils::_('Password')?></label></td>
		</tr>
	    <tr>
	        <td colspan="2"><input type="password" class="swpm_text_field" id="swpm_password" value="" size="30" name="swpm_password" /></td>
	    </tr>
	    <tr>
	        <td colspan="2"><input type="checkbox" name="rememberme" value="checked='checked'"> <?= BUtils::_('Remember Me')?></td>
	    </tr>
	    <tr>
	        <td colspan="2">
                    <input type="submit" name="swpm-login" value="<?= BUtils::_('Login')?>"/>
	        </td>
	    </tr>
        <tr>
	        <td colspan="2">
	        <a id="forgot_pass" href="<?= $password_reset_url;?>"><?= BUtils::_('Forgot Password')?>?</a>
	        </td>
	    </tr>
	    <tr>
	        <td colspan="2"><a id="register" class="register_link" href="<?= $join_url; ?>"><?= BUtils::_('Join Us')?></a></td>
	    </tr>
	    <tr>
	    	<td colspan="2"><span class="swpm-login-widget-action-msg"><?= $auth->get_message();?></span></td>
	    </tr>
	</table>
</form>
</div>
