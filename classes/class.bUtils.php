<?php

class BUtils {

    public static function calculate_subscription_period_days($subcript_period, $subscript_unit) {
        if (($subcript_period == 0) && !empty($subscript_unit)) {//will expire after a fixed date.
            return date(get_option('date_format'), strtotime($subscript_unit));
        }
        
        switch (strtolower($subscript_unit)) {
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
        return $subcript_period ;
    }
    public static function get_expiration_timestamp($user){
        $permission = BPermission::get_instance($user->membership_level);
        $days = self::calculate_subscription_period_days(
                $permission->get('subscription_period'), 
                $permission->get('subscription_unit'));
        if ($days == 'noexpire'){
            return PHP_INT_MAX; // which is equivalent to 
        }
        
        return strtotime($user->subscription_starts . ' ' . $days . ' days');
    }
    public static function gender_dropdown($selected = 'not specified') {
        return '<option ' . ((strtolower($selected) == 'male') ? 'selected="selected"' : "") . ' value="male">Male</option>' .
                '<option ' . ((strtolower($selected) == 'female') ? 'selected="selected"' : "") . ' value="female">Female</option>' .
                '<option ' . ((strtolower($selected) == 'not specified') ? 'selected="selected"' : "") . ' value="not specified">Not Specified</option>';
    }
    public static function account_state_dropdown($selected = 'active'){
        return '<option ' . ((strtolower($selected) == 'active') ? 'selected="selected"' : "") . '  value="active"> ' . BUtils::_('Active') . '</option>'
                . '<option ' . ((strtolower($selected) == 'inactive') ? 'selected="selected"' : "") . '  value="inactive"> ' . BUtils::_('Inactive') . '</option>'
                . '<option ' . ((strtolower($selected) == 'pending') ? 'selected="selected"' : "") . '  value="pending"> ' . BUtils::_('Pending') . '</option>'
                . '<option ' . ((strtolower($selected) == 'expired') ? 'selected="selected"' : "") . '  value="expired"> ' . BUtils::_('Expired') . '</option>';
    }
    public static function subscription_unit_dropdown($selected = 'days') {
        return '<option ' . ((strtolower($selected) == 'days') ? 'selected="selected"' : "") . ' value="days">Days</option>' .
                '<option ' . ((strtolower($selected) == 'weeks') ? 'selected="selected"' : "") . ' value="weeks">Weeks</option>' .
                '<option ' . ((strtolower($selected) == 'months') ? 'selected="selected"' : "") . ' value="months">Months</option>' .
                '<option ' . ((strtolower($selected) == 'years') ? 'selected="selected"' : "") . ' value="years">Years</option>';
    }
    public static function membership_level_dropdown($selected = 0){
        $options = '';
        global $wpdb;
        $query = "SELECT alias, id FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE id != 1";
        $levels = $wpdb->get_results($query);
        foreach($levels as $level){
            $options .= '<option '.($selected == $level->id ? 'select="selected"':'').' value="'.$level->id.'" >' . $level->alias . '</option>';
        }
        return $options;
    }

    public static function get_user_by_id($swpm_id) {
        global $wpdb;
        $query = $wpdb->prepare("SELECT user_name FROM {$wpdb->prefix}swpm_members_tbl WHERE member_id = %d", $swpm_id);
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
                $query = $wpdb->prepare("SELECT * FROM  {$wpdb->prefix}swpm_members_tbl WHERE member_id =  %d", $member_id);
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
            $wp_user_id = email_exists($wp_user_data['user_email']);
            if ($wp_user_id) {return $wp_user_id;}
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
    public static function _($msg){
        return __($msg, 'swpm');
    }
    public static function e($msg){
        _e($msg, 'swpm');
    }
    public static function is_admin(){
        return current_user_can('manage_options');
    }
    public static function get_expire_date($start_date, $subscription_duration, $duration_unit){
        if (($subscription_duration == 0) && !empty($duration_unit)) { //will expire after a fixed date.
            return date(get_option( 'date_format' ), strtotime($duration_unit));
        }
        $expires = self::calculate_subscription_period_days($subscription_duration, $duration_unit);
        if ($expires == 'noexpire') {// its set to no expiry until cancelled
            return BUtils::_('Never');
        }
        
        return date(get_option( 'date_format' ) ,
                strtotime($start_date . ' ' .  $expires . ' days'));
    }
    public static function swpm_username_exists($user_name) {
        global $wpdb;
        $member_table = $wpdb->prefix. 'swpm_members_tbl';
        $query = $wpdb->prepare('SELECT member_id FROM ' . $member_table  . 'WHERE user_name=%s', $user_name);

        return $wpdb->get_var($query);
    }
    public static function get_free_level(){
        $encrypted = filter_input(INPUT_POST, 'level_identifier');
        global $wpdb;
        if (!empty($encrypted)){
            return BPermission::get_instance($encrypted)->get('id');
        }
        
        $is_free = BSettings::get_instance()->get_value('enable-free-membership');
        $free_level = absint(BSettings::get_instance()->get_value('free-membership-id'));
        
        return ($is_free)? $free_level : null;
    }
    public static function is_paid_registration(){
        $member_id = filter_input(INPUT_GET, 'member_id', FILTER_SANITIZE_NUMBER_INT);
        $code = filter_input(INPUT_GET, 'code', FILTER_SANITIZE_STRING);        
        return !empty($member_id) && !empty($code);
    }
    public static function get_paid_member_info(){
        $member_id = filter_input(INPUT_GET, 'member_id', FILTER_SANITIZE_NUMBER_INT);
        $code = filter_input(INPUT_GET, 'code', FILTER_SANITIZE_STRING);
        global $wpdb;
        if (!empty($member_id) && !empty($code)){
            $query = 'SELECT * FROM ' . $wpdb->prefix . 'swpm_members_tbl WHERE member_id= %d AND reg_code=%s';
            $query = $wpdb->prepare($query, $member_id, $code);
            return $wpdb->get_row($query);
        }
        return null;
    }
}
