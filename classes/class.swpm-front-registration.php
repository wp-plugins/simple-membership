<?php

/**
 * Description of BFrontRegistration
 *
 * @author nur
 */
class SwpmFrontRegistration extends SwpmRegistration {

    public static function get_instance() {
        self::$_intance = empty(self::$_intance) ? new SwpmFrontRegistration() : self::$_intance;
        return self::$_intance;
    }

    public function regigstration_ui($level) {
        $form = apply_filters('swpm_registration_form_override', '', $level);
        if (!empty($form)) {
            return $form;
        }

        $settings_configs = SwpmSettings::get_instance();
        $joinuspage_url = $settings_configs->get_value('join-us-page-url');
        $membership_level = '';
        global $wpdb;

        if (SwpmUtils::is_paid_registration()) {
            //Lets check if this is a registration for paid membership
            $member = SwpmUtils::get_paid_member_info();
            if (empty($member)) {
                SwpmUtils::e('Error! Invalid Request. Could not find a match for the given security code and the user ID.');
            } else {
                $membership_level = $member->membership_level;
            }
        } else if (!empty($level)) {
            $member = SwpmTransfer::$default_fields;
            $membership_level = absint($level);
        }
        if (empty($membership_level)) {
            $joinuspage_link = '<a href="' . $joinuspage_url . '">Join us</a>';
            echo '<p>';
            SwpmUtils::e('Free membership is disabled on this site. Please make a payment from the ' . $joinuspage_link . ' page to pay for a premium membership.');
            echo '</p><p>';
            SwpmUtils::e('You will receive a unique link via email after the payment. You will be able to use that link to complete the premium membership registration.');
            echo '</p>';
            return;
        }


        $mebership_info = SwpmPermission::get_instance($membership_level);
        $membership_level = $mebership_info->get('id');
        if (empty($membership_level)) {
            return "Membership Level Not Found.";
        }
        $level_identifier = md5($membership_level);
        $membership_level_alias = $mebership_info->get('alias');
        $swpm_registration_submit = filter_input(INPUT_POST, 'swpm_registration_submit');
        if (!empty($swpm_registration_submit)) {
            $member = $_POST;
        }
        ob_start();
        extract((array) $member, EXTR_SKIP);
        include(SIMPLE_WP_MEMBERSHIP_PATH . 'views/add.php');
        return ob_get_clean();
    }

    public function register() {
        //If captcha is present and validation failed, it returns an error string. If validation succeeds, it returns an empty string.
        $captcha_validation_output = apply_filters('swpm_validate_registration_form_submission', '');

        if (!empty($captcha_validation_output)) {
            $message = array('succeeded' => false, 'message' => SwpmUtils::_('Security check: captcha validation failed.'));
            SwpmTransfer::get_instance()->set('status', $message);
            return;
        }
        if ($this->create_swpm_user() && $this->create_wp_user() && $this->send_reg_email()) {
            do_action('swpm_front_end_registration_complete'); //Keep this action hook for people who are using it (so their implementation doesn't break).
            do_action('swpm_front_end_registration_complete_user_data', $this->member_info);

            $login_page_url = SwpmSettings::get_instance()->get_value('login-page-url');
            $after_rego_msg = '<div class="swpm-registration-success-msg">' . SwpmUtils::_('Registration Successful. ') . SwpmUtils::_('Please') . ' <a href="' . $login_page_url . '">' . SwpmUtils::_('Login') . '</a></div>';
            $message = array('succeeded' => true, 'message' => $after_rego_msg);
            SwpmTransfer::get_instance()->set('status', $message);
            return;
        }
    }

    private function create_swpm_user() {
        global $wpdb;
        $member = SwpmTransfer::$default_fields;
        $form = new SwpmFrontForm($member);
        if (!$form->is_valid()) {
            $message = array('succeeded' => false, 'message' => SwpmUtils::_('Please correct the following'),
                'extra' => $form->get_errors());
            SwpmTransfer::get_instance()->set('status', $message);
            return false;
        }


        $member_info = $form->get_sanitized();
        $free_level = SwpmUtils::get_free_level();
        $account_status = SwpmSettings::get_instance()->get_value('default-account-status', 'active');
        $member_info['last_accessed_from_ip'] = SwpmTransfer::get_real_ip_addr();
        $member_info['member_since'] = date("Y-m-d");
        $member_info['subscription_starts'] = date("Y-m-d");
        $member_info['account_state'] = $account_status;
        $plain_password = $member_info['plain_password'];
        unset($member_info['plain_password']);

        if (SwpmUtils::is_paid_registration()) {
            $member_info['reg_code'] = '';
            $member_id = filter_input(INPUT_GET, 'member_id', FILTER_SANITIZE_NUMBER_INT);
            $code = filter_input(INPUT_GET, 'code', FILTER_SANITIZE_STRING);
            $wpdb->update($wpdb->prefix . "swpm_members_tbl", $member_info, array('member_id' => $member_id, 'reg_code' => $code));

            $query = $wpdb->prepare('SELECT membership_level FROM ' . $wpdb->prefix . 'swpm_members_tbl WHERE member_id=%d', $member_id);
            $member_info['membership_level'] = $wpdb->get_var($query);
            $last_insert_id = $member_id;
        } else if (!empty($free_level)) {
            $member_info['membership_level'] = $free_level;
            $wpdb->insert($wpdb->prefix . "swpm_members_tbl", $member_info);
            $last_insert_id = $wpdb->insert_id;
        } else {
            $message = array('succeeded' => false, 'message' => SwpmUtils::_('Membership Level Couldn\'t be found.'));
            SwpmTransfer::get_instance()->set('status', $message);
            return false;
        }
        $member_info['plain_password'] = $plain_password;
        $this->member_info = $member_info;
        return true;
    }

