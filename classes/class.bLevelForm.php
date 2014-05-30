<?php
class BLevelForm{
	protected $fields;
	protected $op;
	protected $errors;
	protected $sanitized;
	public function __construct($fields){
		$this->fields    = $fields;
		$this->sanitized = array();
		foreach($fields as $key=>$value)
			$this->$key();
	}

	protected function id(){}
	protected function alias(){
        $this->sanitized['alias'] = sanitize_text_field($_POST['alias']);	
    }
	protected function role(){
        $this->sanitized['role'] = sanitize_text_field($_POST['role']);	
    }
	protected function permissions(){
        $this->sanitized['permissions'] = 63;
    }
	protected function subscription_period(){
        if($_POST['subscript_duration_type'] == 0){
            $this->sanitized['subscription_period'] = 0;
            return;           
        }
        
        if(empty($_POST['subscription_period'])){
            $this->errors['subscription_period'] = "Subscriptoin duration must be > 0.";
            return;
        }         
        $this->sanitized['subscription_period'] = absint($_POST['subscription_period']);	        
    }
	protected function subscription_unit(){ 
        if($_POST['subscript_duration_type'] == 0){
            $this->sanitized['subscription_unit'] = null;
            return;           
        }         
        $this->sanitized['subscription_unit'] = sanitize_text_field($_POST['subscription_unit']);	                
    }
	protected function loginredirect_page(){}
	protected function category_list(){}
	protected function page_list(){}
	protected function post_list(){}
	protected function comment_list(){}
	protected function attachment_list(){}
	protected function custom_post_list(){}
	protected function disable_bookmark_list(){}
	protected function options(){}
	protected function campaign_name(){}    
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
