<h2 class="nav-tab-wrapper">
    <a class="nav-tab <?php echo ($selected == "") ? 'nav-tab-active' : ''; ?>" href="admin.php?page=simple_wp_membership_levels"><?php echo SwpmUtils::_('Membership level') ?></a>
    <a class="nav-tab <?php echo ($selected == "manage") ? 'nav-tab-active' : ''; ?>" href="admin.php?page=simple_wp_membership_levels&level_action=manage"><?php echo SwpmUtils::_('Manage Content Production') ?></a>
    <a class="nav-tab <?php echo ($selected == "category_list") ? 'nav-tab-active' : ''; ?>" href="admin.php?page=simple_wp_membership_levels&level_action=category_list"><?php echo SwpmUtils::_('Category Protection') ?></a>
</h2>