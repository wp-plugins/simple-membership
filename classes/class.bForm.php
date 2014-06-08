<?php

class BForm {

    protected $fields;
    protected $op;
    protected $errors;
    protected $sanitized;

    public function __construct($fields) {
        $this->fields = $fields;
        ;
        $this->sanitized = array();
        foreach ($fields as $key => $value){
            $this->$key();
        }
    }

    protected function user_name() {
        global $wpdb;
        if (!empty($this->fields['user_name'])){return;}
        $user_name = filter_input(INPUT_POST, 'user_name',FILTER_SANITIZE_STRING);
        if (empty($user_name)) {
            $this->errors['user_name'] = 'User name is required';
            return;
        }
        $saned = sanitize_text_field($user_name);
        $query = "SELECT count(member_id) FROM {$wpdb->prefix}swpm_members_tbl WHERE user_name= '" .
                strip_tags($saned) . "'";
        $result = $wpdb->get_var($query);
        if ($result > 0) {
            if ($saned != $this->fields['user_name']) {
                $this->errors['user_name'] = 'User name already exists.';
                return;
            }
        }
        $this->sanitized['user_name'] = $saned;
    }

    protected function first_name() {
        $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
        if (empty($first_name)) {return;}
        $this->sanitized['first_name'] = sanitize_text_field($first_name);
    }

    protected function last_name() {
        $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
        if (empty($last_name)) {return;}
        $this->sanitized['last_name'] = sanitize_text_field($last_name);
    }

    protected function password() {
        $password = filter_input(INPUT_POST, 'password',FILTER_UNSAFE_RAW);
        $password_re = filter_input(INPUT_POST, 'password_re',FILTER_UNSAFE_RAW);
        if (empty($this->fields['password']) && empty($password)) {
            $this->errors['password'] = 'Password is required';
            return;
        }
        if (!empty($password)) {
            $saned = sanitize_text_field($password);
            $saned_re = sanitize_text_field($password_re);
            if ($saned != $saned_re){
                $this->errors['password'] = 'Password mismatch';
            }
            include_once(ABSPATH . WPINC . '/class-phpass.php');
            $wp_hasher = new PasswordHash(8, TRUE);
            $this->sanitized['plain_password'] = $password;
            $this->sanitized['password'] = $wp_hasher->HashPassword(trim($password)); //should use $saned??;
        }
    }

    protected function email() {
        global $wpdb;
        if (!empty($this->fields['email'])){
            return;
        }
        $email = filter_input(INPUT_POST, 'email', FILTER_UNSAFE_RAW);
        if (empty($email)) {
            $this->errors['email'] = 'Email is required';
            return;
        }
        if (!is_email($email)) {
            $this->errors['email'] = 'Email is invalid';
            return;
        }
        $saned = sanitize_email($email);
        $query = "SELECT count(member_id) FROM {$wpdb->prefix}swpm_members_tbl WHERE email= '" .
                strip_tags($saned) . "'";
        $member_id = filter_input(INPUT_GET, 'member_id', FILTER_SANITIZE_NUMBER_INT);
        if (!empty($member_id)) {
            $query .= ' AND member_id !=' . $member_id;
        }
        $result = $wpdb->get_var($query);
        if ($result > 0) {
            if ($saned != $this->fields['email']) {
                $this->errors['email'] = 'Email is already used.';
                return;
            }
        }
        $this->sanitized['email'] = $saned;
    }

    protected function phone() {
        $phone = filter_input(INPUT_POST, 'phone', FILTER_UNSAFE_RAW);
        if (empty($phone)) {return;}
        $saned = wp_kses($phone, array());
        $this->sanitized['phone'] = $saned;
        if (strlen($saned) > 9 && preg_match('/^((\+)?[1-9]{1,2})?([-\s\.])?((\(\d{1,4}\))|\d{1,4})(([-\s\.])?[0-9]{1,12}){1,2}$/', $saned)){
            $this->sanitized['phone'] = $saned;
        }
        else{
            $this->errors['phone'] = 'Phone number is invalid';
        }
    }

