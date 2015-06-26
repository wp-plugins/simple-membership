<div class="wrap">

    <h2><?php echo SwpmUtils::_('Simple WP Membership::Settings') ?></h2>

    <?php do_action("swpm-draw-tab"); ?>

    <div id="poststuff"><div id="post-body">
            <div class="postbox">
                <h3><label for="title"><?php echo SwpmUtils::_('Generate a Registration Completion link') ?></label></h3>
                <div class="inside">

                    <p><strong><?php echo SwpmUtils::_('You can manually generate a registration completion link here and give it to your customer if they have missed the email that was automatically sent out to them after the payment.') ?></strong></p>

                    <form action="" method="post">
                        <table>
                            <tr>
                                <?php echo SwpmUtils::_('Generate Registration Completion Link') ?>
                            <br /><input type="radio" value="one" name="swpm_link_for" />For a Particular Member ID
                            <input type="text" name="member_id" size="5" value="" />
                            <br /> <strong> <?php echo SwpmUtils::_('OR') ?> </strong>
                            <br /><input type="radio" checked="checked" value="all" name="swpm_link_for" /> <?php echo SwpmUtils::_('For All Pending Registrations') ?>
                            </tr>
                            <tr>
                                <td><?php echo SwpmUtils::_('Registration Completion Links Will Appear Below:') ?><br/>
                                    <?php foreach ($links as $key => $link): ?>
                                        <input type="text" size="100" readonly="readonly" name="link[<?php echo $key ?>]" value="<?php echo $link; ?>"/><br/>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><?php echo SwpmUtils::_('Send Registration Reminder Email too') ?> <input type="checkbox" value="checked" name="swpm_reminder_email"></td>
                            </tr>
                            <tr>
                                <td><input type="submit" name="submit" class="button-primary" value="<?php echo SwpmUtils::_('Submit') ?>" /></td>
                            </tr>
                        </table>
                    </form>

                </div></div>

        </div></div><!-- end of poststuff and post-body -->
</div><!-- end of wrap -->
