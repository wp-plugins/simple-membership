<?php

class SwpmInitTimeTasks {

    public function __construct() {
        
    }

    public function do_init_tasks() {

        //Set up localisation. First loaded ones will override strings present in later loaded file.
        //Allows users to have a customized language in a different folder.
        $locale = apply_filters('plugin_locale', get_locale(), 'swpm');
        load_textdomain('swpm', WP_LANG_DIR . "/swpm-$locale.mo");
        load_plugin_textdomain('swpm', false, SIMPLE_WP_MEMBERSHIP_DIRNAME . '/languages/');

        if (!isset($_COOKIE['swpm_session'])) { // give a unique ID to current session.
            $uid = md5(microtime());
            $_COOKIE['swpm_session'] = $uid; // fake it for current session/
            setcookie('swpm_session', $uid, 0, '/');
        }

        //Crete the custom post types
        $this->create_post_type();

        if (current_user_can('manage_options')) { // Admin stuff
            $this->admin_init();
        }

        //Do frontend-only init time taks 
        if (!is_admin()) {
            SwpmAuth::get_instance();
            $this->verify_and_delete_account();
            $swpm_logout = filter_input(INPUT_GET, 'swpm-logout');
            if (!empty($swpm_logout)) {
                SwpmAuth::get_instance()->logout();
                wp_redirect(home_url());
            }
            $this->process_password_reset();
            $this->register_member();
            $this->edit_profile();
        }

        //IPN listener
        $this->swpm_ipn_listener();
    }

    public function admin_init() {
        $createswpmuser = filter_input(INPUT_POST, 'createswpmuser');
        if (!empty($createswpmuser)) {
            SwpmAdminRegistration::get_instance()->register();
        }
        $editswpmuser = filter_input(INPUT_POST, 'editswpmuser');
        if (!empty($editswpmuser)) {
            $id = filter_input(INPUT_GET, 'member_id', FILTER_VALIDATE_INT);
            SwpmAdminRegistration::get_instance()->edit($id);
        }
        $createswpmlevel = filter_input(INPUT_POST, 'createswpmlevel');
        if (!empty($createswpmlevel)) {
            SwpmMembershipLevel::get_instance()->create();
        }
        $editswpmlevel = filter_input(INPUT_POST, 'editswpmlevel');
        if (!empty($editswpmlevel)) {
            $id = filter_input(INPUT_GET, 'id');
            SwpmMembershipLevel::get_instance()->edit($id);
        }
    }

    public function create_post_type() {
        //The payment button data for membership levels will be stored using this CPT
        register_post_type('swpm_payment_button', array(
            'public' => false,
            'publicly_queryable' => false,
            'show_ui' => false,
            'query_var' => false,
            'rewrite' => false,
            'capability_type' => 'page',
            'has_archive' => false,
            'hierarchical' => false,
            'supports' => array('title', 'editor')
        ));
        
    }

    private function verify_and_delete_account() {
        include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'classes/class.swpm-members.php');
        $delete_account = filter_input(INPUT_GET, 'delete_account');
        if (empty($delete_account)) {
            return;
        }
        $password = filter_input(INPUT_POST, 'account_delete_confirm_pass', FILTER_UNSAFE_RAW);

        $auth = SwpmAuth::get_instance();
        if (!$auth->is_logged_in()) {
            return;
        }
        if (empty($password)) {
            SwpmUtils::account_delete_confirmation_ui();
        }

        $nonce_field = filter_input(INPUT_POST, 'account_delete_confirm_nonce');
        if (empty($nonce_field) || !wp_verify_nonce($nonce_field, 'swpm_account_delete_confirm')) {
            SwpmUtils::account_delete_confirmation_ui(SwpmUtils::_("Sorry, Nonce verification failed."));
        }
        if ($auth->match_password($password)) {
            $auth->delete();
            wp_redirect(home_url());
        } else {
            SwpmUtils::account_delete_confirmation_ui(SwpmUtils::_("Sorry, Password didn't match."));
        }
    }

    public function process_password_reset() {
        $message = "";
        $swpm_reset = filter_input(INPUT_POST, 'swpm-reset');
        $swpm_reset_email = filter_input(INPUT_POST, 'swpm_reset_email', FILTER_UNSAFE_RAW);
        if (!empty($swpm_reset)) {
            SwpmFrontRegistration::get_instance()->reset_password($swpm_reset_email);
        }
    }

    private function register_member() {
        $registration = filter_input(INPUT_POST, 'swpm_registration_submit');
        if (!empty($registration)) {
            SwpmFrontRegistration::get_instance()->register();
        }
    }

    private function edit_profile() {
        $swpm_editprofile_submit = filter_input(INPUT_POST, 'swpm_editprofile_submit');
        if (!empty($swpm_editprofile_submit)) {
            SwpmFrontRegistration::get_instance()->edit();
            //TODO - do a redirect?
        }
    }

    /* PayPal Payment IPN listener */

    public function swpm_ipn_listener() {
        $swpm_process_ipn = filter_input(INPUT_GET, 'swpm_process_ipn');
        if ($swpm_process_ipn == '1') {
            include(SIMPLE_WP_MEMBERSHIP_PATH . 'ipn/swpm_handle_pp_ipn.php');
            exit;
        }
    }

}