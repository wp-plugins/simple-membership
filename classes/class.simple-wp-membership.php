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
include_once('class.bMessages.php');
include_once('class.bAjax.php');
include_once('class.bRegistration.php');
include_once('class.bFrontRegistration.php');
include_once('class.bAdminRegistration.php');
include_once('class.bMembershipLevel.php');
include_once('class.bMembershipLevelCustom.php');
include_once('class.bMembershipLevelUtils.php');
include_once('class.bPermissionCollection.php');
include_once('class.bAuthPermissionCollection.php');
class SimpleWpMembership {
    public function __construct() {
        add_action('admin_menu', array(&$this, 'menu'));        
        add_action('init', array(&$this, 'init'));

        add_filter('the_content', array(&$this, 'filter_content'),11,1);
        add_filter('widget_text', 'do_shortcode');
        add_filter('show_admin_bar', array(&$this, 'hide_adminbar'));
        add_filter('comment_text', array(&$this, 'filter_comment'));
        add_filter('wp_get_attachment_url', array(&$this, 'filter_attachment_url'), 10, 2);
        add_filter('wp_get_attachment_metadata', array(&$this, 'filter_attachment'), 10, 2);
        add_filter('attachment_fields_to_save', array(&$this,'save_attachment_extra'), 10, 2);
        add_filter('the_content_more_link', array(&$this, 'filter_moretag'), 10, 2 );

        add_shortcode("swpm_registration_form", array(&$this, 'registration_form'));
        add_shortcode('swpm_profile_form', array(&$this, 'profile_form'));
        add_shortcode('swpm_login_form', array(&$this, 'login'));
        add_shortcode('swpm_reset_form', array(&$this, 'reset'));

        add_action('save_post', array(&$this, 'save_postdata'));
        add_action('admin_notices', array(&$this, 'notices'));
        add_action('wp_enqueue_scripts', array(&$this, 'front_library'));
        add_action('load-toplevel_page_simple_wp_membership', array(&$this, 'admin_library'));
        add_action('load-wp-membership_page_simple_wp_membership_levels', array(&$this, 'admin_library'));
        add_action('profile_update', array(&$this, 'sync_with_wp_profile'), 10, 2);
        add_action('wp_logout', array(&$this, 'wp_logout'));
        add_action('wp_authenticate', array(&$this, 'wp_login'), 1, 2);
        add_action('swpm_logout', array(&$this, 'swpm_logout'));        
        
        //AJAX hooks
        add_action('wp_ajax_swpm_validate_email', 'BAjax::validate_email_ajax');
        add_action('wp_ajax_nopriv_swpm_validate_email', 'BAjax::validate_email_ajax');
        add_action('wp_ajax_swpm_validate_user_name', 'BAjax::validate_user_name_ajax');
        add_action('wp_ajax_nopriv_swpm_validate_user_name', 'BAjax::validate_user_name_ajax');

        //init is too early for settings api.
        add_action('admin_init', array(&$this, 'admin_init_hook'));
        add_action('plugins_loaded', array(&$this, "plugins_loaded"));
        add_action('password_reset', array(&$this, 'wp_password_reset_hook'), 10, 2);
    }
    function wp_password_reset_hook( $user, $pass ) 
    {
        $swpm_id = BUtils::get_user_by_user_name($user->user_login);
        if (!empty($swpm_id)){
            $password_hash = BUtils::encrypt_password($pass);
            global $wpdb;
            $wpdb->update($wpdb->prefix . "swpm_members_tbl", array('password' => $password_hash), array('member_id' => $swpm_id));
        }       
    }
    
