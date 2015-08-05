<?php

include_once('class.swpm-misc-utils.php');
include_once('class.swpm-utils.php');
include_once('class.swpm-init-time-tasks.php');
include_once('class.swpm-member-utils.php');
include_once('class.swpm-settings.php');
include_once('class.swpm-protection.php');
include_once('class.swpm-permission.php');
include_once('class.swpm-auth.php');
include_once('class.swpm-access-control.php');
include_once('class.swpm-form.php');
include_once('class.swpm-transfer.php');
include_once('class.swpm-front-form.php');
include_once('class.swpm-level-form.php');
include_once('class.swpm-membership-levels.php');
include_once('class.swpm-log.php');
include_once('class.swpm-messages.php');
include_once('class.swpm-ajax.php');
include_once('class.swpm-registration.php');
include_once('class.swpm-front-registration.php');
include_once('class.swpm-admin-registration.php');
include_once('class.swpm-membership-level.php');
include_once('class.swpm-membership-level-custom.php');
include_once('class.swpm-membership-level-utils.php');
include_once('class.swpm-permission-collection.php');
include_once('class.swpm-auth-permission-collection.php');
include_once('class.swpm-transactions.php');
include_once('shortcode-related/class.swpm-shortcodes-handler.php');

class SimpleWpMembership {

    public function __construct() {
        add_action('admin_menu', array(&$this, 'menu'));
        add_action('init', array(&$this, 'init_hook'));

        add_filter('the_content', array(&$this, 'filter_content'), 11, 1);
        add_filter('widget_text', 'do_shortcode');
        add_filter('show_admin_bar', array(&$this, 'hide_adminbar'));
        add_filter('comment_text', array(&$this, 'filter_comment'));
        add_filter('wp_get_attachment_url', array(&$this, 'filter_attachment_url'), 10, 2);
        add_filter('wp_get_attachment_metadata', array(&$this, 'filter_attachment'), 10, 2);
        add_filter('attachment_fields_to_save', array(&$this, 'save_attachment_extra'), 10, 2);
        add_filter('the_content_more_link', array(&$this, 'filter_moretag'), 10, 2);

        //TODO - refactor these shortcodes into the shortcodes handler class
        add_shortcode("swpm_registration_form", array(&$this, 'registration_form'));
        add_shortcode('swpm_profile_form', array(&$this, 'profile_form'));
        add_shortcode('swpm_login_form', array(&$this, 'login'));
        add_shortcode('swpm_reset_form', array(&$this, 'reset'));

        new SwpmShortcodesHandler(); //Tackle the shortcode definitions and implementation.

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
        add_action('wp_ajax_swpm_validate_email', 'SwpmAjax::validate_email_ajax');
        add_action('wp_ajax_nopriv_swpm_validate_email', 'SwpmAjax::validate_email_ajax');
        add_action('wp_ajax_swpm_validate_user_name', 'SwpmAjax::validate_user_name_ajax');
        add_action('wp_ajax_nopriv_swpm_validate_user_name', 'SwpmAjax::validate_user_name_ajax');

        //init is too early for settings api.
        add_action('admin_init', array(&$this, 'admin_init_hook'));
        add_action('plugins_loaded', array(&$this, "plugins_loaded"));
        add_action('password_reset', array(&$this, 'wp_password_reset_hook'), 10, 2);
    }

    function wp_password_reset_hook($user, $pass) {
        $swpm_id = SwpmUtils::get_user_by_user_name($user->user_login);
        if (!empty($swpm_id)) {
            $password_hash = SwpmUtils::encrypt_password($pass);
            global $wpdb;
            $wpdb->update($wpdb->prefix . "swpm_members_tbl", array('password' => $password_hash), array('member_id' => $swpm_id));
        }
    }

    public function save_attachment_extra($post, $attachment) {
        $this->save_postdata($post['ID']);
        return $post;
    }

    public function filter_attachment($content, $post_id) {
        if (is_admin()) {//No need to filter on the admin side
            return $content;
        }

        $acl = SwpmAccessControl::get_instance();
        if (has_post_thumbnail($post_id)) {
            return $content;
        }
        if ($acl->can_i_read_post($post_id)) {
            return $content;
        }


        if (isset($content['file'])) {
            $content['file'] = 'restricted-icon.png';
            $content['width'] = '400';
            $content['height'] = '400';
        }

        if (isset($content['sizes'])) {
            if ($content['sizes']['thumbnail']) {
                $content['sizes']['thumbnail']['file'] = 'restricted-icon.png';
                $content['sizes']['thumbnail']['mime-type'] = 'image/png';
            }
            if ($content['sizes']['medium']) {
                $content['sizes']['medium']['file'] = 'restricted-icon.png';
                $content['sizes']['medium']['mime-type'] = 'image/png';
            }
            if ($content['sizes']['post-thumbnail']) {
                $content['sizes']['post-thumbnail']['file'] = 'restricted-icon.png';
                $content['sizes']['post-thumbnail']['mime-type'] = 'image/png';
            }
        }
        return $content;
    }

