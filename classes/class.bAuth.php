<?php

class BAuth {

    public $protected;
    public $permitted;
    private $isLoggedIn;
    private $lastStatusMsg;
    private static $_this;
    public $userData;

    private function __construct() {
        $this->isLoggedIn = false;
        $this->userData = null;
        $this->protected = BProtection::get_instance();
    }
    private function init(){
        $valid = $this->validate();
        //Blog::log_simple_debug("init:". ($valid? "valid": "invalid"), true);
        if (!$valid){
            $this->authenticate();
        }
    }
    public static function get_instance() {
        if (empty(self::$_this)){
            self::$_this = new BAuth();
            self::$_this->init();
        }
        return self::$_this;
    }

    private function authenticate($user = null, $pass = null) {
        global $wpdb;
        $swpm_password = empty($pass)?filter_input(INPUT_POST, 'swpm_password') : $pass;
        $swpm_user_name = empty($user)? apply_filters('swpm_user_name', filter_input(INPUT_POST, 'swpm_user_name')) : $user;
        //Blog::log_simple_debug("Authenticate:" . $swpm_user_name, true);
        if (!empty($swpm_user_name) && !empty($swpm_password)) {
            $user = sanitize_user($swpm_user_name);
            $pass = trim($swpm_password);
            $query = "SELECT * FROM " . $wpdb->prefix . "swpm_members_tbl WHERE user_name = %s";
            $userData = $wpdb->get_row($wpdb->prepare($query, $user));
            $this->userData = $userData;
            if (!$userData) {
                $this->isLoggedIn = false;
                $this->userData = null;
                $this->lastStatusMsg = BUtils::_("User Not Found.");
                return false;
            }
            $check = $this->check_password($pass, $userData->password);
            if (!$check) {
                $this->isLoggedIn = false;
                $this->userData = null;
                $this->lastStatusMsg = BUtils::_("Password Empty or Invalid.");
                return false;
            }
            if ($this->check_constraints()) {
                $rememberme = filter_input(INPUT_POST, 'rememberme');
                $remember = empty($rememberme) ? false : true;
                $this->set_cookie($remember);
                $this->isLoggedIn = true;
                $this->lastStatusMsg = "Logged In.";
                Blog::log_simple_debug("swpm_login action.", true);
                do_action('swpm_login', $user, $pass, $remember);
                return true;
            }
        }
        return false;
    }

    private function check_constraints() {
        if (empty($this->userData)){
            return false;
        }
        $enable_expired_login = BSettings::get_instance()->get_value('enable-expired-account-login', '');
        
        $can_login = true;
        if( $this->userData->account_state == 'inactive'){
            $this->lastStatusMsg = BUtils::_('Account is inactive.');
            $can_login = false;
        }
        else if( $this->userData->account_state == 'pending'){
            $this->lastStatusMsg = BUtils::_('Account is pending.');
            $can_login = false;
        }        
        else if( ($this->userData->account_state == 'expired') && empty($enable_expired_login)  ){
            $this->lastStatusMsg = BUtils::_('Account has expired.');
            $can_login = false;
        }        

        if(!$can_login){
            $this->isLoggedIn = false;
            $this->userData = null;
            return false;            
        }
        
        if (BUtils::is_subscription_expired($this->userData)){
            if ($this->userData->account_state == 'active'){
                global $wpdb;
                $wpdb->update( 
                    $wpdb->prefix . 'swpm_members_tbl', 
                    array( 'account_state' => 'expired'), 
                    array( 'member_id' => $this->userData->member_id ), 
                    array( '%s'), 
                    array( '%d' ) 
                );
            }
            if (empty($enable_expired_login)){
                $this->lastStatusMsg = BUtils::_('Account has expired.');
                $this->isLoggedIn = false;
                $this->userData = null;
                return false;
            }
        }
        
        $this->permitted = BPermission::get_instance($this->userData->membership_level);        
        $this->lastStatusMsg = BUtils::_("You are logged in as:") . $this->userData->user_name;
        $this->isLoggedIn = true;
        return true;
    }

    private function check_password($password, $hash) {
        global $wp_hasher;
        if (empty($password)){
            return false;
        }
        if (empty($wp_hasher)) {
            require_once( ABSPATH . 'wp-includes/class-phpass.php');
            $wp_hasher = new PasswordHash(8, TRUE);
        }
        return $wp_hasher->CheckPassword($password, $hash);
    }
    public function match_password($password){
        if (!$this->is_logged_in()) {return false;} 
        return $this->check_password($password, $this->get('password'));
    }
    public function login($user, $pass, $remember = '', $secure = '') {
        Blog::log_simple_debug("login",true);
        if ($this->isLoggedIn){
            return;
        }
        if ($this->authenticate($user, $pass) && $this->validate()) {
            $this->set_cookie($remember, $secure); 
        } else {
            $this->isLoggedIn = false;
            $this->userData = null;
        }
        return $this->lastStatusMsg;
    }

