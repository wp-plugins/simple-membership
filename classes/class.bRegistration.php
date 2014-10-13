<?php

/**
 * Description of BRegistration
 *
 * @author nur
 */
abstract class BRegistration {
    protected $member_info = array();
    protected static $_intance = null;
    //public abstract static function get_instance();
    protected function send_reg_email(){
        global $wpdb;
        if (empty($this->member_info)) {return false;}
        $member_info = $this->member_info;
        $settings = BSettings::get_instance();
        $subject = $settings->get_value('reg-complete-mail-subject');
        $body = $settings->get_value('reg-complete-mail-body');
        $from_address = $settings->get_value('email-from');
        $login_link = $settings->get_value('login-page-url');
        $headers = 'From: ' . $from_address . "\r\n";
        $member_info['membership_level_name'] = BPermission::get_instance($member_info['membership_level'])->get('alias');
        $member_info['password'] = $member_info['plain_password'];
        $member_info['login_link'] = $login_link;
        $values = array_values($member_info);
        $keys = array_map('swpm_enclose_var', array_keys($member_info));
        $body = str_replace($keys, $values, $body);
        $email = sanitize_email(filter_input(INPUT_POST, 'email', FILTER_UNSAFE_RAW));
        wp_mail(trim($email), $subject, $body, $headers);
        if ($settings->get_value('enable-admin-notification-after-reg')) {
            $subject = "Notification of New Member Registration";
            $body = "A new member has registered. The following email was sent to the member." .
                    "\n\n-------Member Email----------\n" . $body .
                    "\n\n------End------\n";
            wp_mail($from_address, $subject, $body, $headers);
        }
        return true;
    }
}
function swpm_enclose_var($n){
    return '{'.$n .'}';
}
