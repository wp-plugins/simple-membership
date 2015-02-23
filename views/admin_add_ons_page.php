<?php
$output = '';
echo '<link type="text/css" rel="stylesheet" href="' . SIMPLE_WP_MEMBERSHIP_URL . '/css/swpm.addons.listing.css" />' . "\n";
?>

<h1><?php echo  BUtils::_('Simple WP Membership::Add-ons') ?></h1>
<div class="wrap">

    <?php
    $addons_data = array();
    $addon_1 = array(
        'name' => 'After Login Redirection',
        'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/swpm-login-redirection.png',
        'description' => 'Allows you to configure after login redirection to a specific page based on the member\'s level',
        'page_url' => 'https://simple-membership-plugin.com/configure-login-redirection-members/',
    );
    array_push($addons_data, $addon_1);

    $addon_2 = array(
        'name' => 'MailChimp Integration',
        'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/mailchimp-integration.png',
        'description' => 'Allows you to signup the member to your MailChimp list after registration',
        'page_url' => 'https://simple-membership-plugin.com/signup-members-mailchimp-list/',
    );
    array_push($addons_data, $addon_2);
    
    $addon_3 = array(
        'name' => 'Form Shortcode',
        'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/form-shortcode-generator.png',
        'description' => 'Simple Membership Addon to generate form shortcode for specific membership level.',
        'page_url' => 'https://simple-membership-plugin.com/simple-membership-registration-form-shortcode-generator/',
    );
    array_push($addons_data, $addon_3);

    $addon_4 = array(
        'name' => 'WP User Import',
        'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/wp-user-import.png',
        'description' => 'Addon for importing existing Wordpress users to Simple Membership plugin',
        'page_url' => 'https://simple-membership-plugin.com/import-existing-wordpress-users-simple-membership-plugin/',
    );
    array_push($addons_data, $addon_4);

    $addon_5 = array(
        'name' => 'Form Builder',
        'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/swpm-form-builder.png',
        'description' => 'Allows you to fully customize the fields that appear on the registration and edit profile forms of your membership site',
        'page_url' => 'https://simple-membership-plugin.com/simple-membership-form-builder-addon/',
    );
    array_push($addons_data, $addon_5);
    
    $addon_6 = array(
        'name' => 'Custom Messages',
        'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/swpm-custom-messages.png',
        'description' => 'Custom Messages addon allows you to customize the content protection message that gets output from the membership plugin',
        'page_url' => 'https://simple-membership-plugin.com/simple-membership-custom-messages-addon/',
    );
    array_push($addons_data, $addon_6);
    
    $addon_7 = array(
        'name' => 'Protect Older Posts',
        'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/swpm-older-posts-protection.png',
        'description' => 'The protect older posts addon allows you to control protection of posts that were published before a member\'s access start date.',
        'page_url' => 'https://simple-membership-plugin.com/simple-membership-protect-older-posts-addon/',
    );
    array_push($addons_data, $addon_7);
    
    foreach ($addons_data as $addon) {
        $output .= '<div class="swpm_addon_item_canvas">';

        $output .= '<div class="swpm_addon_item_thumb">';
        $img_src = $addon['thumbnail'];
        $output .= '<img src="' . $img_src . '" alt="' . $addon['name'] . '">';
        $output .= '</div>'; //end thumbnail

        $output .='<div class="swpm_addon_item_body">';
        $output .='<div class="swpm_addon_item_name">';
        $output .= '<a href="' . $addon['page_url'] . '" target="_blank">' . $addon['name'] . '</a>';
        $output .='</div>'; //end name

        $output .='<div class="swpm_addon_item_description">';
        $output .= $addon['description'];
        $output .='</div>'; //end description

        $output .='<div class="swpm_addon_item_details_link">';
        $output .='<a href="'.$addon['page_url'].'" class="swpm_addon_view_details" target="_blank">View Details</a>';
        $output .='</div>'; //end detils link      
        $output .='</div>'; //end body

        $output .= '</div>'; //end canvas
    }

    echo $output;
    ?>

</div><!-- end of .wrap -->