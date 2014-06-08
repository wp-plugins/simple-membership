<?php

class BUtils {

    public static function calculate_subscription_period($subcript_period, $subscript_unit) {
        if (($subcript_period == 0) && !empty($subscript_unit)) //will expire after a fixed date.
            return date(get_option('date_format'), strtotime($subscript_unit));
        switch ($subscript_unit) {
            case 'Days':
                break;
            case 'Weeks':
                $subcript_period = $subcript_period * 7;
                break;
            case 'Months':
                $subcript_period = $subcript_period * 30;
                break;
            case 'Years':
                $subcript_period = $subcript_period * 365;
                break;
        }
        if ($subcript_period == 0)// its set to no expiry until cancelled
            return 'noexpire';
        return $subcript_period . ' ' . $subscript_unit;
    }

    public static function gender_dropdown($selected = 'not specified') {
        return '<option ' . ((strtolower($selected) == 'male') ? 'selected="selected"' : "") . ' value="male">Male</option>' .
                '<option ' . ((strtolower($selected) == 'female') ? 'selected="selected"' : "") . ' value="female">Female</option>' .
                '<option ' . ((strtolower($selected) == 'not specified') ? 'selected="selected"' : "") . ' value="not specified">Not Specified</option>';
    }

    public static function subscription_unit_dropdown($selected = 'days') {
        return '<option ' . ((strtolower($selected) == 'days') ? 'selected="selected"' : "") . ' value="days">Days</option>' .
                '<option ' . ((strtolower($selected) == 'weeks') ? 'selected="selected"' : "") . ' value="weeks">Weeks</option>' .
                '<option ' . ((strtolower($selected) == 'months') ? 'selected="selected"' : "") . ' value="months">Months</option>' .
                '<option ' . ((strtolower($selected) == 'years') ? 'selected="selected"' : "") . ' value="years">Years</option>';
    }

    public static function get_user_by_id($swpm_id) {
        global $wpdb;
        $query = "SELECT user_name FROM {$wpdb->prefix}swpm_members_tbl WHERE member_id = $swpm_id";
        return $wpdb->get_var($query);
    }

    public static function get_registration_link($for = 'all', $send_email = false, $member_id = '') {
        $members = array();
        global $wpdb;
        switch ($for) {
            case 'one':
                if (empty($member_id)) {
                    return array();
                }
                $query = "SELECT * FROM  {$wpdb->prefix}swpm_members_tbl WHERE member_id = $member_id ";
                $members = $wpdb->get_results($query);
                break;
            case 'all':
                $query = "SELECT * FROM  {$wpdb->prefix}swpm_members_tbl WHERE reg_code != '' ";
                $members = $wpdb->get_results($query);
                break;
        }
        $settings = BSettings::get_instance();
        $separator = '?';
        $url = $settings->get_value('registration-page-url');
        if (strpos($url, '?') !== false) {
            $separator = '&';
        }
        $subject = $settings->get_value('reg-complete-mail-subject');
        if (empty($subject)) {
            $subject = "Please complete your registration";
        }
        $body = $settings->get_value('reg-complete-mail-body');
        if (empty($body)) {
            $body = "Please use the following link to complete your registration. \n {reg_link}";
        }
        $from_address = $settings->get_value('email-from');
        $links = array();
        foreach ($members as $member) {
            $reg_url = $url . $separator . 'member_id=' . $member->member_id . '&code=' . $member->reg_code;
            if (!empty($send_email) && empty($member->user_name)) {
                $tags = array("{first_name}", "{last_name}", "{reg_link}");
                $vals = array($member->first_name, $member->last_name, $reg_url);
                $email_body = str_replace($tags, $vals, $body);
                $headers = 'From: ' . $from_address . "\r\n";
                wp_mail($member->email, $subject, $email_body, $headers);
            }
            $links[] = $reg_url;
        }
        return $links;
    }

    public static function update_wp_user_Role($wp_user_id, $role) {
        $preserve_role = 'yes';
        if ($preserve_role) {
            return;
        }
        if (self::is_multisite_install()) {//MS install
            return; //TODO - don't do this for MS install
        }
        $caps = get_user_meta($wp_user_id, 'wp_capabilities', true);
        if (in_array('administrator', array_keys((array) $caps))) {
            return;
        }
        do_action('set_user_role', $wp_user_id, $role); //Fire the action for other plugin(s)
        wp_update_user(array('ID' => $wp_user_id, 'role' => $role));
        $roles = new WP_Roles();
        $level = $roles->roles[$role]['capabilities'];
        if (isset($level['level_10']) && $level['level_10']) {
            update_user_meta($wp_user_id, 'wp_user_level', 10);
            return;
        }
        if (isset($level['level_9']) && $level['level_9']) {
            update_user_meta($wp_user_id, 'wp_user_level', 9);
            return;
        }
        if (isset($level['level_8']) && $level['level_8']) {
            update_user_meta($wp_user_id, 'wp_user_level', 8);
            return;
        }
        if (isset($level['level_7']) && $level['level_7']) {
            update_user_meta($wp_user_id, 'wp_user_level', 7);
            return;
        }
        if (isset($level['level_6']) && $level['level_6']) {
            update_user_meta($wp_user_id, 'wp_user_level', 6);
            return;
        }
        if (isset($level['level_5']) && $level['level_5']) {
            update_user_meta($wp_user_id, 'wp_user_level', 5);
            return;
        }
        if (isset($level['level_4']) && $level['level_4']) {
            update_user_meta($wp_user_id, 'wp_user_level', 4);
            return;
        }
        if (isset($level['level_3']) && $level['level_3']) {
            update_user_meta($wp_user_id, 'wp_user_level', 3);
            return;
        }
        if (isset($level['level_2']) && $level['level_2']) {
            update_user_meta($wp_user_id, 'wp_user_level', 2);
            return;
        }
        if (isset($level['level_1']) && $level['level_1']) {
            update_user_meta($wp_user_id, 'wp_user_level', 1);
            return;
        }
        if (isset($level['level_0']) && $level['level_0']) {
            update_user_meta($wp_user_id, 'wp_user_level', 0);
            return;
        }
    }
    public static function create_wp_user($wp_user_data) {
        if (self::is_multisite_install()) {//MS install
            global $blog_id;
            if ($wp_user_id = email_exists($wp_user_data['user_email'])) {// if user exists then just add him to current blog.
                add_existing_user_to_blog(array('user_id' => $wp_user_id, 'role' => 'subscriber'));
                return $wp_user_id;
            }
            $wp_user_id = wpmu_create_user($wp_user_data['user_login'], $wp_user_data['password'], $wp_user_data['user_email']);
            $role = 'subscriber'; //TODO - add user as a subscriber first. The subsequent update user role function to update the role to the correct one
            add_user_to_blog($blog_id, $wp_user_id, $role);
        } else {//Single site install
            $wp_user_id = wp_create_user($wp_user_data['user_login'], $wp_user_data['password'], $wp_user_data['user_email']);
        }
        $wp_user_data['ID'] = $wp_user_id;
        wp_update_user($wp_user_data);
        $user_info = get_userdata($wp_user_id);
        $user_cap = (isset($user_info->wp_capabilities) && is_array($user_info->wp_capabilities)) ? array_keys($user_info->wp_capabilities) : array();
        if (!in_array('administrator', $user_cap)){
            BUtils::update_wp_user_Role($wp_user_id, $wp_user_data['role']);
        }
        return $wp_user_id;
    }
    public static function is_multisite_install() {
        if (function_exists('is_multisite') && is_multisite()) {
            return true;
        } else {
            return false;
        }
    }
}