    public function logout() {
        if (!$this->isLoggedIn){
            return;
        }
        setcookie(SIMPLE_WP_MEMBERSHIP_AUTH, ' ', time() - YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
        setcookie(SIMPLE_WP_MEMBERSHIP_SEC_AUTH, ' ', time() - YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
        $this->userData = null;
        $this->isLoggedIn = false;
        $this->lastStatusMsg = BUtils::_("Logged Out Successfully.");
        do_action('swpm_logout');
    }

    private function set_cookie($remember = '', $secure = '') {
        if ($remember){
            $expiration = time() + 1209600; // 14 days
            $expire = $expiration  + 43200; // 12 hours grace period 
        }
        else{
            $expiration = time() + 172800; // 2 days.
            $expire = $expiration;//The minimum cookie expiration should be at least couple of days.
        }
        
        $expiration_timestamp = BUtils::get_expiration_timestamp($this->userData);
        $enable_expired_login = BSettings::get_instance()->get_value('enable-expired-account-login', '');
        // make sure cookie doesn't live beyond account expiration date.
        // but if expired account login is enabled then ignore if account is expired
        $expiration = empty($enable_expired_login)? min ($expiration,$expiration_timestamp) : $expiration;
        $pass_frag = substr($this->userData->password, 8, 4);
        $scheme = 'auth';
        if (!$secure){
            $secure = is_ssl();
        }
        $key = BAuth::b_hash($this->userData->user_name . $pass_frag . '|' . $expiration, $scheme);
        $hash = hash_hmac('md5', $this->userData->user_name . '|' . $expiration, $key);
        $auth_cookie = $this->userData->user_name . '|' . $expiration . '|' . $hash;
        $auth_cookie_name = $secure ? SIMPLE_WP_MEMBERSHIP_SEC_AUTH : SIMPLE_WP_MEMBERSHIP_AUTH;
        //setcookie($auth_cookie_name, $auth_cookie, $expire, PLUGINS_COOKIE_PATH, COOKIE_DOMAIN, $secure, true);
        setcookie($auth_cookie_name, $auth_cookie, $expire, COOKIEPATH, COOKIE_DOMAIN, $secure, true);
    }

    private function validate() {
        $auth_cookie_name = is_ssl() ? SIMPLE_WP_MEMBERSHIP_SEC_AUTH : SIMPLE_WP_MEMBERSHIP_AUTH;
        if (!isset($_COOKIE[$auth_cookie_name]) || empty($_COOKIE[$auth_cookie_name])){
            return false;
        }
        $cookie_elements = explode('|', $_COOKIE[$auth_cookie_name]);
        if (count($cookie_elements) != 3){
            return false;
        }
        Blog::log_simple_debug("validate:" . $_COOKIE[$auth_cookie_name],true);
        list($username, $expiration, $hmac) = $cookie_elements;
        $expired = $expiration;
        // Allow a grace period for POST and AJAX requests
        if (defined('DOING_AJAX') || 'POST' == $_SERVER['REQUEST_METHOD']){
            $expired += HOUR_IN_SECONDS;
        }
        // Quick check to see if an honest cookie has expired
        if ($expired < time()) {
            $this->lastStatusMsg = BUtils::_("Session Expired."); //do_action('auth_cookie_expired', $cookie_elements);
            return false;
        }
        Blog::log_simple_debug("validate:Session Expired",true);
        global $wpdb;
        $query = " SELECT * FROM " . $wpdb->prefix . "swpm_members_tbl WHERE user_name = %s";
        $user = $wpdb->get_row($wpdb->prepare($query, $username));
        if (empty($user)) {
            $this->lastStatusMsg = BUtils::_("Invalid User Name");
            return false;
        }
        Blog::log_simple_debug("validate:Invalid User Name:" . serialize($user),true);
        $pass_frag = substr($user->password, 8, 4);
        $key = BAuth::b_hash($username . $pass_frag . '|' . $expiration);
        $hash = hash_hmac('md5', $username . '|' . $expiration, $key);
        if ($hmac != $hash) {
            $this->lastStatusMsg = BUtils::_("Sorry! Something went wrong");
            return false;
        }
        Blog::log_simple_debug("validate:bad hash",true);
        if ($expiration < time()){
            $GLOBALS['login_grace_period'] = 1;
        }
        $this->userData = $user;
        return $this->check_constraints();
    }

    public static function b_hash($data, $scheme = 'auth') {
        $salt = wp_salt($scheme) . 'j4H!B3TA,J4nIn4.';
        return hash_hmac('md5', $data, $salt);
    }

    public function is_logged_in() {
        return $this->isLoggedIn;
    }

    public function get($key, $default = "") {
        if (isset($this->userData->$key)){
            return $this->userData->$key;
        }
        if (isset($this->permitted->$key)){
            return $this->permitted->$key;
        }
        if (!empty($this->permitted)){
            return $this->permitted->get($key, $default);
        }
        return $default;
    }

    public function get_message() {
        return $this->lastStatusMsg;
    }
    public function get_expire_date(){
        if ($this->isLoggedIn){
            return BUtils::get_expire_date(
                    $this->get('subscription_starts'),
                    $this->get('subscription_period'),
                    $this->get('subscription_duration_type'));
        }
        return "";
    }
    public function delete(){
        if (!$this->is_logged_in()) {return ;}
        $user_name = $this->get('user_name');
        $user_id   = $this->get('member_id');
        wp_clear_auth_cookie();
        $this->logout();        
        BMembers::delete_swpm_user_by_id($user_id);
        BMembers::delete_wp_user($user_name);
    }
    
    public function reload_user_data(){
        if (!$this->is_logged_in()) {return ;}
        global $wpdb;
        $query = "SELECT * FROM " . $wpdb->prefix . "swpm_members_tbl WHERE member_id = %d";
        $this->userData = $wpdb->get_row($wpdb->prepare($query, $this->userData->member_id));        
        
    }
    public function is_expired_account(){
        // should be called after logging in.        
        if (!$this->is_logged_in()) {return null;}
        return $this->get('account_state') === 'expired';
    }
}
