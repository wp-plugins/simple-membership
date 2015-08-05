<div class="wrap">
    <h2><?php echo SwpmUtils::_('Simple WP Membership::Members') ?>
        <a href="admin.php?page=simple_wp_membership&member_action=add" class="add-new-h2"><?php echo SwpmUtils::_('Add New'); ?></a></h2>
        <?php include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_members_menu.php'); ?>
        <?php echo $output ?>
</div><!-- end of wrap -->
