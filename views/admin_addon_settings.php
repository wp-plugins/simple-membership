<div class="wrap">

    <h2><?php echo SwpmUtils::_('Simple WP Membership::Settings') ?></h2>

    <?php do_action("swpm-draw-tab"); ?>

    <p>
        <?php echo SwpmUtils::_("Some of the simple membership plugin's addon settings and options will be displayed here (if you have them)") ?>
    </p>
    <form action="" method="POST">
        <input type="hidden" name="tab" value="<?php echo $current_tab; ?>" />
        <?php do_action('swpm_addon_settings_section'); ?>
        <?php submit_button('Save Changes', 'primary', 'swpm-addon-settings'); ?>
    </form>
</div><!-- end of wrap -->