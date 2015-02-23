<div class="wrap">
    <h2><?php screen_icon('users'); ?><?php echo  BUtils::_('Simple WP Membership::Members') ?>
        <a href="admin.php?page=simple_wp_membership&member_action=add" class="add-new-h2"><?php echo  BUtils::_('Add New'); ?></a></h2>
    <form method="post">
        <p class="search-box">
            <label class="screen-reader-text" for="search_id-search-input">
                search:</label>
            <input id="search_id-search-input" type="text" name="s" value="" />
            <input id="search-submit" class="button" type="submit" name="" value="<?php echo  BUtils::_('search')?>" />
            <input type="hidden" name="page" value="simple_wp_membership" />
        </p>
    </form>
    <?php $this->prepare_items(); ?>
    <form method="post">
        <?php $this->display(); ?>
    </form>

    <p>
        <a href="admin.php?page=simple_wp_membership&member_action=add" class="button-primary"><?php echo  BUtils::_('Add New')?></a>
    </p>
</div><!-- end of wrap -->
