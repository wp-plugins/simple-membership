<?php
class BForm{
	protected $fields;
	protected $op;
	protected $errors;
	protected $sanitized;
	public function __construct($fields){
		$this->fields    = $fields;;
		$this->sanitized = array();
		foreach($fields as $key=>$value)
			$this->$key();
	}
	protected function user_name(){
		global $wpdb;
		if(!empty($this->fields['user_name'])) return;
		if(!isset($_POST['user_name'])||empty($_POST['user_name'])){
			$this->errors['user_name'] = "User name is required";
			return;
		}
		$saned = sanitize_text_field($_POST['user_name']);
		$query = "SELECT count(member_id) FROM {$wpdb->prefix}swpm_members_tbl WHERE user_name= '" .
				 strip_tags($saned). "'";
		$result = $wpdb->get_var($query);
		if($result>0){
			if($saned != $this->fields['user_name']){
				$this->errors['user_name'] = "User name already exists.";
				return;
			}
		}
		$this->sanitized['user_name'] = $saned;
	}
	protected function first_name(){
		if(isset($_POST['first_name']))
			$this->sanitized['first_name'] = sanitize_text_field($_POST['first_name']);
	}
	protected function last_name(){
		if(isset($_POST['last_name']))
			$this->sanitized['last_name'] = sanitize_text_field($_POST['last_name']);
	}
	protected function password(){
		if(empty($this->fields['password'])&&empty($_POST['password'])){
			$this->errors['password'] = "Password is required";
			return;
		}
		if(!empty($_POST['password'])){
			$saned    = sanitize_text_field($_POST['password']);
			$saned_re = sanitize_text_field($_POST['password_re']);
			if($saned != $saned_re)$this->errors['password'] = "Password mismatch";
			include_once(ABSPATH . WPINC . '/class-phpass.php');
		    $wp_hasher = new PasswordHash(8, TRUE);
		    $password = $wp_hasher->HashPassword(trim($_POST['password'])); //should use $saned??
			$this->sanitized['plain_password'] = $_POST['password'];
			$this->sanitized['password'] = $password;
		}
	}
	protected function email(){
		global $wpdb;
		if(!empty($this->fields['email'])) return ;
		if(!isset($_POST['email'])||empty($_POST['email'])){
			$this->errors['email'] = "Email is required";
			return;
		}
		if(!is_email($_POST['email'])){
			$this->errors['email'] = "Email is invalid";
			return;
		}
		$saned = sanitize_email($_POST['email']);
		$query = "SELECT count(member_id) FROM {$wpdb->prefix}swpm_members_tbl WHERE email= '" .
				 strip_tags($saned). "'";
                $member_id = filter_input(INPUT_GET, 'member_id', FILTER_SANITIZE_NUMBER_INT);
                if (!empty($member_id)) {$query .= ' AND member_id !=' . $member_id;}
		$result = $wpdb->get_var($query);
		if($result>0){
			if($saned != $this->fields['email']){
				$this->errors['email'] = "Email is already used.";
				return;
			}
		}
		$this->sanitized['email'] = $saned;
	}
	protected function phone(){
		if(isset($_POST['phone'])&&!empty($_POST['phone'])){
		    $saned = wp_kses($_POST['phone'], array());
			$this->sanitized['phone'] = $saned;
			return;
			if ( strlen( $saned ) > 9 && preg_match( '/^((\+)?[1-9]{1,2})?([-\s\.])?((\(\d{1,4}\))|\d{1,4})(([-\s\.])?[0-9]{1,12}){1,2}$/', $saned ) )
				$this->sanitized['phone'] = $saned;
			else
				$this->errors['phone'] = "Phone number is invalid";
		}
	}
	protected function address_street(){
		if(isset($_POST['address_street'])&&!empty($_POST['address_street']))
		$this->sanitized['address_street'] = wp_kses($_POST['address_street'], array());
	}
	protected function address_city(){
		if(isset($_POST['address_city'])&&!empty($_POST['address_city']))
		$this->sanitized['address_city'] = wp_kses($_POST['address_city'], array());
	}
	protected function address_state(){
		if(isset($_POST['address_state'])&&!empty($_POST['address_state']))
		$this->sanitized['address_state'] = wp_kses($_POST['address_state'], array());
	}
	protected function address_zipcode(){
		if(isset($_POST['address_zipcode'])&&!empty($_POST['address_zipcode']))
		$this->sanitized['address_zipcode'] = wp_kses($_POST['address_zipcode'], array());
	}
	protected function country(){
		if(isset($_POST['country'])&&!empty($_POST['country']))
		$this->sanitized['country'] = wp_kses($_POST['country'], array());
	}
	protected function company_name(){
		if(isset($_POST['company_name']))
		$this->sanitized['company_name'] = sanitize_text_field($_POST['company_name']);
	}
	protected function member_since(){
		if(isset($_POST['member_since'])){
			$saned = sanitize_text_field($_POST['member_since']);
			if(preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $saned))
				$this->sanitized['member_since'] = $saned;
			else
				$this->errors['member_since'] = "Member since field is invalid";
		}
	}
	protected function subscription_starts(){
		if(isset($_POST['subscription_starts'])){
			$saned = sanitize_text_field($_POST['subscription_starts']);
			if(preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $saned))
				$this->sanitized['subscription_starts'] = $saned;
			else
				$this->errors['subscription_starts'] = "Subscription starts field is invalid";
		}
	}
	protected function gender(){
		if(isset($_POST['gender'])){
			$saned = sanitize_text_field($_POST['gender']);
			if(in_array($saned, array('male','female','not specified')))
				$this->sanitized['gender'] = $saned;
			else
				$this->errors['gender'] = "Gender field is invalid";
		}
	}
	protected function account_state(){
		if(isset($_POST['account_state'])){
			$saned = sanitize_text_field($_POST['account_state']);
			if(in_array($saned, array('active','pending','inactive','expired')))
				$this->sanitized['account_state'] = $saned;
			else
				$this->errors['account_state'] = "Account state field is invalid";
		}
	}
	protected function membership_level(){
		if(isset($_POST['membership_level'])){
			$this->sanitized['membership_level'] = absint($_POST['membership_level']);
		}
	}
	protected function password_re(){}
	protected function last_accessed(){}
	protected function last_accessed_from_ip(){}
	protected function referrer(){}
	protected function extra_info(){}
	protected function reg_code(){}
	protected function txn_id(){}
	protected function subscr_id(){}
	protected function flags(){}
	protected function more_membership_levels(){}
	protected function initial_membership_level(){}
	protected function home_page(){}
	protected function notes(){}
	protected function profile_image(){}
	protected function expiry_1st(){}
	protected function expiry_2nd(){}
	protected function member_id(){}
	public function is_valid(){
		return count($this->errors)<1;
	}
	public function get_sanitized(){
		return $this->sanitized;
	}
	public function get_errors(){
		return $this->errors;
	}
}
