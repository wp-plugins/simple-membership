
<ul class="xoxo">
	<li id="text-1" class="widget widget_text"><h3 class="widgettitle"><?= BUtils::_('Logged in as')?></h3>
		<div class="textwidget"><b><?php echo $auth->userData->user_name;?><b></div>
	</li>
	<li id="text-2" class="widget widget_text"><h3 class="widgettitle"><?= BUtils::_('Account Status')?></h3>
		<div class="textwidget"><b><?php echo ucfirst($auth->userData->account_state);?></b></div>
	</li>
	<li id="text-2" class="widget widget_text"><h3 class="widgettitle"><?= BUtils::_('Membership')?></h3>
		<div class="textwidget"><b><?php echo $auth->userData->permitted->get('alias');?></b></div>
	</li>
	<li id="text-2" class="widget widget_text">
		<a href="?swpm-logout=true"><?= BUtils::_('Logout')?></a>
	</li>
</ul>