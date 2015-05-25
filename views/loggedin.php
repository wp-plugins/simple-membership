<div class="swpm-login-widget-logged">
    <div class="swpm-logged-username">
        <div class="swpm-logged-username-label swpm-logged-label"><?php echo  SwpmUtils::_('Logged in as') ?></div>
        <div class="swpm-logged-username-value swpm-logged-value"><?php echo $auth->get('user_name'); ?></div>
    </div>
    <div class="swpm-logged-status">
        <div class="swpm-logged-status-label swpm-logged-label"><?php echo  SwpmUtils::_('Account Status') ?></div>
        <div class="swpm-logged-status-value swpm-logged-value"><?php echo ucfirst($auth->get('account_state')); ?></div>
    </div>
    <div class="swpm-logged-membership">
        <div class="swpm-logged-membership-label swpm-logged-label"><?php echo  SwpmUtils::_('Membership') ?></div>
        <div class="swpm-logged-membership-value swpm-logged-value"><?php echo $auth->get('alias'); ?></div>
    </div>
    <div class="swpm-logged-expiry">
        <div class="swpm-logged-expiry-label swpm-logged-label"><?php echo  SwpmUtils::_('Account Expiry') ?></div>
        <div class="swpm-logged-expiry-value swpm-logged-value"><?php echo $auth->get_expire_date(); ?></div>
    </div>
    <div class="swpm-logged-logout-link">
        <a href="?swpm-logout=true"><?php echo  SwpmUtils::_('Logout') ?></a>
    </div>
</div>