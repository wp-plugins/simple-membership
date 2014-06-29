<table>
	<tr>
		<td><?= BUtils::_('Logged in as')?></td>
		<td><b><?php echo $auth->userData->user_name;?><b></td>
	</tr>
	<tr>
		<td><?= BUtils::_('Account Status')?></td>
		<td><b><?php echo ucfirst($auth->userData->account_state);?></b></td>
	</tr>
	<tr>
		<td><?= BUtils::_('Membership')?></td>
		<td><b><?php echo $auth->userData->permitted->get('alias');?></b></td>
	</tr>
	<tr>
		<td colspan="2"><a href="?swpm-logout=true"><?= BUtils::_('Logout')?></a></td>
	</tr>
</table>