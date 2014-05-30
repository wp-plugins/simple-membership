
<ul class="xoxo">
	<li id="text-1" class="widget widget_text"><h3 class="widgettitle">Logged in as</h3>
		<div class="textwidget"><b><?php echo $auth->userData->user_name;?><b></div>
	</li>
	<li id="text-2" class="widget widget_text"><h3 class="widgettitle">Account Status</h3>
		<div class="textwidget"><b><?php echo ucfirst($auth->userData->account_state);?></b></div>
	</li>
	<li id="text-2" class="widget widget_text"><h3 class="widgettitle">Membership</h3>
		<div class="textwidget"><b><?php echo $auth->userData->permitted->get('alias');?></b></div>
	</li>
	<li id="text-2" class="widget widget_text">
		<a href="?swpm-logout=true">Logout</a>
	</li>
</ul>