    protected function address_street() {
        $address_street = filter_input(INPUT_POST, 'address_street', FILTER_SANITIZE_STRING);
        if (empty($address_street)) { return;}
        $this->sanitized['address_street'] = wp_kses($address_street, array());
    }

    protected function address_city() {
        $address_city = filter_input(INPUT_POST, 'address_city', FILTER_SANITIZE_STRING);
        if (empty($address_city)){ return; }
        $this->sanitized['address_city'] = wp_kses($address_city, array());
    }

    protected function address_state() {
        $address_state = filter_input(INPUT_POST, 'address_state', FILTER_SANITIZE_STRING);
        if (empty($address_state))  {return;}
        $this->sanitized['address_state'] = wp_kses($address_state, array());
    }

    protected function address_zipcode() {
        $address_zipcode = filter_input(INPUT_POST, 'address_zipcode', FILTER_UNSAFE_RAW);
        if (empty($address_zipcode)){ return;}
        $this->sanitized['address_zipcode'] = wp_kses($address_zipcode, array());
    }

    protected function country() {
        $country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING);
        if (empty($country)){ return;}
        $this->sanitized['country'] = wp_kses($country, array());
    }

    protected function company_name() {
        $company_name = filter_input(INPUT_POST, 'company_name', FILTER_SANITIZE_STRING);
        $this->sanitized['company_name'] = $company_name;
    }

    protected function member_since() {
        $member_since = filter_input(INPUT_POST, 'member_since', FILTER_UNSAFE_RAW);
        if (empty($member_since)) {return;}
        if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $member_since)){
            $this->sanitized['member_since'] =  sanitize_text_field($member_since);
            return;
        }
        $this->errors['member_since'] = 'Member since field is invalid';

    }

    protected function subscription_starts() {
        $subscription_starts = filter_input(INPUT_POST, 'subscription_starts', FILTER_SANITIZE_STRING);
        if(empty($subscription_starts)) {return ;}
        if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $subscription_starts)){
            $this->sanitized['subscription_starts'] =  sanitize_text_field($subscription_starts);
            return;
        }
        $this->errors['subscription_starts'] = 'Subscription starts field is invalid';
    }

    protected function gender() {
        $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);
        if(empty($gender)) {return;}
        if (in_array($gender, array('male', 'female', 'not specified'))){
            $this->sanitized['gender'] = $gender;
        }
        else{
            $this->errors['gender'] = 'Gender field is invalid';
        }
    }

    protected function account_state() {
        $account_state = filter_input(INPUT_POST, 'account_state', FILTER_SANITIZE_STRING);
        if(empty($account_state)) {return;}
        if (in_array($account_state, array('active', 'pending', 'inactive', 'expired'))){
            $this->sanitized['account_state'] = $account_state;
        }
        else{
            $this->errors['account_state'] = 'Account state field is invalid';
        }
    }

    protected function membership_level() {
        $membership_level = filter_input(INPUT_POST, 'membership_level', FILTER_SANITIZE_NUMBER_INT);
        if (empty($membership_level)) {return;}
        $this->sanitized['membership_level'] = $membership_level;
    }

    protected function password_re() {

    }

    protected function last_accessed() {

    }

    protected function last_accessed_from_ip() {

    }

    protected function referrer() {

    }

    protected function extra_info() {

    }

    protected function reg_code() {

    }

    protected function txn_id() {

    }

    protected function subscr_id() {

    }

    protected function flags() {

    }

    protected function more_membership_levels() {

    }

    protected function initial_membership_level() {

    }

    protected function home_page() {

    }

    protected function notes() {

    }

    protected function profile_image() {

    }

    protected function expiry_1st() {

    }

    protected function expiry_2nd() {

    }

    protected function member_id() {

    }

    public function is_valid() {
        return count($this->errors) < 1;
    }

    public function get_sanitized() {
        return $this->sanitized;
    }

    public function get_errors() {
        return $this->errors;
    }

}
