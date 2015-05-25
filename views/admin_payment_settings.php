<?php screen_icon( 'options-general' );?>
<h1><?php echo  SwpmUtils::_('Simple WP Membership::Settings')?></h1>
<div class="wrap">

<?php do_action("swpm-draw-tab"); ?>

<div id="poststuff"><div id="post-body">

<?php
global $wpdb;

if(isset($_POST['swpm_generate_adv_code']))
{
	$paypal_ipn_url = SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL.'/?swpm_process_ipn=1';
    $mem_level = trim($_POST['swpm_paypal_adv_member_level']);
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE id !=1 AND id =%d", $mem_level);
    $membership_level_resultset = $wpdb->get_row($query);
    if($membership_level_resultset){
    	$pp_av_code = 'notify_url='.$paypal_ipn_url.'<br /> '.'custom=subsc_ref='.$mem_level;
        echo '<div id="message" class="updated fade"><p>';
        echo '<strong>Paste the code below in the "Add advanced variables" field of your PayPal button for membership level '.$mem_level.'</strong>';
		echo '<br /><br /><code>'.$pp_av_code.'</code>';
        echo '</p></div>';
    }
    else{
        echo '<div id="message" class="updated fade"><p><strong>';
        SwpmUtils::e( 'Error! The membership level ID ('.$mem_level.') you specified is incorrect. Please check this value again.');
        echo '</strong></p></div>';
    }
}
?>
	<div class="postbox">
	<h3><label for="title"><?php echo  SwpmUtils::_('PayPal Integration Settings')?></label></h3>
	<div class="inside">

	<p><strong><?php echo  SwpmUtils::_('Generate the "Advanced Variables" Code for your PayPal button')?></strong></p>

        <form action="" method="post">
        <?php echo  SwpmUtils::_('Enter the Membership Level ID')?>
        <input type="text" value="" size="4" name="swpm_paypal_adv_member_level">
        <input type="submit" value="<?php echo  SwpmUtils::_('Generate Code')?>" class="button-primary" name="swpm_generate_adv_code">
        </form>

	</div></div>

</div></div><!-- end of poststuff and post-body -->
</div><!-- end of wrap -->
