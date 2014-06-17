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
        Blog::log_simple_debug("init:". ($valid? "valid": "invalid"), true);
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

    private function authenticate() {
        global $wpdb;
        $swpm_user_name = filter_input(INPUT_POST, 'swpm_user_name');
        $swpm_password = filter_input(INPUT_POST, 'swpm_password');
        Blog::log_simple_debug("Authenticate:" . $swpm_user_name, true);
        if (!empty($swpm_user_name) && !empty($swpm_password)) {
            $user = sanitize_user($swpm_user_name);
            $pass = trim($swpm_password);
            $query = " SELECT * FROM " . $wpdb->prefix . "swpm_members_tbl";
            $query.= " WHERE user_name = '" . $user . "'";
            $userData = $wpdb->get_row($query);
            $this->userData = $userData;
            if (!$userData) {
                $this->isLoggedIn = false;
                $this->userData = null;
                $this->lastStatusMsg = "User Not Found.";
                return false;
            }
            $check = $this->check_password($pass, $userData->password);
            if (!$check) {
                $this->isLoggedIn = false;
                $this->userData = null;
                $this->lastStatusMsg = "Password Empty or Invalid.";
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
        $permission = BPermission::get_instance($this->userData->membership_level);
        $valid = true;
        if ($this->userData->account_state != 'active') {
            $this->lastStatusMsg = 'Account is inactive.';
            $valid = false;
        }
        if (!$valid) {
            $this->isLoggedIn = false;
            $this->userData = null;
            return false;
        }
        //:todo check if account expired and update db if it did.
        $this->userData->permitted = $permission;
        $this->lastStatusMsg = "You are logged in as:" . $this->userData->user_name;
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
        setcookie(SIMPLE_WP_MEMBERSHIP_AUTH, ' ', time() - YEAR_IN_SECONDS, "/", COOKIE_DOMAIN);
        setcookie(SIMPLE_WP_MEMBERSHIP_SEC_AUTH, ' ', time() - YEAR_IN_SECONDS, "/", COOKIE_DOMAIN);
        $this->userData = null;
        $this->isLoggedIn = false;
        $this->lastStatusMsg = "Logged Out Successfully.";
        do_action('swpm_logout');
    }

    private function set_cookie($remember = '', $secure = '') {
        if ($remember){
            $expire = time() + 1209600;
        }
        else{
            $expire = time() + 172800;
        }
        $pass_frag = substr($this->userData->password, 8, 4);
        $scheme = 'auth';
        if (!$secure){
            $secure = is_ssl();
        }
        $key = BAuth::b_hash($this->userData->user_name . $pass_frag . '|' . $expire, $scheme);
        $hash = hash_hmac('md5', $this->userData->user_name . '|' . $expire, $key);
        $auth_cookie = $this->userData->user_name . '|' . $expire . '|' . $hash;
        $auth_cookie_name = $secure ? SIMPLE_WP_MEMBERSHIP_SEC_AUTH : SIMPLE_WP_MEMBERSHIP_AUTH;
        //setcookie($auth_cookie_name, $auth_cookie, $expire, PLUGINS_COOKIE_PATH, COOKIE_DOMAIN, $secure, true);
        setcookie($auth_cookie_name, $auth_cookie, $expire, "/", COOKIE_DOMAIN, $secure, true);
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
            $this->lastStatusMsg = "Session Expired."; //do_action('auth_cookie_expired', $cookie_elements);
            return false;
        }
        Blog::log_simple_debug("validate:Session Expired",true);
        global $wpdb;
        $query = " SELECT * FROM " . $wpdb->prefix . "swpm_members_tbl";
        $query.= " WHERE user_name = '" . $username . "'";
        $user = $wpdb->get_row($query);
        if (empty($user)) {
            $this->lastStatusMsg = "Invalid User Name";
            return false;
        }
        Blog::log_simple_debug("validate:Invalid User Name:" . serialize($user),true);
        $pass_frag = substr($user->password, 8, 4);
        $key = BAuth::b_hash($username . $pass_frag . '|' . $expiration);
        $hash = hash_hmac('md5', $username . '|' . $expiration, $key);
        if ($hmac != $hash) {
            $this->lastStatusMsg = "Bad Cookie Hash";
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
        if (isset($this->userData->permitted->$key)){
            return $this->userData->permitted->$key;
        }
        return $default;
    }

    public function get_message() {
        return $this->lastStatusMsg;
    }

}
