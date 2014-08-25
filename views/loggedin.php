<table>
	<tr>
		<td><?= BUtils::_('Logged in as')?></td>
		<td><b><?php echo $auth->get('user_name');?><b></td>
	</tr>
	<tr>
		<td><?= BUtils::_('Account Status')?></td>
		<td><b><?php echo ucfirst($auth->get('account_state'));?></b></td>
	</tr>
	<tr>
		<td><?= BUtils::_('Membership')?></td>
		<td><b><?php echo $auth->get('alias');?></b></td>
	</tr>
	<tr>
		<td><?= BUtils::_('Account Expiry')?></td>
		<td><b><?php echo $auth->get_expire_date();?></b></td>
	</tr>
	<tr>
		<td colspan="2"><a href="?swpm-logout=true"><?= BUtils::_('Logout')?></a></td>
	</tr>
</table>