    private function create_wp_user() {
        global $wpdb;
        $member_info = $this->member_info;
        $query = $wpdb->prepare("SELECT role FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE id = %d", $member_info['membership_level']);
        $wp_user_info = array();
        $wp_user_info['user_nicename'] = implode('-', explode(' ', $member_info['user_name']));
        $wp_user_info['display_name'] = $member_info['user_name'];
        $wp_user_info['user_email'] = $member_info['email'];
        $wp_user_info['nickname'] = $member_info['user_name'];
        $wp_user_info['first_name'] = $member_info['first_name'];
        $wp_user_info['last_name'] = $member_info['last_name'];
        $wp_user_info['user_login'] = $member_info['user_name'];
        $wp_user_info['password'] = $member_info['plain_password'];
        $wp_user_info['role'] = $wpdb->get_var($query);
        $wp_user_info['user_registered'] = date('Y-m-d H:i:s');
        SwpmUtils::create_wp_user($wp_user_info);
        return true;
    }

    public function edit() {
        global $wpdb;
        $auth = SwpmAuth::get_instance();
        if (!$auth->is_logged_in()) {
            return;
        }
        $user_data = (array) $auth->userData;
        unset($user_data['permitted']);
        $form = new SwpmForm($user_data);
        if ($form->is_valid()) {
            global $wpdb;
            $message = array('succeeded' => true, 'message' => SwpmUtils::_('Profile updated successfully.'));

            $member_info = $form->get_sanitized();
            SwpmUtils::update_wp_user($auth->get('user_name'), $member_info); //Update corresponding wp user record.


            if (isset($member_info['plain_password'])) {
                //Password was also changed so show the appropriate message
                $message = array('succeeded' => true, 'message' => SwpmUtils::_('Profile updated successfully. You will need to re-login since you changed your password.'));
                unset($member_info['plain_password']);
            }

            $wpdb->update($wpdb->prefix . "swpm_members_tbl", $member_info, array('member_id' => $auth->get('member_id')));
            $auth->reload_user_data();

            SwpmTransfer::get_instance()->set('status', $message);
        } else {
            $message = array('succeeded' => false, 'message' => SwpmUtils::_('Please correct the following'),
                'extra' => $form->get_errors());
            SwpmTransfer::get_instance()->set('status', $message);
            return;
        }
    }

    public function reset_password($email) {
        $email = sanitize_email($email);
        if (!is_email($email)) {
            $message = '<div class="swpm-reset-pw-error">' . SwpmUtils::_("Email address not valid.") . '</div>';
            $message = array('succeeded' => false, 'message' => $message);
            SwpmTransfer::get_instance()->set('status', $message);
            return;
        }
        global $wpdb;
        $query = 'SELECT member_id,user_name,first_name, last_name FROM ' .
                $wpdb->prefix . 'swpm_members_tbl ' .
                ' WHERE email = %s';
        $user = $wpdb->get_row($wpdb->prepare($query, $email));
        if (empty($user)) {
            $message = '<div class="swpm-reset-pw-error">' . SwpmUtils::_("No user not found with that email address.") . '</div>';
            $message .= '<div class="swpm-reset-pw-error-email">' . SwpmUtils::_("Email Address: ") . $email . '</div>';
            $message = array('succeeded' => false, 'message' => $message);
            SwpmTransfer::get_instance()->set('status', $message);
            return;
        }
        $settings = SwpmSettings::get_instance();
        $password = wp_generate_password();

        $password_hash = SwpmUtils::encrypt_password(trim($password)); //should use $saned??;
        $wpdb->update($wpdb->prefix . "swpm_members_tbl", array('password' => $password_hash), array('member_id' => $user->member_id));

        // update wp user pass.
        SwpmUtils::update_wp_user($user->user_name, array('plain_password' => $password));

        $body = $settings->get_value('reset-mail-body');
        $subject = $settings->get_value('reset-mail-subject');
        $search = array('{user_name}', '{first_name}', '{last_name}', '{password}');
        $replace = array($user->user_name, $user->first_name, $user->last_name, $password);
        $body = str_replace($search, $replace, $body);
        $from = $settings->get_value('email-from');
        $headers = "From: " . $from . "\r\n";
        wp_mail($email, $subject, $body, $headers);
        $message = '<div class="swpm-reset-pw-success">' . SwpmUtils::_("New password has been sent to your email address.") . '</div>';
        $message .= '<div class="swpm-reset-pw-success-email">' . SwpmUtils::_("Email Address: ") . $email . '</div>';

        $message = array('succeeded' => false, 'message' => $message);
        SwpmTransfer::get_instance()->set('status', $message);
    }

}
