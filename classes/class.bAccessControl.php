<?php
class BAccessControl {
    private $lastError;
    private $moretags;
    private static $_this;
    private function __construct(){
        $this->lastError = '';
        $this->moretags  = array();
    }
    public static function get_instance(){
        self::$_this = empty(self::$_this)? new BAccessControl():self::$_this;
        return self::$_this;
    }

    public function can_i_read_post($id){
        $this->lastError = '';
		$protected = BProtection::get_instance(); 
		if ($protected->is_protected($id)){
			$auth = BAuth::get_instance();
			if($auth->is_logged_in()){
				$perms = BPermission::get_instance($auth->get('membership_level'));
				if($perms->is_permitted($id))return true;
				$this->lastError ='You are not allowed to view this content' ;
				return false;							
			}
			$this->lastError ='You need to login to view this content. ' 
                                . BSettings::get_instance()->get_login_link();
			return false;			
		}
		return true;
    }
    public function can_i_read_comment($id){
        $this->lastError = '';
		$protected = BProtection::get_instance(); 
		if ($protected->is_protected_comment($id)){
			$auth = BAuth::get_instance();
			if($auth->is_logged_in()){
				$perms = BPermission::get_instance($auth->get('membership_level'));
				if($perms->is_permitted_comment($id))return true;
				$this->lastError ="You are not allowed to view this content";
				return false;							
			}
			$this->lastError ="You need to login to view this content. "
                                . BSettings::get_instance()->get_login_link();
			return false;			
		}
		return true;		
    }
    public function why(){
		return $this->lastError;		
    }
    public function filter_post($id,$content){
        //if(in_array($id, $this->moretags)) return $content;
        if($this->can_i_read_post($id)) return $content;
        return $this->lastError;
    }
    public function filter_comment($id,$content){        
        if($this->can_i_read_comment($id)) return $content;
        return $this->lastError;
    }
    public function filter_post_with_moretag($id, $content){
		$this->moretags[] = $id;
		if($this->can_i_read_post($id)) return $content;
		return $this->lastError;  
    }
}