    public function save_attachment_extra($post, $attachment) {
        $this->save_postdata($post['ID']);
        return $post;
    }
    public function filter_attachment($content, $post_id){
        if(is_admin()){//No need to filter on the admin side
            return $content;
        }
        
        $acl = BAccessControl::get_instance();
        if (has_post_thumbnail($post_id)){ return $content;}
        if ($acl->can_i_read_post($post_id)) {return $content;}
        
        
        if (isset($content['file'])){
            $content['file'] = 'restricted-icon.png';
            $content['width'] = '400';
            $content['height'] = '400';
        }
        
        if (isset($content['sizes'])){
            if ($content['sizes']['thumbnail']){
                $content['sizes']['thumbnail']['file'] = 'restricted-icon.png';
                $content['sizes']['thumbnail']['mime-type'] = 'image/png';
            }
            if ($content['sizes']['medium']){
                $content['sizes']['medium']['file'] = 'restricted-icon.png';
                $content['sizes']['medium']['mime-type'] = 'image/png';
            }
            if ($content['sizes']['post-thumbnail']){
                $content['sizes']['post-thumbnail']['file'] = 'restricted-icon.png';
                $content['sizes']['post-thumbnail']['mime-type'] = 'image/png';
            }
        }
        return $content;
    }
    
    public function filter_attachment_url($content, $post_id){
        if(is_admin()){//No need to filter on the admin side
            return $content;
        }
        $acl = BAccessControl::get_instance();
        if (has_post_thumbnail($post_id)){return $content;}
        
        if ($acl->can_i_read_post($post_id)){return $content;}
        
        return BUtils::get_restricted_image_url();
    }
    
    public function admin_init_hook(){
        BSettings::get_instance()->init_config_hooks();
        $addon_saved = filter_input(INPUT_POST, 'swpm-addon-settings');
        if(!empty($addon_saved)){
            do_action('swpm_addon_settings_save');
        }
    }
    