    public function filter_attachment_url($content, $post_id) {
        if (is_admin()) {//No need to filter on the admin side
            return $content;
        }
        $acl = SwpmAccessControl::get_instance();
        if (has_post_thumbnail($post_id)) {
            return $content;
        }

        if ($acl->can_i_read_post($post_id)) {
            return $content;
        }

        return SwpmUtils::get_restricted_image_url();
    }

    public function admin_init_hook() {
        $this->common_library();
        SwpmSettings::get_instance()->init_config_hooks();
        $addon_saved = filter_input(INPUT_POST, 'swpm-addon-settings');
        if (!empty($addon_saved)) {
            do_action('swpm_addon_settings_save');
        }
    }

    public function hide_adminbar() {
        if (!is_user_logged_in()) {//Never show admin bar if the user is not even logged in
            return false;
        }
        $hide = SwpmSettings::get_instance()->get_value('hide-adminbar');
        return $hide ? FALSE : TRUE;
    }

    public function shutdown() {
        SwpmLog::writeall();
    }

    public static function swpm_login($user, $pass, $rememberme = true) {
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            if ($current_user->user_login == $user) {
                return;
            }
        }
        $user = wp_signon(array('user_login' => $user, 'user_password' => $pass, 'remember' => $rememberme), is_ssl());
        if (is_a($user, 'WP_User')) {
            wp_set_current_user($user->ID, $user->user_login);
        }
        do_action('swpm_after_login');
        if (!SwpmUtils::is_ajax()) {
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
        $auth = SwpmAuth::get_instance();
        if (($auth->is_logged_in() && ($auth->userData->user_name == $username))) {
            return;
        }
        if (!empty($username)) {
            $auth->login($username, $password, true);
        }
    }

    public function wp_logout() {
        $auth = SwpmAuth::get_instance();
        if ($auth->is_logged_in()) {
            $auth->logout();
        }
    }

    public function sync_with_wp_profile($wp_user_id) {
        global $wpdb;
        $wp_user_data = get_userdata($wp_user_id);
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "swpm_members_tbl WHERE " . ' user_name=%s', $wp_user_data->user_login);
        $profile = $wpdb->get_row($query, ARRAY_A);
        $profile = (array) $profile;
        if (empty($profile)) {
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
        $auth = SwpmAuth::get_instance();
        if ($auth->is_logged_in()) {
            include(SIMPLE_WP_MEMBERSHIP_PATH . 'views/loggedin.php');
        } else {
            $setting = SwpmSettings::get_instance();
            $password_reset_url = $setting->get_value('reset-page-url');
            $join_url = $setting->get_value('join-us-page-url');

            include(SIMPLE_WP_MEMBERSHIP_PATH . 'views/login.php');
        }
        return ob_get_clean();
    }

    public function reset() {
        $succeeded = $this->notices();
        if ($succeeded) {
            return '';
        }
        ob_start();
        include(SIMPLE_WP_MEMBERSHIP_PATH . 'views/forgot_password.php');
        return ob_get_clean();
    }

    public function profile_form() {
        $auth = SwpmAuth::get_instance();
        $this->notices();
        if ($auth->is_logged_in()) {
            $out = apply_filters('swpm_profile_form_override', '');
            if (!empty($out)) {
                return $out;
            }
            $user_data = (array) $auth->userData;
            $user_data['membership_level_alias'] = $auth->get('alias');
            ob_start();
            extract($user_data, EXTR_SKIP);
            include(SIMPLE_WP_MEMBERSHIP_PATH . 'views/edit.php');
            return ob_get_clean();
        }
        return SwpmUtils::_('You are not logged in.');
    }

