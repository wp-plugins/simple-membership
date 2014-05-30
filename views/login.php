<div class="swpm-login-widget-form">
<form id="swpm-login-form" name="swpm-login-form" method="post" action=""> 
<table width="95%" border="0" cellpadding="3" cellspacing="5" class="forms">
	    <tr>
	    	<td colspan="2"><label for="login_user_name" class="eMember_label">User Name</label></td>
	    </tr>
	    <tr>
	        <td colspan="2"><input type="text" class="swpm_text_field" id="swpm_user_name"  value="" size="30" name="swpm_user_name" /></td>
	    </tr>
	    <tr>
	    	<td colspan="2"><label for="login_pwd" class="eMember_label">Password</label></td>
		</tr>
	    <tr>
	        <td colspan="2"><input type="password" class="swpm_text_field" id="swpm_password" value="" size="30" name="swpm_password" /></td>
	    </tr>
	    <tr>
	        <td colspan="2"><input type="checkbox" name="rememberme" value="checked='checked'"> Remember Me</td>
	    </tr>
	    <tr>
	        <td colspan="2">
	        <input type="submit" name="swpm-login" value="Login"/>
	        </td>	       
	    </tr>
        <tr> 
	        <td colspan="2"> 
	        <a id="forgot_pass" href="<?php echo $password_reset_url;?>">Forgot Password?</a>
	        </td>
	    </tr>
	    <tr> 
	        <td colspan="2"><a id="register" class="register_link" href="<?php echo $join_url; ?>">Join Us</a></td>
	    </tr>        
	    <tr>
	    	<td colspan="2"><span> <?php echo $auth->get_message();?> </span></td>
	    </tr>
	</table>
</form>
</div>