    public function hide_adminbar(){
        if (!is_user_logged_in()){//Never show admin bar if the user is not even logged in
            return false;
        }
        $hide = BSettings::get_instance()->get_value('hide-adminbar');
        return $hide? FALSE: TRUE;
    }
    public function shutdown(){
        BLog::writeall();
    }
    public static function swpm_login($user, $pass, $rememberme = true) {
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            if ($current_user->user_login == $user){
                return;
            }
        }
        $user = wp_signon(array('user_login' => $user, 'user_password' => $pass, 'remember' => $rememberme), is_ssl());
        if ( is_a( $user, 'WP_User' ) ) {
            wp_set_current_user( $user->ID, $user->user_login );
        }
        do_action('swpm_after_login');
        if (!BUtils::is_ajax()) {
            wp_redirect(site_url());
        }
    }

    public function swpm_logout() {
        if (is_user_logged_in()) {
            wp_logout();
            wp_set_current_user(0);
        }
    }

    public function wp_login($username, $password) {
        $auth = BAuth::get_instance(); 
        if (($auth->is_logged_in() &&($auth->userData->user_name == $username))) {
            return;
        }
        if(!empty($username)) {$auth->login($username, $password, true);}
    }

    public function wp_logout() {
        $auth = BAuth::get_instance();
        if ($auth->is_logged_in()){
            $auth->logout();
        }
    }

    public function sync_with_wp_profile($wp_user_id) {
        global $wpdb;
        $wp_user_data = get_userdata($wp_user_id);
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "swpm_members_tbl WHERE " . ' user_name=%s', $wp_user_data->user_login);
        $profile = $wpdb->get_row($query, ARRAY_A);
        $profile = (array) $profile;
        if (empty($profile)){
            return;
        }
        $profile['user_name'] = $wp_user_data->user_login;
        $profile['email'] = $wp_user_data->user_email;
        $profile['password'] = $wp_user_data->user_pass;
        $profile['first_name'] = $wp_user_data->user_firstname;
        $profile['last_name'] = $wp_user_data->user_lastname;
        $wpdb->update($wpdb->prefix . "swpm_members_tbl", $profile, array('member_id' => $profile['member_id']));
    }

    public function login() {
        ob_start();
        $auth = BAuth::get_instance();
        if ($auth->is_logged_in()){
            include(SIMPLE_WP_MEMBERSHIP_PATH . 'views/loggedin.php');
        }
        else {
            $setting = BSettings::get_instance();
            $password_reset_url = $setting->get_value('reset-page-url');
            $join_url = $setting->get_value('join-us-page-url');

            include(SIMPLE_WP_MEMBERSHIP_PATH . 'views/login.php');

        }
        return ob_get_clean();
    }

    public function reset() {
        $succeeded = $this->notices();
        if($succeeded){
            return '';
        }
        ob_start();
        include(SIMPLE_WP_MEMBERSHIP_PATH . 'views/forgot_password.php');
        return ob_get_clean();
    }
    public function profile_form() {
        $auth = BAuth::get_instance();
        $this->notices();
        if ($auth->is_logged_in()) {
            $out = apply_filters('swpm_profile_form_override', '');
            if (!empty($out)){return $out;}
            $user_data = (array) $auth->userData;
            $user_data['membership_level_alias'] = $auth->get('alias');
            ob_start();
            extract($user_data, EXTR_SKIP);
            include(SIMPLE_WP_MEMBERSHIP_PATH . 'views/edit.php');
            return ob_get_clean();
        }
        return BUtils::_( 'You are not logged in.');
    }

    public function notices() {
        $message = BTransfer::get_instance()->get('status');
        $succeeded = false;
        if (empty($message)) { return false;}
        if ($message['succeeded']) {
            echo "<div id='message' class='updated'>";
            $succeeded = true;
        } else{
            echo "<div id='message' class='error'>";
        }
        echo $message['message'];
        $extra = isset($message['extra']) ? $message['extra'] : array();
        if (is_string($extra)){
            echo $extra;
        }
        else if (is_array($extra)) {
            echo '<ul>';
            foreach ($extra as $key => $value){
                echo '<li>' . $value . '</li>';
            }
            echo '</ul>';
        }
        echo "</div>";
        return $succeeded;
    }

    public function meta_box() {
        if (function_exists('add_meta_box')) {
            $post_types = get_post_types();
            foreach ($post_types as $post_type => $post_type){
                add_meta_box('swpm_sectionid',
                        __('Simple WP Membership Protection', 'swpm'),
                        array(&$this, 'inner_custom_box'), $post_type, 'advanced');
            }
        } else {//older version doesn't have custom post type so modification isn't needed.
            add_action('dbx_post_advanced', array(&$this, 'show_old_custom_box'));
            add_action('dbx_page_advanced', array(&$this, 'show_old_custom_box'));
        }
    }

    public function show_old_custom_box() {
        echo '<div class="dbx-b-ox-wrapper">' . "\n";
        echo '<fieldset id="eMember_fieldsetid" class="dbx-box">' . "\n";
        echo '<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">' .
        __('Simple Membership Protection options', 'swpm') . "</h3></div>";
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
        echo '<h4>' . __("Do you want to protect this content?", 'swpm') . '</h4>';
        echo '<input type="radio" ' . ((!$is_protected) ? 'checked' : "") .
                '  name="swpm_protect_post" value="1" /> No, Do not protect this content. <br/>';
        echo '<input type="radio" ' . (($is_protected) ? 'checked' : "") .
                '  name="swpm_protect_post" value="2" /> Yes, Protect this content.<br/>';
        echo '<h4>' . __("Select the membership level that can access this content:", 'swpm') . "</h4>";
        $query = "SELECT * FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE  id !=1 ";
        $levels = $wpdb->get_results($query, ARRAY_A);
        foreach ($levels as $level) {
            echo '<input type="checkbox" ' . (BPermission::get_instance($level['id'])->is_permitted($id) ? "checked='checked'" : "") .
                    ' name="swpm_protection_level[' . $level['id'] . ']" value="' . $level['id'] . '" /> ' . $level['alias'] . "<br/>";
        }
    }

    public function save_postdata($post_id) {
        global $wpdb;
        $post_type = filter_input(INPUT_POST,'post_type');
        $swpm_protect_post = filter_input(INPUT_POST,'swpm_protect_post');
        $swpm_noncename = filter_input(INPUT_POST, 'swpm_noncename');
        if (wp_is_post_revision($post_id)){
            return;
        }
        if (!wp_verify_nonce($swpm_noncename, plugin_basename(__FILE__))){
            return $post_id;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
            return $post_id;
        }
        if ('page' == $post_type ) {
            if (!current_user_can('edit_page', $post_id)){
                return $post_id;
            }
        } else {
            if (!current_user_can('edit_post', $post_id)){
                return $post_id;
            }
        }
        if (empty($swpm_protect_post)){
            return;
        }
        // OK, we're authenticated: we need to find and save the data
        $isprotected = ($swpm_protect_post == 2);
        $args =  array('swpm_protection_level'=>array(
                            'filter' => FILTER_VALIDATE_INT,
                            'flags'  => FILTER_REQUIRE_ARRAY,
                           ));
        $swpm_protection_level = filter_input_array(INPUT_POST, $args);
        $swpm_protection_level = $swpm_protection_level['swpm_protection_level'];
        if (!empty($post_type)) {
            if($isprotected){
                BProtection::get_instance()->apply(array($post_id),$post_type);
            }
            else{
                BProtection::get_instance()->remove(array($post_id),$post_type);
            }
            BProtection::get_instance()->save();
            $query = "SELECT id FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE  id !=1 ";
            $level_ids = $wpdb->get_col($query);
            foreach ($level_ids as $level){
                if(isset($swpm_protection_level[$level])){
                    BPermission::get_instance($level)->apply(array($post_id), $post_type)->save();
                }
                else{
                    BPermission::get_instance($level)->remove(array($post_id), $post_type)->save();
                }
            }
        }
        $enable_protection = array();
        $enable_protection['protect'] = $swpm_protect_post;
        $enable_protection['level'] = $swpm_protection_level;
        return $enable_protection;
    }

    public function filter_comment($content) {
        $acl = BAccessControl::get_instance();
        global $comment;
        return $acl->filter_post($comment->comment_post_ID, $content);
    }

    public function filter_content($content) {
        if (is_preview()) {return $content;}
        $acl = BAccessControl::get_instance();
        global $post;
        return $acl->filter_post($post->ID, $content);
    }

    public function filter_moretag($more_link, $more_link_text = "More") {
        $moretag = BSettings::get_instance()->get_value('enable-moretag');
        if (empty($moretag)) {return $more_link;}
        $acl = BAccessControl::get_instance();
        global $post;
        return $acl->filter_post_with_moretag($post->ID, $more_link, $more_link_text);
    }

    public function admin_init() {
        $createswpmuser = filter_input(INPUT_POST, 'createswpmuser');
        if (!empty($createswpmuser)) {
            BAdminRegistration::get_instance()->register();
        }
        $editswpmuser = filter_input(INPUT_POST, 'editswpmuser');
        if (!empty($editswpmuser)) {
            $id = filter_input(INPUT_GET, 'member_id', FILTER_VALIDATE_INT);
            BAdminRegistration::get_instance()->edit($id);
        }
        $createswpmlevel = filter_input(INPUT_POST, 'createswpmlevel');
        if (!empty($createswpmlevel)) {
            BMembershipLevel::get_instance()->create();
        }
        $editswpmlevel = filter_input(INPUT_POST, 'editswpmlevel');
        if (!empty($editswpmlevel)) {
            $id = filter_input(INPUT_GET, 'id');
            BMembershipLevel::get_instance()->edit($id);
        }
    }

    public function init() {

        //Set up localisation. First loaded ones will override strings present in later loaded file.
        //Allows users to have a customized language in a different folder.
        $locale = apply_filters( 'plugin_locale', get_locale(), 'swpm' );
        load_textdomain( 'swpm', WP_LANG_DIR . "/swpm-$locale.mo" );
	load_plugin_textdomain('swpm', false, SIMPLE_WP_MEMBERSHIP_DIRNAME. '/languages/');

        if (!isset($_COOKIE['swpm_session'])) { // give a unique ID to current session.
            $uid = md5(microtime());
            $_COOKIE['swpm_session'] = $uid; // fake it for current session/
            setcookie('swpm_session', $uid, 0, '/');
        }

        if(current_user_can('manage_options')){ // admin stuff
            $this->admin_init();
        }
        if (!is_admin()){ //frontend stuff
            BAuth::get_instance();
            $this->verify_and_delete_account();
            $swpm_logout = filter_input(INPUT_GET, 'swpm-logout');
            if (!empty($swpm_logout)) {
                BAuth::get_instance()->logout();
                wp_redirect(home_url());
            }
            $this->process_password_reset();
            $this->register_member();
            $this->edit_profile();
        }
        $this->swpm_ipn_listener();
    }

    public function swpm_ipn_listener() {
        $swpm_process_ipn = filter_input(INPUT_GET, 'swpm_process_ipn');
        if ($swpm_process_ipn == '1') {
            include(SIMPLE_WP_MEMBERSHIP_PATH.'ipn/swpm_handle_pp_ipn.php');
            exit;
        }
    }

    public function process_password_reset() {
        $message = "";
        $swpm_reset = filter_input(INPUT_POST, 'swpm-reset');
        $swpm_reset_email = filter_input(INPUT_POST, 'swpm_reset_email', FILTER_UNSAFE_RAW);
        if (!empty($swpm_reset)) {
            BFrontRegistration::get_instance()->reset_password($swpm_reset_email);
        }
    }

    private function edit_profile() {
        $swpm_editprofile_submit = filter_input(INPUT_POST, 'swpm_editprofile_submit');        
        if (!empty($swpm_editprofile_submit)) {
            BFrontRegistration::get_instance()->edit();
            //todo: do a redirect
        }
    }

    public function admin_library() {
        $this->common_library();
        wp_enqueue_script('password-strength-meter');
        wp_enqueue_script('swpm.password-meter', SIMPLE_WP_MEMBERSHIP_URL . '/js/swpm.password-meter.js');
        wp_enqueue_style('jquery.tools.dateinput', SIMPLE_WP_MEMBERSHIP_URL . '/css/jquery.tools.dateinput.css');
        wp_enqueue_script('jquery.tools', SIMPLE_WP_MEMBERSHIP_URL . '/js/jquery.tools18.min.js');     
        $settings = array('statusChangeEmailHead'=> BSettings::get_instance()->get_value('account-change-email-subject'),
                          'statusChangeEmailBody'=> BSettings::get_instance()->get_value('account-change-email-body'));
        wp_localize_script( 'swpm.password-meter', 'SwpmSettings', $settings );        
    }

    public function front_library() {
        $this->common_library();
    }

    private function common_library() {
        wp_enqueue_script('jquery');
        wp_enqueue_style('swpm.common', SIMPLE_WP_MEMBERSHIP_URL . '/css/swpm.common.css');
        wp_enqueue_style('validationEngine.jquery', SIMPLE_WP_MEMBERSHIP_URL . '/css/validationEngine.jquery.css');
        wp_enqueue_script('jquery.validationEngine-en', SIMPLE_WP_MEMBERSHIP_URL . '/js/jquery.validationEngine-en.js');
        wp_enqueue_script('jquery.validationEngine', SIMPLE_WP_MEMBERSHIP_URL . '/js/jquery.validationEngine.js');
    }

    public function registration_form($atts) {
        $succeeded = $this->notices();
        if($succeeded){
            return;
        }
        $is_free = BSettings::get_instance()->get_value('enable-free-membership');
        $free_level = absint(BSettings::get_instance()->get_value('free-membership-id'));        
        $level = isset($atts['level'])? absint($atts['level']): ($is_free? $free_level: null);
        return BFrontRegistration::get_instance()->regigstration_ui($level);
    }

    private function register_member() {
        $registration = filter_input(INPUT_POST, 'swpm_registration_submit');
        if (!empty($registration)) {
            BFrontRegistration::get_instance()->register();
        }
    }

    public function menu() {
        $menu_parent_slug = 'simple_wp_membership';

        add_menu_page(__("WP Membership", 'swpm'), __("WP Membership", 'swpm')
                , 'manage_options', $menu_parent_slug, array(&$this, "admin_members")
                , SIMPLE_WP_MEMBERSHIP_URL . '/images/logo.png');
        add_submenu_page($menu_parent_slug, __("Members", 'swpm'), __('Members', 'swpm'),
                'manage_options', 'simple_wp_membership', array(&$this, "admin_members"));
        add_submenu_page($menu_parent_slug, __("Membership Levels", 'swpm'), __("Membership Levels", 'swpm'),
                'manage_options', 'simple_wp_membership_levels', array(&$this, "admin_membership_levels"));
        add_submenu_page($menu_parent_slug, __("Settings", 'swpm'), __("Settings", 'swpm'),
                'manage_options', 'simple_wp_membership_settings', array(&$this, "admin_settings"));
        add_submenu_page($menu_parent_slug, __("Add-ons", 'swpm'), __("Add-ons", 'swpm'),
                'manage_options', 'simple_wp_membership_addons', array(&$this, "add_ons_menu"));
        
        do_action('swpm_after_main_admin_menu', $menu_parent_slug);

        $this->meta_box();
    }

    public function admin_membership_levels() {
        include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'classes/class.bMembershipLevels.php');
        $levels = new BMembershipLevels();
        $level_action = filter_input(INPUT_GET, 'level_action');
        $action2 = filter_input(INPUT_GET, 'action2');
        $action = $level_action ? $level_action : ($action2 ? $action2 : "");
        switch ($action) {
            case 'add':
            case 'edit':
                $levels->process_form_request();
                break;
            case 'manage':
                $levels->manage();
                break;
            case 'category_list':
                $levels->manage_categroy();
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
        $action = filter_input(INPUT_GET, 'member_action');
        $action = empty($action)? filter_input(INPUT_POST, 'action') : $action;
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
            case 6:
                include(SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_addon_settings.php');
                break;         
            case 4:
                $link_for = filter_input(INPUT_POST, 'swpm_link_for',FILTER_SANITIZE_STRING);
                $member_id = filter_input(INPUT_POST, 'member_id',FILTER_SANITIZE_NUMBER_INT);
                $send_email = filter_input(INPUT_POST, 'swpm_reminder_email',FILTER_SANITIZE_NUMBER_INT);
                $links = BUtils::get_registration_link($link_for, $send_email, $member_id);
                include(SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_tools_settings.php');
                break;
            case 2:
                include(SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_payment_settings.php');
                break;
            default:
                include(SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_settings.php');
                break;
        }
    }
    
    public function add_ons_menu(){
         include(SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_add_ons_page.php');
    }

    public function plugins_loaded(){
        //Runs when plugins_loaded action gets fired
        if(is_admin()){
            //Check and run DB upgrade operation (if needed)
            if (get_option('swpm_db_version') != SIMPLE_WP_MEMBERSHIP_DB_VER) {
                include_once('class.bInstallation.php');
                BInstallation::run_safe_installer();
            }
        }        
    }
    
    public static function activate() {
        wp_schedule_event(time(), 'daily', 'swpm_account_status_event');
        wp_schedule_event(time(), 'daily', 'swpm_delete_pending_account_event');
        include_once('class.bInstallation.php');
        BInstallation::run_safe_installer();
    }
    
    public function deactivate() {
        wp_clear_scheduled_hook('swpm_account_status_event');
        wp_clear_scheduled_hook('swpm_delete_pending_account_event');        
    }
    private function verify_and_delete_account(){
        include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'classes/class.bMembers.php');
        $delete_account = filter_input(INPUT_GET, 'delete_account');
        if (empty($delete_account)) {return; }
        $password = filter_input(INPUT_POST, 'account_delete_confirm_pass',FILTER_UNSAFE_RAW);

        $auth = BAuth::get_instance();
        if (!$auth->is_logged_in()){return;}
        if (empty($password)){
            BUtils::account_delete_confirmation_ui();
        }
        
        $nonce_field = filter_input(INPUT_POST, 'account_delete_confirm_nonce');
        if (empty($nonce_field) || !wp_verify_nonce($nonce_field, 'swpm_account_delete_confirm')){
            BUtils::account_delete_confirmation_ui(BUtils::_("Sorry, Nonce verification failed."));
        }       
        if ($auth->match_password($password)){
            $auth->delete();
            wp_redirect(home_url());
        }
        else{
            BUtils::account_delete_confirmation_ui(BUtils::_("Sorry, Password didn't match."));
        }
    }
}
