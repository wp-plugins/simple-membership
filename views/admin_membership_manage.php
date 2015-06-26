<div class="wrap">
<h2>Simple WP Membership::Manage Protection</h2>
    <?php include_once(SIMPLE_WP_MEMBERSHIP_PATH.'views/admin_membership_level_menu.php');?>

<div id="poststuff"><div id="post-body">
<h1>How to Apply Content Protection</h1>

<p>Take the following steps to apply protection your content so only members can have access to it.</p>

1. Edit the Post or Page that you want to protect in WordPress editor.
<br />2. Scroll down to the section titled 'Simple WP Membership Protection'.
<br />3. Select 'Yes, Protect this content' option.
<br />4. Check the membership levels that should have access to that page's content.
<br />5. Hit the Update/Save Button to save the changes.

<br /><br />
<h3><?php echo  SwpmUtils::_('Example Content Protection Settings')?></h3>

<img src="<?php echo SIMPLE_WP_MEMBERSHIP_URL.'/images/simple-membership-content-protection-usage.png'; ?>" alt="Content protection example usage">

</div></div>
</div> <!-- end of wrap -->