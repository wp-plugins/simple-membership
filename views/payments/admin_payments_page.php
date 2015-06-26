<?php
$output = '';

$tab = isset($_GET['tab']) ? $_GET['tab'] : '';
?>

<div class="wrap">

    <h2><?php echo SwpmUtils::_('Simple Membership::Payments') ?></h2>

    <div id="poststuff"><div id="post-body">

            <h2 class="nav-tab-wrapper">
                <a class="nav-tab <?php echo ($tab == '') ? 'nav-tab-active' : ''; ?>" href="admin.php?page=simple_wp_membership_payments">Transactions</a>
                <a class="nav-tab <?php echo ($tab == 'payment_buttons') ? 'nav-tab-active' : ''; ?>" href="admin.php?page=simple_wp_membership_payments&tab=payment_buttons">Manage Payment Buttons</a>
                <a class="nav-tab <?php echo ($tab == 'create_new_button') ? 'nav-tab-active' : ''; ?>" href="admin.php?page=simple_wp_membership_payments&tab=create_new_button">Create New Button</a>
                
                <?php
                if($tab == 'edit_button' ){//Only show the "edit button" tab when a button is being edited.
                    echo '<a class="nav-tab nav-tab-active" href="#">Edit Button</a>';
                }
                ?>                
            </h2>

            <?php
            switch ($tab) {
                case 'payment_buttons':
                    include_once(SIMPLE_WP_MEMBERSHIP_PATH . '/views/payments/admin_payment_buttons.php');
                    break;
                case 'create_new_button':
                    include_once(SIMPLE_WP_MEMBERSHIP_PATH . '/views/payments/admin_create_payment_buttons.php');
                    break;
                case 'edit_button':
                    include_once(SIMPLE_WP_MEMBERSHIP_PATH . '/views/payments/admin_edit_payment_buttons.php');
                    break;                    
                case 'all_txns':
                    include_once(SIMPLE_WP_MEMBERSHIP_PATH . '/views/payments/admin_all_payment_transactions.php');
                    break;
                default:
                    include_once(SIMPLE_WP_MEMBERSHIP_PATH . '/views/payments/admin_all_payment_transactions.php');
                    break;
            }
            ?>

        </div></div><!-- end of poststuff and post-body -->
</div><!-- end of .wrap -->