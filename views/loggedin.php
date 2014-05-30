<table>
	<tr>
		<td>Logged in as</td>	
		<td><b><?php echo $auth->userData->user_name;?><b></td>
	</tr>
	<tr>
		<td>Account Status</td>
		<td><b><?php echo ucfirst($auth->userData->account_state);?></b></td>
	</tr>
	<tr>
		<td>Membership</td>
		<td><b><?php echo $auth->userData->permitted->get('alias');?></b></td>
	</tr>
	<tr>
		<td colspan="2"><a href="?swpm-logout=true">Logout</a></td>
	</tr>
</table>