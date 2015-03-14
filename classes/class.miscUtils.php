<?php

class miscUtils {

    public static function create_mandatory_wp_pages() {
        $settings = BSettings::get_instance();

        //Create join us page
        $swpm_join_page_content = '<p style="color:red;font-weight:bold;">This page and the content has been automatically generated for you to give you a basic idea of how a "Join Us" page should look like. You can customize this page however you like it by editing this page from your WordPress page editor.</p>';
        $swpm_join_page_content .= '<p style="font-weight:bold;">If you end up changing the URL of this page then make sure to update the URL value in the settings menu of the plugin.</p>';
        $swpm_join_page_content .= '<p style="border-top:1px solid #ccc;padding-top:10px;margin-top:10px;"></p>
			<strong>Free Membership</strong>
			<br />
			You get unlimited access to free membership content
			<br />
			<em><strong>Price: Free!</strong></em>
			<br /><br />Link the following image to go to the Registration Page if you want your visitors to be able to create a free membership account<br /><br />
			<img title="Join Now" src="' . SIMPLE_WP_MEMBERSHIP_URL . '/images/join-now-button-image.gif" alt="Join Now Button" width="277" height="82" />
			<p style="border-bottom:1px solid #ccc;padding-bottom:10px;margin-bottom:10px;"></p>';
        $swpm_join_page_content .= '<p><strong>You can register for a Free Membership or pay for one of the following membership options</strong></p>';
        $swpm_join_page_content .= '<p style="border-top:1px solid #ccc;padding-top:10px;margin-top:10px;"></p>
			[ ==> Insert Payment Button For Your Paid Membership Levels Here <== ]
			<p style="border-bottom:1px solid #ccc;padding-bottom:10px;margin-bottom:10px;"></p>';

        $swpm_join_page = array(
            'post_title' => 'Join Us',
            'post_name' => 'membership-join',
            'post_content' => $swpm_join_page_content,
            'post_parent' => 0,
            'post_status' => 'publish',
            'post_type' => 'page',
            'comment_status' => 'closed',
            'ping_status' => 'closed'
        );

        $join_page_obj = get_page_by_path('membership-join');
        if (!$join_page_obj) {
            $join_page_id = wp_insert_post($swpm_join_page);
        } else {
            $join_page_id = $join_page_obj->ID;
            if ($join_page_obj->post_status == 'trash') { //For cases where page may be in trash, bring it out of trash
                wp_update_post(array('ID' => $join_page_obj->ID, 'post_status' => 'publish'));
            }
        }
        $swpm_join_page_permalink = get_permalink($join_page_id);
        $settings->set_value('join-us-page-url', $swpm_join_page_permalink);

        //Create registration page
        $swpm_rego_page = array(
            'post_title' => BUtils::_('Registration'),
            'post_name' => 'membership-registration',
            'post_content' => '[swpm_registration_form]',
            'post_parent' => $join_page_id,
            'post_status' => 'publish',
            'post_type' => 'page',
            'comment_status' => 'closed',
            'ping_status' => 'closed'
        );
        $rego_page_obj = get_page_by_path('membership-registration');
        if (!$rego_page_obj) {
            $rego_page_id = wp_insert_post($swpm_rego_page);
        } else {
            $rego_page_id = $rego_page_obj->ID;
            if ($rego_page_obj->post_status == 'trash') { //For cases where page may be in trash, bring it out of trash
                wp_update_post(array('ID' => $rego_page_obj->ID, 'post_status' => 'publish'));
            }
        }
        $swpm_rego_page_permalink = get_permalink($rego_page_id);
        $settings->set_value('registration-page-url', $swpm_rego_page_permalink);

        //Create login page
        $swpm_login_page = array(
            'post_title' => BUtils::_('Member Login'),
            'post_name' => 'membership-login',
            'post_content' => '[swpm_login_form]',
            'post_parent' => 0,
            'post_status' => 'publish',
            'post_type' => 'page',
            'comment_status' => 'closed',
            'ping_status' => 'closed'
        );
        $login_page_obj = get_page_by_path('membership-login');
        if (!$login_page_obj) {
            $login_page_id = wp_insert_post($swpm_login_page);
        } else {
            $login_page_id = $login_page_obj->ID;
            if ($login_page_obj->post_status == 'trash') { //For cases where page may be in trash, bring it out of trash
                wp_update_post(array('ID' => $login_page_obj->ID, 'post_status' => 'publish'));
            }
        }
        $swpm_login_page_permalink = get_permalink($login_page_id);
        $settings->set_value('login-page-url', $swpm_login_page_permalink);

        //Create profile page
        $swpm_profile_page = array(
            'post_title' => BUtils::_('Profile'),
            'post_name' => 'membership-profile',
            'post_content' => '[swpm_profile_form]',
            'post_parent' => $login_page_id,
            'post_status' => 'publish',
            'post_type' => 'page',
            'comment_status' => 'closed',
            'ping_status' => 'closed'
        );
        $profile_page_obj = get_page_by_path('membership-profile');
        if (!$profile_page_obj) {
            $profile_page_id = wp_insert_post($swpm_profile_page);
        } else {
            $profile_page_id = $profile_page_obj->ID;
            if ($profile_page_obj->post_status == 'trash') { //For cases where page may be in trash, bring it out of trash
                wp_update_post(array('ID' => $profile_page_obj->ID, 'post_status' => 'publish'));
            }
        }
        $swpm_profile_page_permalink = get_permalink($profile_page_id);
        $settings->set_value('profile-page-url', $swpm_profile_page_permalink);

        //Create reset page
        $swpm_reset_page = array(
            'post_title' => BUtils::_('Password Reset'),
            'post_name' => 'password-reset',
            'post_content' => '[swpm_reset_form]',
            'post_parent' => $login_page_id,
            'post_status' => 'publish',
            'post_type' => 'page',
            'comment_status' => 'closed',
            'ping_status' => 'closed'
        );
        $reset_page_obj = get_page_by_path('password-reset');
        if (!$profile_page_obj) {
            $reset_page_id = wp_insert_post($swpm_reset_page);
        } else {
            $reset_page_id = $reset_page_obj->ID;
            if ($reset_page_obj->post_status == 'trash') { //For cases where page may be in trash, bring it out of trash
                wp_update_post(array('ID' => $reset_page_obj->ID, 'post_status' => 'publish'));
            }
        }
        $swpm_reset_page_permalink = get_permalink($reset_page_id);
        $settings->set_value('reset-page-url', $swpm_reset_page_permalink);

        $settings->save(); //Save all settings object changes
    }

    public static function reset_swmp_log_files() {
        $log_reset = true;
        $logfile_list = array(
            SIMPLE_WP_MEMBERSHIP_PATH.'/log.txt',
        );

        foreach ($logfile_list as $logfile) {
            if (empty($logfile)) {
                continue;
            }

            $text = '[' . date('m/d/Y g:i A') . '] - SUCCESS : Log file reset';
            $text .= "\n------------------------------------------------------------------\n\n";
            $fp = fopen($logfile, 'w');
            if ($fp != FALSE) {
                @fwrite($fp, $text);
                @fclose($fp);
            } else {
                $log_reset = false;
            }
        }
        return $log_reset;
    }

}