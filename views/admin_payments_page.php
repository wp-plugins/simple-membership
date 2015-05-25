<?php
$output = '';
?>

<h1><?php echo SwpmUtils::_('Simple Membership::Payments') ?></h1>
<div class="wrap">
    <div id="poststuff"><div id="post-body">

            <div style="background: #DDDDDD;border: 1px solid #CCCCCC;color: #383838;margin: 10px 0;padding: 5px 5px 5px 10px;text-shadow: 1px 1px #FFFFFF;">
                <p>
                    <?php echo SwpmUtils::_('All the payments/transactions of your members are recorded here.'); ?>
                </p>
            </div>

            <div class="postbox">
                <h3><label for="title">Search Transaction</label></h3>
                <div class="inside">
                    <?php echo SwpmUtils::_('Search for a transaction by using email or name'); ?>
                    <br /><br />
                    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
                        <input name="swpm_txn_search" type="text" size="40" value="<?php echo isset($_POST['swpm_txn_search']) ? $_POST['swpm_txn_search'] : ''; ?>"/>
                        <input type="submit" name="swpm_txn_search_btn" class="button" value="<?php echo SwpmUtils::_('Search'); ?>" />
                    </form>
                </div></div>

            <?php
            include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'classes/admin-includes/class.swpm-payments-list-table.php');
            //Create an instance of our package class...
            $payments_list_table = new SWPMPaymentsListTable();

            //Check if an action was performed
            if (isset($_REQUEST['action'])) { //Do list table form row action tasks
                if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete_txn') { //Delete link was clicked for a row in list table
                    $record_id = strip_tags($_REQUEST['id']);
                    $payments_list_table->delete_record($record_id);
                    $success_msg = '<div id="message" class="updated"><p><strong>';
                    $success_msg .= SwpmUtils::_('The selected entry was deleted!');
                    $success_msg .= '</strong></p></div>';
                    echo $success_msg;
                }
            }

            //Fetch, prepare, sort, and filter our data...
            $payments_list_table->prepare_items();
            ?>
            <form id="tables-filter" method="get" onSubmit="return confirm('Are you sure you want to perform this bulk operation on the selected entries?');">
                <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
                <!-- Now we can render the completed list table -->
                <?php $payments_list_table->display(); ?>
            </form>

        </div></div><!-- end of poststuff and post-body -->
</div><!-- end of .wrap -->