    public function notices() {
        $message = SwpmTransfer::get_instance()->get('status');
        $succeeded = false;
        if (empty($message)) {
            return false;
        }
        if ($message['succeeded']) {
            echo "<div id='message' class='updated'>";
            $succeeded = true;
        } else {
            echo "<div id='message' class='error'>";
        }
        echo $message['message'];
        $extra = isset($message['extra']) ? $message['extra'] : array();
        if (is_string($extra)) {
            echo $extra;
        } else if (is_array($extra)) {
            echo '<ul>';
            foreach ($extra as $key => $value) {
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
            foreach ($post_types as $post_type => $post_type) {
                add_meta_box('swpm_sectionid', __('Simple WP Membership Protection', 'swpm'), array(&$this, 'inner_custom_box'), $post_type, 'advanced');
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
        $is_protected = SwpmProtection::get_instance()->is_protected($id);
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
            echo '<input type="checkbox" ' . (SwpmPermission::get_instance($level['id'])->is_permitted($id) ? "checked='checked'" : "") .
            ' name="swpm_protection_level[' . $level['id'] . ']" value="' . $level['id'] . '" /> ' . $level['alias'] . "<br/>";
        }
    }

    public function save_postdata($post_id) {
        global $wpdb;
        $post_type = filter_input(INPUT_POST, 'post_type');
        $swpm_protect_post = filter_input(INPUT_POST, 'swpm_protect_post');
        $swpm_noncename = filter_input(INPUT_POST, 'swpm_noncename');
        if (wp_is_post_revision($post_id)) {
            return;
        }
        if (!wp_verify_nonce($swpm_noncename, plugin_basename(__FILE__))) {
            return $post_id;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        if ('page' == $post_type) {
            if (!current_user_can('edit_page', $post_id)) {
                return $post_id;
            }
        } else {
            if (!current_user_can('edit_post', $post_id)) {
                return $post_id;
            }
        }
        if (empty($swpm_protect_post)) {
            return;
        }
        // OK, we're authenticated: we need to find and save the data
        $isprotected = ($swpm_protect_post == 2);
        $args = array('swpm_protection_level' => array(
                'filter' => FILTER_VALIDATE_INT,
                'flags' => FILTER_REQUIRE_ARRAY,
        ));
        $swpm_protection_level = filter_input_array(INPUT_POST, $args);
        $swpm_protection_level = $swpm_protection_level['swpm_protection_level'];
        if (!empty($post_type)) {
            if ($isprotected) {
                SwpmProtection::get_instance()->apply(array($post_id), $post_type);
            } else {
                SwpmProtection::get_instance()->remove(array($post_id), $post_type);
            }
            SwpmProtection::get_instance()->save();
            $query = "SELECT id FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE  id !=1 ";
            $level_ids = $wpdb->get_col($query);
            foreach ($level_ids as $level) {
                if (isset($swpm_protection_level[$level])) {
                    SwpmPermission::get_instance($level)->apply(array($post_id), $post_type)->save();
                } else {
                    SwpmPermission::get_instance($level)->remove(array($post_id), $post_type)->save();
                }
            }
        }
        $enable_protection = array();
        $enable_protection['protect'] = $swpm_protect_post;
        $enable_protection['level'] = $swpm_protection_level;
        return $enable_protection;
    }

    public function filter_comment($content) {
        $acl = SwpmAccessControl::get_instance();
        global $comment;
        return $acl->filter_post($comment->comment_post_ID, $content);
    }

    public function filter_content($content) {
        if (is_preview()) {
            return $content;
        }
        $acl = SwpmAccessControl::get_instance();
        global $post;
        return $acl->filter_post($post->ID, $content);
    }

    public function filter_moretag($more_link, $more_link_text = "More") {
        $moretag = SwpmSettings::get_instance()->get_value('enable-moretag');
        if (empty($moretag)) {
            return $more_link;
        }
        $acl = SwpmAccessControl::get_instance();
        global $post;
        return $acl->filter_post_with_moretag($post->ID, $more_link, $more_link_text);
    }

    public function init_hook() {
        $init_tasks = new SwpmInitTimeTasks();
        $init_tasks->do_init_tasks();
    }

    public function admin_library() {
        $this->common_library();
        wp_enqueue_script('password-strength-meter');
        wp_enqueue_script('swpm.password-meter', SIMPLE_WP_MEMBERSHIP_URL . '/js/swpm.password-meter.js');
        wp_enqueue_style('jquery.tools.dateinput', SIMPLE_WP_MEMBERSHIP_URL . '/css/jquery.tools.dateinput.css');
        wp_enqueue_script('jquery.tools', SIMPLE_WP_MEMBERSHIP_URL . '/js/jquery.tools18.min.js');
        $settings = array('statusChangeEmailHead' => SwpmSettings::get_instance()->get_value('account-change-email-subject'),
            'statusChangeEmailBody' => SwpmSettings::get_instance()->get_value('account-change-email-body'));
        wp_localize_script('swpm.password-meter', 'SwpmSettings', $settings);
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
        if ($succeeded) {
            return;
        }
        $is_free = SwpmSettings::get_instance()->get_value('enable-free-membership');
        $free_level = absint(SwpmSettings::get_instance()->get_value('free-membership-id'));
        $level = isset($atts['level']) ? absint($atts['level']) : ($is_free ? $free_level : null);
        return SwpmFrontRegistration::get_instance()->regigstration_ui($level);
    }

    public function menu() {
        $menu_parent_slug = 'simple_wp_membership';

        add_menu_page(__("WP Membership", 'swpm'), __("WP Membership", 'swpm'), 'manage_options', $menu_parent_slug, array(&$this, "admin_members"), 'dashicons-id');
        add_submenu_page($menu_parent_slug, __("Members", 'swpm'), __('Members', 'swpm'), 'manage_options', 'simple_wp_membership', array(&$this, "admin_members"));
        add_submenu_page($menu_parent_slug, __("Membership Levels", 'swpm'), __("Membership Levels", 'swpm'), 'manage_options', 'simple_wp_membership_levels', array(&$this, "admin_membership_levels"));
        add_submenu_page($menu_parent_slug, __("Settings", 'swpm'), __("Settings", 'swpm'), 'manage_options', 'simple_wp_membership_settings', array(&$this, "admin_settings"));
        add_submenu_page($menu_parent_slug, __("Payments", 'swpm'), __("Payments", 'swpm'), 'manage_options', 'simple_wp_membership_payments', array(&$this, "payments_menu"));
        add_submenu_page($menu_parent_slug, __("Add-ons", 'swpm'), __("Add-ons", 'swpm'), 'manage_options', 'simple_wp_membership_addons', array(&$this, "add_ons_menu"));

        do_action('swpm_after_main_admin_menu', $menu_parent_slug);

        $this->meta_box();
    }

    public function admin_membership_levels() {
        include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'classes/class.swpm-membership-levels.php');
        $levels = new SwpmMembershipLevels();
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
                $levels->delete();
            default:
                $levels->show();
                break;
        }
    }

    public function admin_members() {
        include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'classes/class.swpm-members.php');
        $members = new SwpmMembers();
        $action = filter_input(INPUT_GET, 'member_action');
        $action = empty($action) ? filter_input(INPUT_POST, 'action') : $action;
        $output = '';
        switch ($action) {
            case 'add':
            case 'edit':
                $members->process_form_request();
                break;
            case 'delete':
                $members->delete();
            default:
                $output = apply_filters('swpm_admin_member_menu_details_hook', $action, '');
                if (empty($output)) {
                    $output = $members->show();
                }
                $selected = $action;
                include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_members.php');
                break;
        }
    }

