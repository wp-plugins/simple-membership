<?php

/**
 * Description of BRegistration
 *
 * @author nur
 */
abstract class SwpmRegistration {

    protected $member_info = array();
    protected static $_intance = null;

    //public abstract static function get_instance();
    protected function send_reg_email() {
        global $wpdb;
        if (empty($this->member_info)) {
            return false;
        }
        $member_info = $this->member_info;
        $settings = SwpmSettings::get_instance();
        $subject = $settings->get_value('reg-complete-mail-subject');
        $body = $settings->get_value('reg-complete-mail-body');
        $from_address = $settings->get_value('email-from');
        $login_link = $settings->get_value('login-page-url');
        $headers = 'From: ' . $from_address . "\r\n";
        $member_info['membership_level_name'] = SwpmPermission::get_instance($member_info['membership_level'])->get('alias');
        $member_info['password'] = $member_info['plain_password'];
        $member_info['login_link'] = $login_link;
        $values = array_values($member_info);
        $keys = array_map('swpm_enclose_var', array_keys($member_info));
        $body = str_replace($keys, $values, $body);
        $email = sanitize_email(filter_input(INPUT_POST, 'email', FILTER_UNSAFE_RAW));
        
        wp_mail(trim($email), $subject, $body, $headers);
        SwpmLog::log_simple_debug('Member notification email sent to: '.$email, true);
        
        if ($settings->get_value('enable-admin-notification-after-reg')) {
            $to_email_address = $settings->get_value('admin-notification-email');
            $headers = 'From: ' . $from_address . "\r\n";
            $subject = "Notification of New Member Registration";
            $body = "A new member has registered. The following email was sent to the member." .
                    "\n\n-------Member Email----------\n" . $body .
                    "\n\n------End------\n";
            $admin_notification = empty($to_email_address) ? $from_address : $to_email_address;
            wp_mail(trim($admin_notification), $subject, $body, $headers);
            SwpmLog::log_simple_debug('Admin notification email sent to: '.$admin_notification, true);
        }
        return true;
    }

}

function swpm_enclose_var($n) {
    return '{' . $n . '}';
}
