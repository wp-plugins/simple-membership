<div class="wrap">
    <h2><?php screen_icon('users'); ?><?php echo  SwpmUtils::_('Simple WP Membership::Categories') ?></h2>
    <?php include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_membership_level_menu.php'); ?>
    
    <div style="background: none repeat scroll 0 0 #FFF6D5;border: 1px solid #D1B655;color: #3F2502;margin: 10px 0;padding: 5px 5px 5px 10px;text-shadow: 1px 1px #FFFFFF;">
    <p>
        <?php echo  SwpmUtils::_('First of all, globally protect the category on your site by selecting "General Protection" from the drop-down box below and then select the categories that should be protected from non-logged in users.'); ?>
    </p>
    <p>
        <?php echo  SwpmUtils::_('Next, select an existing membership level from the drop-down box below and then select the categories you want to grant access to (for that particular membership level).'); ?>
    </p>
    </div>
    <form id="category_list_form" method="post">    
        <p class="swpm-select-box-left">
            <label for="membership_level_id">
                Membership Level:</label>
            
            <select id="membership_level_id" name="membership_level_id">
                <option <?php echo  $category_list->selected_level_id==1? "selected": "" ?> value="1">General Protection</option>
                <?php echo  SwpmUtils::membership_level_dropdown($category_list->selected_level_id); ?>
            </select>                
        </p>
        <p class="swpm-select-box-left"><input type="submit" class="button-primary" name="update_category_list" value="Update"></p>        
        <?php $category_list->prepare_items(); ?>   
        <?php $category_list->display(); ?>
    </form>
</div><!-- end of .wrap -->
<script type="text/javascript">
    jQuery(document).ready(function($){
        $('#membership_level_id').change(function(){
            $('#category_list_form').submit();
        });
    });
</script>
