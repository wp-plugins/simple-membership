<div class="wrap">
    <h2><?php echo  SwpmUtils::_('Simple WP Membership::Membership Levels') ?>
        <a href="admin.php?page=simple_wp_membership_levels&level_action=add" class="add-new-h2"><?php echo esc_html_x('Add New', 'Level'); ?></a></h2>
    
    <?php include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_membership_level_menu.php'); ?>
    
    <form method="post">
        <p class="search-box">
            <label class="screen-reader-text" for="search_id-search-input">
                search:</label>
            <input id="search_id-search-input" type="text" name="s" value="" />
            <input id="search-submit" class="button" type="submit" name="" value="<?php echo  SwpmUtils::_('search')?>" />
            <input type="hidden" name="page" value="simple_wp_membership" />
        </p>
    </form>
    <?php $this->prepare_items(); ?>
    <form method="post">
        <?php $this->display(); ?>
    </form>

    <p>
        <a href="admin.php?page=simple_wp_membership_levels&level_action=add" class="button-primary"><?php echo  SwpmUtils::_('Add New') ?></a>
    </p>

</div><!-- end of .wrap -->