    public function admin_settings() {
        $current_tab = SwpmSettings::get_instance()->current_tab;
        switch ($current_tab) {
            case 6:
                include(SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_addon_settings.php');
                break;
            case 4:
                $link_for = filter_input(INPUT_POST, 'swpm_link_for', FILTER_SANITIZE_STRING);
                $member_id = filter_input(INPUT_POST, 'member_id', FILTER_SANITIZE_NUMBER_INT);
                $send_email = filter_input(INPUT_POST, 'swpm_reminder_email', FILTER_SANITIZE_NUMBER_INT);
                $links = SwpmUtils::get_registration_link($link_for, $send_email, $member_id);
                include(SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_tools_settings.php');
                break;
            case 2:
                include(SIMPLE_WP_MEMBERSHIP_PATH . 'views/payments/admin_payment_settings.php');
                break;
            default:
                include(SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_settings.php');
                break;
        }
    }

    public function payments_menu() {
        include(SIMPLE_WP_MEMBERSHIP_PATH . 'views/payments/admin_payments_page.php');
    }

    public function add_ons_menu() {
        include(SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_add_ons_page.php');
    }

    public function plugins_loaded() {
        //Runs when plugins_loaded action gets fired
        if (is_admin()) {
            //Check and run DB upgrade operation (if needed)
            if (get_option('swpm_db_version') != SIMPLE_WP_MEMBERSHIP_DB_VER) {
                include_once('class.swpm-installation.php');
                SwpmInstallation::run_safe_installer();
            }
        }
    }

    public static function activate() {
        wp_schedule_event(time(), 'daily', 'swpm_account_status_event');
        wp_schedule_event(time(), 'daily', 'swpm_delete_pending_account_event');
        include_once('class.swpm-installation.php');
        SwpmInstallation::run_safe_installer();
    }

    public function deactivate() {
        wp_clear_scheduled_hook('swpm_account_status_event');
        wp_clear_scheduled_hook('swpm_delete_pending_account_event');
    }

}
