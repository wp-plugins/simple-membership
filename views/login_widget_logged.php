
<ul class="xoxo">
	<li id="text-1" class="widget widget_text"><h3 class="widgettitle"><?= BUtils::_('Logged in as')?></h3>
		<div class="textwidget"><b><?php echo $auth->get('user_name');?><b></div>
	</li>
	<li id="text-2" class="widget widget_text"><h3 class="widgettitle"><?= BUtils::_('Account Status')?></h3>
		<div class="textwidget"><b><?php echo ucfirst($auth->get('account_state'));?></b></div>
	</li>
	<li id="text-2" class="widget widget_text"><h3 class="widgettitle"><?= BUtils::_('Membership')?></h3>
		<div class="textwidget"><b><?php echo $auth->get('alias');?></b></div>
	</li>
	<li id="text-2" class="widget widget_text"><h3 class="widgettitle"><?= BUtils::_('Account Expiry')?></h3>
		<div class="textwidget"><b><?php echo $auth->get_expire_date();?></b></div>
	</li>
	<li id="text-2" class="widget widget_text">
		<a href="?swpm-logout=true"><?= BUtils::_('Logout')?></a>
	</li>
</ul>