<?php

/**
 * Description of BAdminRegistration
 *
 * @author nur
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
            $member_info['account_state'] = 'active';
            $plain_password = $member_info['plain_password'];
            unset($member_info['plain_password']);
            $wpdb->insert($wpdb->prefix . "swpm_members_tbl", $member_info);
            /*             * ******************** register to wordpress ********** */
            $query = "SELECT role FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE id = " . $member_info['membership_level'];
            $wp_user_info = array();
            $wp_user_info['user_nicename'] = implode('-', explode(' ', $member_info['user_name']));
            $wp_user_info['display_name'] = $member_info['user_name'];
            $wp_user_info['user_email'] = $member_info['email'];
            $wp_user_info['nickname'] = $member_info['user_name'];
            $wp_user_info['first_name'] = $member_info['first_name'];
            $wp_user_info['last_name'] = $member_info['last_name'];
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
        $query = "SELECT * FROM " . $wpdb->prefix . "swpm_members_tbl WHERE member_id = $id";
        $member = $wpdb->get_row($query, ARRAY_A);
        unset($member['member_id']);
        unset($member['email']);
        unset($member['user_name']);
        $form = new BForm($member);
        if ($form->is_valid()) {
            $member = $form->get_sanitized();
            unset($member['plain_password']);
            $wpdb->update($wpdb->prefix . "swpm_members_tbl", $member, array('member_id' => $id));
            $message = array('succeeded' => true, 'message' => 'Updated Successfully.');
            BTransfer::get_instance()->set('status', $message);
            wp_redirect('admin.php?page=simple_wp_membership');
        }
        $message = array('succeeded' => false, 'message' => BUtils::_('Please correct the following:'), 'extra' => $form->get_errors());
        BTransfer::get_instance()->set('status', $message);
    }
}
