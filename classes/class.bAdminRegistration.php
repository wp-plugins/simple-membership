<?php

/**
 * Description of BAdminRegistration
 *
 */
class BAdminRegistration extends BRegistration {
    public static function get_instance(){
        self::$_intance = empty(self::$_intance)? new BAdminRegistration():self::$_intance;
        return self::$_intance;
    }
    public function show_form() {

    }

    public function register() {
        global $wpdb;
        $member = BTransfer::$default_fields;
        $form = new BForm($member);
        if ($form->is_valid()) {
            $member_info = $form->get_sanitized();
            $account_status = BSettings::get_instance()->get_value('default-account-status', 'active');
            $member_info['account_state'] = $account_status;
            $plain_password = $member_info['plain_password'];
            unset($member_info['plain_password']);
            $wpdb->insert($wpdb->prefix . "swpm_members_tbl", $member_info);
            /*             * ******************** register to wordpress ********** */
            $query = $wpdb->prepare("SELECT role FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE id = %d", $member_info['membership_level']) ;
            $wp_user_info = array();
            $wp_user_info['user_nicename'] = implode('-', explode(' ', $member_info['user_name']));
            $wp_user_info['display_name'] = $member_info['user_name'];
            $wp_user_info['user_email'] = $member_info['email'];
            $wp_user_info['nickname'] = $member_info['user_name'];
            if (isset($member_info['first_name'])){$wp_user_info['first_name'] = $member_info['first_name']; }
            if (isset($member_info['last_name'])){$wp_user_info['last_name'] = $member_info['last_name'];}
            $wp_user_info['user_login'] = $member_info['user_name'];
            $wp_user_info['password'] = $plain_password;
            $wp_user_info['role'] = $wpdb->get_var($query);
            $wp_user_info['user_registered'] = date('Y-m-d H:i:s');
            BUtils::create_wp_user($wp_user_info);
            /*             * ******************** register to wordpress ********** */
            $send_notification = BSettings::get_instance()->get_value('enable-notification-after-manual-user-add');
            $member_info['plain_password'] = $plain_password;
            $this->member_info = $member_info;
            if (!empty($send_notification)){
                $this->send_reg_email();
            }
            $message = array('succeeded' => true, 'message' => BUtils::_('Registration Successful.'));
            BTransfer::get_instance()->set('status', $message);
            wp_redirect('admin.php?page=simple_wp_membership'); 
            return;
        }
        $message = array('succeeded' => false, 'message' => BUtils::_('Please correct the following:'), 'extra' => $form->get_errors());
        BTransfer::get_instance()->set('status', $message);
    }
    public function edit($id){
        global $wpdb;
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "swpm_members_tbl WHERE member_id = %d", $id);
        $member = $wpdb->get_row($query, ARRAY_A);
        $email_address = $member['email'];
        $user_name = $member['user_name'];
        unset($member['member_id']);
        unset($member['user_name']);
        $form = new BForm($member);
        if ($form->is_valid()) {
            $member = $form->get_sanitized(); 
            BUtils::update_wp_user($user_name, $member);
            unset($member['plain_password']);
            $wpdb->update($wpdb->prefix . "swpm_members_tbl", $member, array('member_id' => $id));
            $message = array('succeeded' => true, 'message' => 'Updated Successfully.');
            do_action('swpm_admin_edit_custom_fields', $member + array('member_id'=>$id));
            BTransfer::get_instance()->set('status', $message);
            $send_notification = filter_input(INPUT_POST, 'account_status_change');
            if (!empty($send_notification)){
                $settings = BSettings::get_instance();
                $from_address = $settings->get_value('email-from');
                $headers = 'From: ' . $from_address . "\r\n";
                $subject = filter_input(INPUT_POST,'notificationmailhead');
                $body = filter_input(INPUT_POST, 'notificationmailbody');
                $settings->set_value('account-change-email-body', $body)->set_value('account-change-email-subject', $subject)->save();                
                $member['login_link'] = $settings->get_value('login-page-url');
                $values = array_values($member);
                $keys = array_map('swpm_enclose_var', array_keys($member));
                $body = str_replace($keys, $values, $body);                
                wp_mail($email_address, $subject, $body, $headers);                
            }
            wp_redirect('admin.php?page=simple_wp_membership');
        }               
        $message = array('succeeded' => false, 'message' => BUtils::_('Please correct the following:'), 'extra' => $form->get_errors());
        BTransfer::get_instance()->set('status', $message);
    }
}
