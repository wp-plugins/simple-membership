<?php

class bUtils {
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
    public static function get_registration_link($for='all', $send_email=false, $member_id=''){
        $members = array();
        global $wpdb;
        switch ($for){
            case 'one':
                if(empty($member_id)){return array();}
                $query = "SELECT * FROM  {$wpdb->prefix}swpm_members_tbl WHERE member_id = $member_id ";
                $members = $wpdb->get_results($query);
                break;
            case 'all':
                $query = "SELECT * FROM  {$wpdb->prefix}swpm_members_tbl WHERE reg_code != '' ";
                $members = $wpdb->get_results($query);
                break;
        }
        $settings = BSettings::get_instance();
        $separator='?';
        $url = $settings->get_value('registration-page-url');
        if(strpos($url,'?')!==false){$separator='&';}
        $subject = $settings->get_value('reg-complete-mail-subject');
        if (empty($subject)){
            $subject = "Please complete your registration";
        }
        $body = $settings->get_value('reg-complete-mail-body');
        if (empty($body)){
            $body = "Please use the following link to complete your registration. \n {reg_link}";
        }
        $from_address = $settings->get_value('email-from');
        $links = array();
        foreach($members as $member){
            $reg_url = $url.$separator.'member_id='.$member->member_id.'&code='.$member->reg_code;
            if(!empty($send_email) && empty($member->user_name)){
                $tags = array("{first_name}","{last_name}","{reg_link}");
                $vals = array($member->first_name,$member->last_name,$reg_url);
                $email_body    = str_replace($tags,$vals,$body);
                $headers = 'From: '.$from_address . "\r\n";
                wp_mail($member->email,$subject,$email_body,$headers);
            }
            $links[] = $reg_url;
        }
        return $links;
    }
}
