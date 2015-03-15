
<header class="entry-header">
    Delete Account
</header>
 <?php if (!empty($msg)) echo '<p>' . $msg . '</p>' ; ?>
<p  style="color:red;">
You are about to delete an account.This will delete user data associated with this account. 
It will also delete associated wordpress account.
(NOTE: For consistency, we do not allow deleting any associated wordpress account with administrator role).
Continue?       
</p>
<form method="post">
    <p>Password: <input name="account_delete_confirm_pass" type="password"></p>
    <p><input type="submit" name="confirm" value="Confirm" /> </p>
     <?php wp_nonce_field( 'swpm_account_delete_confirm', 'account_delete_confirm_nonce' ); ?>
</form>