<?php

include_once('class.bUtils.php');
include_once('class.miscUtils.php');
include_once('class.bSettings.php');
include_once('class.bProtection.php');
include_once('class.bPermission.php');
include_once('class.bAuth.php');
include_once('class.bAccessControl.php');
include_once('class.bForm.php');
include_once('class.bTransfer.php');
include_once('class.bFrontForm.php');
include_once('class.bLevelForm.php');
include_once('class.bMembershipLevels.php');
include_once('class.bLog.php');

class SimpleWpMembership {

    private $settings;
    private $lastMessage;

    public function __construct() {
        BAuth::get_instance();
        add_action('admin_menu', array(&$this, 'menu'));
        add_action('admin_init', array(&$this, 'admin_init')); //This call has been moved inside 'init' function
        add_action('init', array(&$this, 'init'));
        add_filter('the_content', array(&$this, 'filter_content'));
        //add_filter( 'the_content_more_link', array(&$this, 'filter_moretag'), 10, 2 );
        add_filter('comment_text', array(&$this, 'filter_comment'));
        add_action('save_post', array(&$this, 'save_postdata'));
        add_shortcode("swpm_registration_form", array(&$this, 'registration_form'));
        add_shortcode('swpm_profile_form', array(&$this, 'profile_form'));
        add_shortcode('swpm_login_form', array(&$this, 'login'));
        add_shortcode('swpm_reset_form', array(&$this, 'reset'));
        add_action('admin_notices', array(&$this, 'notices'));
        add_action('wp_enqueue_scripts', array(&$this, 'front_library'));
        add_action('load-toplevel_page_simple_wp_membership', array(&$this, 'admin_library'));
        add_action('load-wp-membership_page_simple_wp_membership_levels', array(&$this, 'admin_library'));
        add_action('wp_ajax_swpm_validate_email', array(&$this, 'validate_email_ajax'));
        add_action('wp_ajax_nopriv_swpm_validate_email', array(&$this, 'validate_email_ajax'));
        add_action('wp_ajax_swpm_validate_user_name', array(&$this, 'validate_user_name_ajax'));
        add_action('wp_ajax_nopriv_swpm_validate_user_name', array(&$this, 'validate_user_name_ajax'));
        add_action('profile_update', array(&$this, 'sync_with_wp_profile'), 10, 2);
        add_action('wp_logout', array(&$this, 'wp_logout'));
        add_action('wp_authenticate', array(&$this, 'wp_login'), 1, 2);
        add_action('swpm_logout', array(&$this, 'swpm_logout'));
    }
    public function shutdown(){
        bLog::writeall();
    }
    public static function swpm_login($user, $pass, $rememberme = true) {
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            if ($current_user->user_login == $user)
                return;
        }
        wp_signon(array('user_login' => $user, 'user_password' => $pass, 'remember' => $rememberme), is_ssl() ? true : false);
        wp_redirect(site_url());
    }

    public function swpm_logout() {
        if (is_user_logged_in()) {
            wp_logout();
            wp_set_current_user(0);
        }
    }

    public function wp_login($username, $password) {
        $auth = BAuth::get_instance();
        if ($auth->is_logged_in()) {
            if ($auth->userData->user_name == $username)
                return;
        }
        $auth->login($username, $password, true);
    }

    public function wp_logout() {
        $auth = BAuth::get_instance();
        if ($auth->is_logged_in())
            $auth->logout();
    }

    public function sync_with_wp_profile($wp_user_id) {
        global $wpdb;
        $wp_user_data = get_userdata($wp_user_id);
        $query = "SELECT * FROM " . $wpdb->prefix . "swpm_members_tbl WHERE " . ' user_name=\'' . $wp_user_data->user_login . '\'';
        $profile = $wpdb->get_row($query, ARRAY_A);
        $profile = (array) $profile;
        if (empty($profile))
            return;
        $profile['user_name'] = $wp_user_data->user_login;
        $profile['email'] = $wp_user_data->user_email;
        $profile['password'] = $wp_user_data->user_pass;
        $profile['first_name'] = $wp_user_data->user_firstname;
        $profile['last_name'] = $wp_user_data->user_lastname;
        $wpdb->update($wpdb->prefix . "swpm_members_tbl", $profile, array('member_id' => $profile['member_id']));
    }

    public function login() {
        $auth = BAuth::get_instance();
        if ($auth->is_logged_in())
            include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/loggedin.php');
        else {
            $setting = BSettings::get_instance();
            $password_reset_url = $setting->get_value('reset-page-url');
            $join_url = $setting->get_value('join-us-page-url');
            include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/login.php');
        }
    }

    public function reset() {
        $message = get_transient('swpm-password-reset');
        if (empty($message))
            include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/forgot_password.php');
        else
            echo $message;
    }

    public function validate_email_ajax() {
        global $wpdb;
        $table = $wpdb->prefix . "swpm_members_tbl";
        $email = esc_sql(trim($_GET['fieldValue']));
        $query = $wpdb->prepare("SELECT COUNT(*) FROM $table WHERE email = %s", $email);
        $exists = $wpdb->get_var($query) > 0;
        echo '[ "' . $_GET['fieldId'] . (($exists) ? '",false, "&chi;&nbsp;Aready taken"]' : '",true, "&radic;&nbsp;Available"]');
        exit;
    }

    public function validate_user_name_ajax() {
        global $wpdb;
        $table = $wpdb->prefix . "swpm_members_tbl";
        $user = esc_sql(trim($_GET['fieldValue']));
        $query = $wpdb->prepare("SELECT COUNT(*) FROM $table WHERE user_name = %s", $user);
        $exists = $wpdb->get_var($query) > 0;
        echo '[ "' . $_GET['fieldId'] . (($exists) ? '",false,"&chi;&nbsp;Aready taken"]' : '",true,"&radic;&nbsp;Available"]');
        exit;
    }

    public function profile_form() {
        $auth = BAuth::get_instance();
        $this->notices();
        if ($auth->is_logged_in()) {
            $user_data = (array) $auth->userData;
            $user_data['membership_level_alias'] = $auth->userData->permitted->get('alias');
            extract($user_data, EXTR_SKIP);
            include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/edit.php');
            return;
        }
        echo 'You are not logged in.';
    }

    public function notices() {
        $message = BTransfer::get_instance()->get('status');
        $succeeded = false;
        if (!empty($message)) {
            if ($message['succeeded']) {
                echo "<div id='message' class='updated'>";
                $succeeded = true;
            } else
                echo "<div id='message' class='error'>";
            echo $message['message'];
            $extra = isset($message['extra']) ? $message['extra'] : array();
            if (!empty($extra)) {
                echo '<ul>';
                foreach ($extra as $key => $value)
                    echo '<li>' . $value . '</li>';
                echo '</ul>';
            }
            echo "</div>";
        }
        return $succeeded;
    }

    public function meta_box() {
        if (function_exists('add_meta_box')) {
            $post_types = get_post_types();
            foreach ($post_types as $post_type => $post_type)
                add_meta_box('eMember_sectionid', __('Simple WP Membership Protection', 'eMember_textdomain'), array(&$this, 'inner_custom_box'), $post_type, 'advanced');
        } else {//older version doesn't have custom post type so modification isn't needed.
            add_action('dbx_post_advanced', array(&$this, 'show_old_custom_box'));
            add_action('dbx_page_advanced', array(&$this, 'show_old_custom_box'));
        }
    }

    public function show_old_custom_box() {
        echo '<div class="dbx-b-ox-wrapper">' . "\n";
        echo '<fieldset id="eMember_fieldsetid" class="dbx-box">' . "\n";
        echo '<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">' .
        __('eMember Protection options', 'eMember_textdomain') . "</h3></div>";
        echo '<div class="dbx-c-ontent-wrapper"><div class="dbx-content">';
        // output editing form
        $this->inner_custom_box();
        // end wrapper
        echo "</div></div></fieldset></div>\n";
    }

    public function inner_custom_box() {
        global $post, $wpdb;
        $id = $post->ID;
        // Use nonce for verification
        $is_protected = BProtection::get_instance()->is_protected($id);
        echo '<input type="hidden" name="swpm_noncename" id="swpm_noncename" value="' .
        wp_create_nonce(plugin_basename(__FILE__)) . '" />';
        // The actual fields for data entry
        echo '<h4>' . __("Do you want to protect this content?", 'eMember_textdomain') . '</h4>';
        echo '<input type="radio" ' . ((!$is_protected) ? 'checked' : "") . '  name="swpm_protect_post" value="1" /> No, Do not protect this content. <br/>';
        echo '<input type="radio" ' . (($is_protected) ? 'checked' : "") . '  name="swpm_protect_post" value="2" /> Yes, Protect this content.<br/>';
        echo '<h4>' . __("Select the membership level that can access this content:", 'eMember_textdomain') . "</h4>";
        $query = "SELECT * FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE  id !=1 ";
        $levels = $wpdb->get_results($query, ARRAY_A);
        foreach ($levels as $level) {
            echo '<input type="checkbox" ' . (BPermission::get_instance($level['id'])->is_permitted($id) ? "checked='checked'" : "") . ' name="swpm_protection_level[' . $level['id'] . ']" value="' . $level['id'] . '" /> ' . $level['alias'] . "<br/>";
        }
    }

    public function save_postdata($post_id) {
        global $wpdb;
        if (wp_is_post_revision($post_id))
            return;
        if (isset($_POST['swpm_noncename']) && !wp_verify_nonce($_POST['swpm_noncename'], plugin_basename(__FILE__)))
            return $post_id;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return $post_id;
        if (isset($_POST['post_type']) && ('page' == $_POST['post_type'] )) {
            if (!current_user_can('edit_page', $post_id))
                return $post_id;
        } else {
            if (!current_user_can('edit_post', $post_id))
                return $post_id;
        }
        if (!isset($_POST['swpm_protect_post']))
            return;
        // OK, we're authenticated: we need to find and save the data
        $isprotected = ($_POST['swpm_protect_post'] == 2);
        $protected = BProtection::get_instance();
        if (isset($_POST['post_type'])) {
            BProtection::get_instance()->update_perms($post_id, $isprotected, $_POST['post_type'])->save();
            $query = "SELECT id FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE  id !=1 ";
            $level_ids = $wpdb->get_col($query);
            foreach ($level_ids as $level)
                BPermission::get_instance($level)->update_perms($post_id, isset($_POST['swpm_protection_level'][$level]), $_POST['post_type'])->save();
        }
        $enable_protection = array();
        $enable_protection['protect'] = $_POST['swpm_protect_post'];
        $enable_protection['level'] = isset($_POST['swpm_protection_level']) ? $_POST['swpm_protection_level'] : "";
        return $enable_protection;
    }

    public function filter_comment($content) {
        $acl = BAccessControl::get_instance();
        global $comment;
        return $acl->filter_comment($comment->comment_ID, $content);
    }

    public function filter_content($content) {
        $acl = BAccessControl::get_instance();
        global $post;
        return $acl->filter_post($post->ID, $content);
    }

    public function filter_moretag($more_link, $more_link_text = "More") {
        $acl = BAccessControl::get_instance();
        global $post;
        //return $acl->filter_post_with_moretag($post->post_ID, $);
    }

    public function admin_init() {
        global $wpdb;
        include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'classes/class.bSettings.php');
        BSettings::get_instance();
        if (isset($_POST['createswpmuser'])) {
            $member = BTransfer::$default_fields;
            $form = new BForm($member);
            if ($form->is_valid()) {
                $member_info = $form->get_sanitized();
                $member_info['account_state'] = 'active';
                $plain_password = $member_info['plain_password'];
                unset($member_info['plain_password']);
                $wpdb->insert($wpdb->prefix . "swpm_members_tbl", $member_info);
                /*                 * ******************** register to wordpress ********** */
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
                self::create_wp_user($wp_user_info);
                /*                 * ******************** register to wordpress ********** */
                $message = array('succeeded' => true, 'message' => 'Registration Successful.');
                BTransfer::get_instance()->set('status', $message);
                wp_redirect('admin.php?page=simple_wp_membership');
                return;
            }
            $message = array('succeeded' => false, 'message' => 'Please correct the following:', 'extra' => $form->get_errors());
            BTransfer::get_instance()->set('status', $message);
        }
        if (isset($_POST["editswpmuser"])) {
            $id = absint($_REQUEST['member_id']);
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
            $message = array('succeeded' => false, 'message' => 'Please correct the following:', 'extra' => $form->get_errors());
            BTransfer::get_instance()->set('status', $message);
        }

        if (isset($_POST['createswpmlevel'])) {
            $level = BTransfer::$default_level_fields;
            $form = new BLevelForm($level);
            if ($form->is_valid()) {
                $level_info = $form->get_sanitized();
                $wpdb->insert($wpdb->prefix . "swpm_membership_tbl", $level_info);
                $message = array('succeeded' => true, 'message' => 'Membership Level Creation Successful.');
                BTransfer::get_instance()->set('status', $message);
                wp_redirect('admin.php?page=simple_wp_membership_levels');
                return;
            }
            $message = array('succeeded' => false, 'message' => 'Please correct the following:', 'extra' => $form->get_errors());
            BTransfer::get_instance()->set('status', $message);
        }
        if (isset($_POST["editswpmlevel"])) {
            $id = absint($_REQUEST['id']);
            $query = "SELECT * FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE id = $id";
            $level = $wpdb->get_row($query, ARRAY_A);
            $form = new BLevelForm($level);
            if ($form->is_valid()) {
                $wpdb->update($wpdb->prefix . "swpm_membership_tbl", $form->get_sanitized(), array('id' => $id));
                $message = array('succeeded' => true, 'message' => 'Updated Successfully.');
                BTransfer::get_instance()->set('status', $message);
                wp_redirect('admin.php?page=simple_wp_membership_levels');
            }
            $message = array('succeeded' => false, 'message' => 'Please correct the following:', 'extra' => $form->get_errors());
            BTransfer::get_instance()->set('status', $message);
        }
    }

    public function init() {
        if (isset($_GET['swpm-logout'])) {
            BAuth::get_instance()->logout();
            wp_redirect(site_url());
        } else if (!is_admin()) {
            BAuth::get_instance();
        }

        $widget_options = array('classname' => 'swpm_widget', 'description' => __("Display SWPM Login."));
        wp_register_sidebar_widget('swpm_login_widget', __('SWPM Login'), 'SimpleWpMembership::login_widget', $widget_options);
        $this->process_password_reset();
        $this->register_member();
        $this->edit_profile();
        $this->swpm_ipn_listener();
    }

    public function swpm_ipn_listener() {
        if (isset($_REQUEST['swpm_process_ipn']) && $_REQUEST['swpm_process_ipn'] == '1') {
            include_once(SIMPLE_WP_MEMBERSHIP_PATH.'ipn/swpm_handle_pp_ipn.php');
            exit;
        }
    }

    public function process_password_reset() {
        $message = "";
        if (isset($_POST['swpm-reset'])) {
            if (is_email($_POST['swpm_reset_email']))
                $message = "Email Address Not Valid.";
            else {
                global $wpdb;
                $query = "SELECT member_id,user_name,first_name, last_name FROM " .
                        $wpdb->prefix . "swpm_members_tbl " .
                        " WHERE email = '" . $_POST['swpm_reset_email'] . "'";
                $user = $wpdb->get_results($query);
                if (empty($user))
                    $message = "User Not Found.";
                else {
                    $settings = BSettings::get_instance();
                    $password = wp_generate_password();
                    $body = $settings->get_value('reset-mail-body');
                    $subject = $settings->get_value('reset-mail-subject');
                    $wpdb->update($wpdb->prefix . "swpm_members_tbl", array('password' => $password), array('member_id' => $user->member_id));
                    $search = array('{user_name}', '{first_name}', '{last_name}', '{password}');
                    $replace = array($user->user_name, $user->first_name, $user->last_name, $password);
                    $body = str_replace($search, $replace, $body);
                    $from = $settings->get_value('email-from');
                    $headers = "From: " . $from . "\r\n";
                    wp_mail($from, $subject, $body, $headers);
                    $message = "New password has been sent to your email address.";
                }
            }
            set_trasient('swpm-password-reset', $message, 10);
        }
    }

    public static function login_widget($args) {
        extract($args);
        $auth = BAuth::get_instance();
        $widget_title = "User Login";
        echo $before_widget;
        echo $before_title . $widget_title . $after_title;
        if ($auth->is_logged_in())
            include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/login_widget_logged.php');
        else
            include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/login_widget.php');
        echo $after_widget;
    }

    private function edit_profile() {
        $auth = BAuth::get_instance();
        if (isset($_POST['swpm_editprofile_submit'])) {
            if ($auth->is_logged_in()) {
                $user_data = (array) $auth->userData;
                unset($user_data['permitted']);
                $form = new BForm($user_data);
                if ($form->is_valid()) {
                    global $wpdb;
                    $member_info = $form->get_sanitized();
                    if (isset($member_info['plain_password'])) {
                        unset($member_info['plain_password']);
                    }
                    $wpdb->update(
                            $wpdb->prefix . "swpm_members_tbl", $member_info, array('member_id' => $auth->get('member_id')));
                    $message = array('succeeded' => true, 'message' => 'Profile Updated.');
                    BTransfer::get_instance()->set('status', $message);
                } else {
                    $message = array('succeeded' => false, 'message' => 'Please correct the following', 'extra' => $form->get_errors());
                    BTransfer::get_instance()->set('status', $message);
                    return;
                }
            }
            //todo: do a redirect
        }
    }

    public function admin_library() {
        $this->common_library();
        wp_enqueue_script('password-strength-meter');
        wp_enqueue_script('swpm.password-meter', SIMPLE_WP_MEMBERSHIP_URL . '/js/swpm.password-meter.js');
    }

    public function front_library() {
        $this->common_library();
    }

    private function common_library() {
        wp_enqueue_script('jquery');
        wp_enqueue_style('swpm.common', SIMPLE_WP_MEMBERSHIP_URL . '/css/swpm.common.css');
        wp_enqueue_style('validationEngine.jquery', SIMPLE_WP_MEMBERSHIP_URL . '/css/validationEngine.jquery.css');
        wp_enqueue_style('jquery.tools.dateinput', SIMPLE_WP_MEMBERSHIP_URL . '/css/jquery.tools.dateinput.css');
        wp_enqueue_script('jquery.tools', SIMPLE_WP_MEMBERSHIP_URL . '/js/jquery.tools18.min.js');
        wp_enqueue_script('jquery.validationEngine-en', SIMPLE_WP_MEMBERSHIP_URL . '/js/jquery.validationEngine-en.js');
        wp_enqueue_script('jquery.validationEngine', SIMPLE_WP_MEMBERSHIP_URL . '/js/jquery.validationEngine.js');
    }

    public function registration_form() {
        $settings_configs = BSettings::get_instance();
        $is_free = BSettings::get_instance()->get_value('enable-free-membership');
        $free_level = absint(BSettings::get_instance()->get_value('free-membership-id'));
        $joinuspage_url = $settings_configs->get_value('join-us-page-url');
        $membership_level = '';
        $member_id = filter_input(INPUT_GET, 'member_id', FILTER_SANITIZE_NUMBER_INT);
        $code      = filter_input(INPUT_GET, 'code', FILTER_SANITIZE_STRING);
        $member = BTransfer::$default_fields;
        global $wpdb;
        if (!empty($member_id) && !empty($code)){
            $query = 'SELECT * FROM ' . $wpdb->prefix . 'swpm_members_tbl WHERE member_id= %d AND reg_code=%s';
            $query = $wpdb->prepare($query, $member_id, $code);
            $member = $wpdb->get_row($query);
            if (empty($member)){
                return 'Invalid Request';
            }
            $membership_level = $member->membership_level;
        }
        else if (isset($_SESSION['swpm-registered-level'])) {
            $membership_level = absint($_SESSION['swpm-registered-level']);
        } else if ($is_free) {
            $membership_level = $free_level;
        }
        if (empty($membership_level)) {
            $joinuspage_link = '<a href="' . $joinuspage_url . '">Join us</a>';
            $output = 'Free membership is disabled on this site. Please make a payment from the ' . $joinuspage_link . ' page to pay for a premium membership.';
            return $output;
        }

        $query = "SELECT alias FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE id = $membership_level";
        $result = $wpdb->get_row($query);
        if (empty($result)) {
            return "Membership Level Not Found.";
        }
        $succeeded = $this->notices();
        $membership_level_alias = $result->alias;
        if (isset($_POST['swpm_registration_submit']))
            $member = $_POST;

        extract((array)$member, EXTR_SKIP);
        if (!$succeeded)
            include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/add.php');
    }

    private function register_member() {
        global $wpdb;
        if (isset($_POST['swpm_registration_submit'])) {
            $member = BTransfer::$default_fields;
            $form = new BFrontForm($member);
            $is_free = BSettings::get_instance()->get_value('enable-free-membership');
            $free_level = absint(BSettings::get_instance()->get_value('free-membership-id'));
            $member_id = filter_input(INPUT_GET, 'member_id', FILTER_SANITIZE_NUMBER_INT);
            $code      = filter_input(INPUT_GET, 'code', FILTER_SANITIZE_STRING);
            if ($form->is_valid()) {
                $member_info = $form->get_sanitized();
                if (isset($_SESSION['swpm-registered-level']))
                    $member_info['membership_level'] = absint($_SESSION['swpm-registered-level']);
                else if ($is_free && !empty($free_level))
                    $member_info['membership_level'] = $free_level;
                else if (empty($member_id)){
                    $message = array('succeeded' => false, 'message' => 'Membership Level Couldn\'t be found.');
                    BTransfer::get_instance()->set('status', $message);
                    return;
                }
                $member_info['last_accessed_from_ip'] = BTransfer::get_real_ip_addr();
                $member_info['member_since'] = date("Y-m-d");
                $member_info['subscription_starts'] = date("Y-m-d");
                $member_info['account_state'] = 'active';
                $settings = BSettings::get_instance();
                $plain_password = $member_info['plain_password'];
                unset($member_info['plain_password']);
                if(!empty($member_id) && !empty($code)){
                    $member_info['reg_code'] = '';
                    $wpdb->update($wpdb->prefix . "swpm_members_tbl", $member_info, array('member_id' => $member_id));
                    $last_insert_id = $member_id;
                }
                else{
                    $wpdb->insert($wpdb->prefix . "swpm_members_tbl", $member_info);
                    $last_insert_id = $wpdb->insert_id;
                }
                if (!isset($member_info['membership_level'])){
                    $query = 'SELECT membership_level FROM ' . $wpdb->prefix . 'swpm_members_tbl WHERE member_id=' . $member_id;
                    $member_info['membership_level'] = $wpdb->get_var( $query );
                }
                $member_info['plain_password'] = $plain_password;
                $subject = $settings->get_value('reg-complete-mail-subject');
                $body = $settings->get_value('reg-complete-mail-body');
                $from_address = $settings->get_value('email-from');
                $login_link = $settings->get_value('login-page-url');
                $headers = 'From: ' . $from_address . "\r\n";
                $query = "SELECT alias FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE id = " . $member_info['membership_level'];
                $member_info['membership_level_name'] = $wpdb->get_var($query);
                $values = array_values($member_info);
                $keys = array_map(function($n) {
                    return "{$n}";
                }, array_keys($member_info));
                $body = str_replace($keys, $values, $body);
                wp_mail(trim($_POST['email']), $subject, $body, $headers);
                if ($settings->get_value('enable-admin-notification-after-reg')) {
                    $subject = "Notification of New Member Registration";
                    $body = "A new member has registered. The following email was sent to the member." .
                            "\n\n-------Member Email----------\n" . $body .
                            "\n\n------End------\n";
                    wp_mail($from_address, $subject, $body, $headers);
                }
                // register to wordpress
                $query = "SELECT role FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE id = " . $member_info['membership_level'];
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
                self::create_wp_user($wp_user_info);
                /*                 * ******************** register to wordpress ********** */
                //@unset($_SESSION['swpm-registered-level']);
                $login_page_url = $settings->get_value('login-page-url');
                $after_rego_msg = '<p>Registration Successful. Please <a href="' . $login_page_url . '">Login</a></p>';
                $message = array('succeeded' => true, 'message' => $after_rego_msg);
                BTransfer::get_instance()->set('status', $message);
                return;
            }
            $message = array('succeeded' => false, 'message' => 'Please correct the following', 'extra' => $form->get_errors());
            BTransfer::get_instance()->set('status', $message);
        }
    }

    public function menu() {
        add_menu_page(__("WP Membership", 'swpm'), __("WP Membership", 'swpm')
                , 'manage_options', 'simple_wp_membership', array(&$this, "admin_members")
                , SIMPLE_WP_MEMBERSHIP_URL . '/images/logo.png');
        add_submenu_page('simple_wp_membership', __("Members", 'swpm'), __('Members', 'swpm'), 'activate_plugins', 'simple_wp_membership', array(&$this, "admin_members"));
        add_submenu_page('simple_wp_membership', __("Membership Levels", 'swpm'), __("Membership Levels", 'swpm'), 'activate_plugins', 'simple_wp_membership_levels', array(&$this, "admin_membership_levels"));
        add_submenu_page('simple_wp_membership', __("Settings", 'swpm'), __("Settings", 'swpm'), 'activate_plugins', 'simple_wp_membership_settings', array(&$this, "admin_settings"));
        $this->meta_box();
    }

    public function admin_membership_levels() {
        include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'classes/class.bMembershipLevels.php');
        $levels = new BMembershipLevels();

        $action = isset($_GET['level_action']) ? $_GET['level_action'] : (isset($_POST['action2']) ? $_POST['action2'] : "");
        switch ($action) {
            case 'add':
            case 'edit':
                $levels->process_form_request();
                break;
            case 'manage':
                $levels->manage();
                break;
            case 'delete':
            case 'bulk_delete':
                $levels->delete();
            default:
                $levels->show();
                break;
        }
    }

    public function admin_members() {
        include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'classes/class.bMembers.php');
        $members = new BMembers();

        $action = isset($_GET['member_action']) ? $_GET['member_action'] : (isset($_POST['action2']) ? $_POST['action2'] : "");
        switch ($action) {
            case 'add':
            case 'edit':
                $members->process_form_request();
                break;
            case 'delete':
            case 'bulk_delete':
                $members->delete();
            default:
                $members->show();
                break;
        }
    }

    public function admin_settings() {
        $current_tab = BSettings::get_instance()->current_tab;
        switch ($current_tab) {
            case 4:
                
                $link_for = filter_input(INPUT_POST, 'swpm_link_for',FILTER_SANITIZE_STRING);
                $member_id = filter_input(INPUT_POST, 'member_id',FILTER_SANITIZE_NUMBER_INT);
                $send_email = filter_input(INPUT_POST, 'swpm_reminder_email',FILTER_SANITIZE_NUMBER_INT);
                $links = bUtils::get_registration_link($link_for, $send_email, $member_id);
                include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_tools_settings.php');
                break;
            case 2:
                include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_payment_settings.php');
                break;
            default:
                include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_settings.php');
                break;
        }
    }

    public static function activate() {
        global $wpdb;
        if (function_exists('is_multisite') && is_multisite()) {
            // check if it is a network activation - if so, run the activation function for each blog id
            if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
                $old_blog = $wpdb->blogid;
                // Get all blog ids
                $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                foreach ($blogids as $blog_id) {
                    switch_to_blog($blog_id);
                    SimpleWpMembership::installer();
                    SimpleWpMembership::initdb();
                }
                switch_to_blog($old_blog);
                return;
            }
        }
        SimpleWpMembership::installer();
        SimpleWpMembership::initdb();
    }

    private static function installer() {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $sql = "CREATE TABLE " . $wpdb->prefix . "swpm_members_tbl (
			member_id int(12) NOT NULL PRIMARY KEY AUTO_INCREMENT,
			user_name varchar(32) NOT NULL,
			first_name varchar(32) DEFAULT '',
			last_name varchar(32) DEFAULT '',
			password varchar(64) NOT NULL,
			member_since date NOT NULL DEFAULT '0000-00-00',
			membership_level smallint(6) NOT NULL,
			more_membership_levels VARCHAR(100) DEFAULT NULL,
			account_state enum('active','inactive','expired','pending','unsubscribed') DEFAULT 'pending',
			last_accessed datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			last_accessed_from_ip varchar(64) NOT NULL,
			email varchar(64) DEFAULT NULL,
			phone varchar(64) DEFAULT NULL,
			address_street varchar(255) DEFAULT NULL,
			address_city varchar(255) DEFAULT NULL,
			address_state varchar(255) DEFAULT NULL,
			address_zipcode varchar(255) DEFAULT NULL,
			home_page varchar(255) DEFAULT NULL,
			country varchar(255) DEFAULT NULL,
			gender enum('male','female','not specified') DEFAULT 'not specified',
			referrer varchar(255) DEFAULT NULL,
			extra_info text,
			reg_code varchar(255) DEFAULT NULL,
			subscription_starts date DEFAULT NULL,
			initial_membership_level smallint(6) DEFAULT NULL,
			txn_id varchar(64) DEFAULT '',
			subscr_id varchar(32) DEFAULT '',
			company_name varchar(100) DEFAULT '',
			notes text DEFAULT NULL,
			flags int(11) DEFAULT '0',
			profile_image varchar(255) DEFAULT ''
          )ENGINE=MyISAM DEFAULT CHARSET=utf8;";
        dbDelta($sql);

        $sql = "CREATE TABLE " . $wpdb->prefix . "swpm_membership_tbl (
			id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
			alias varchar(127) NOT NULL,
			role varchar(255) NOT NULL DEFAULT 'subscriber',
			permissions tinyint(4) NOT NULL DEFAULT '0',
			subscription_period int(11) NOT NULL DEFAULT '-1',
			subscription_unit   VARCHAR(20)        NULL,
			loginredirect_page  text NULL,
			category_list longtext,
			page_list longtext,
			post_list longtext,
			comment_list longtext,
			attachment_list longtext,
			custom_post_list longtext,
			disable_bookmark_list longtext,
			options longtext,
			campaign_name varchar(60) NOT NULL DEFAULT ''
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        dbDelta($sql);
        $sql = "SELECT * FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE id = 1";
        $results = $wpdb->get_row($sql);
        if (is_null($results)) {
            $sql = "INSERT INTO  " . $wpdb->prefix . "swpm_membership_tbl  (
			id ,
			alias ,
			role ,
			permissions ,
			subscription_period ,
			subscription_unit,
			loginredirect_page,
			category_list ,
			page_list ,
			post_list ,
			comment_list,
			disable_bookmark_list,
			options,
			campaign_name
			)VALUES (1 , 'Content Protection', 'administrator', '15', '0',NULL,NULL, NULL , NULL , NULL , NULL,NULL,NULL,'');";
            $wpdb->query($sql);
        }
    }

    public static function initdb() {
        $settings = BSettings::get_instance();

        $installed_version = $settings->get_value('swpm-active-version');

        //Set other default settings values
        $reg_prompt_email_subject = "Complete your registration";
        $reg_prompt_email_body = "Dear {first_name} {last_name}" .
                "\n\nThank you for joining us!" .
                "\n\nPlease complete your registration by visiting the following link:" .
                "\n\n{reg_link}" .
                "\n\nThank You";
        $reg_email_subject = "Your registration is complete";
        $reg_email_body = "Dear {first_name} {last_name}\n\n" .
                "Your registration is now complete!\n\n" .
                "Registration details:\n" .
                "Username: {user_name}\n" .
                "Password: {password}\n\n" .
                "Please login to the member area at the following URL:\n\n" .
                "{login_link}\n\n" .
                "Thank You";

        $upgrade_email_subject = "Subject for email sent after account upgrade";
        $upgrade_email_body = "Dear {first_name} {last_name}" .
                "\n\nYour Account Has Been Upgraded." .
                "\n\nThank You";
        $reset_email_subject = get_bloginfo('name') . ": New Password";
        $reset_email_body = "Dear {first_name} {last_name}" .
                "\n\nHere is your new password" .
                "\n\nUser name: {user_name}" .
                "\n\nPassword: {password}" .
                "\n\nThank You";
        if (empty($installed_version)) {
            //Do fresh install tasks

            /*             * * Create the mandatory pages (if they are not there) ** */
            miscUtils::create_mandatory_wp_pages();
            /*             * * End of page creation ** */
            $settings->set_value('reg-complete-mail-subject', stripslashes($reg_email_subject))
                    ->set_value('reg-complete-mail-body', stripslashes($reg_email_body))
                    ->set_value('reg-prompt-complete-mail-subject', stripslashes($reg_prompt_email_subject))
                    ->set_value('reg-prompt-complete-mail-body', stripslashes($reg_prompt_email_body))
                    ->set_value('upgrade-complete-mail-subject', stripslashes($upgrade_email_subject))
                    ->set_value('upgrade-complete-mail-body', stripslashes($upgrade_email_body))
                    ->set_value('reset-mail-subject', stripslashes($reset_email_subject))
                    ->set_value('reset-mail-body', stripslashes($reset_email_body))
                    ->set_value('email-from', trim(get_option('admin_email')));
        }
        if (version_compare($installed_version, SIMPLE_WP_MEMBERSHIP_VER) == -1) {
            //Do upgrade tasks
        }

        $settings->set_value('swpm-active-version', SIMPLE_WP_MEMBERSHIP_VER)->save(); //save everything.
    }

    public function deactivate() {

    }

    public static function is_multisite_install() {
        if (function_exists('is_multisite') && is_multisite()) {
            return true;
        } else {
            return false;
        }
    }

    public function create_wp_user($wp_user_data) {
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
        if (!in_array('administrator', $user_cap))
            self::update_wp_user_Role($wp_user_id, $wp_user_data['role']);
        return $wp_user_id;
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
        if (in_array('administrator', array_keys((array) $caps)))
            return;
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

}
