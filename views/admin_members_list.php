<form method="post">
    <p class="search-box">
        <label class="screen-reader-text" for="search_id-search-input">
            search:</label>
        <input id="search_id-search-input" type="text" name="s" value="" />
        <input id="search-submit" class="button" type="submit" name="" value="<?php echo SwpmUtils::_('search') ?>" />
        <input type="hidden" name="page" value="simple_wp_membership" />
    </p>
</form>
<?php $this->prepare_items(); ?>
<form method="post">
    <?php $this->display(); ?>
</form>

<p>
    <a href="admin.php?page=simple_wp_membership&member_action=add" class="button-primary"><?php echo SwpmUtils::_('Add New') ?></a>
</p>