<?php
include_once('class.bProtectionBase.php');

class BProtection extends BProtectionBase{
    private static $_this;
    private function __construct(){
		$this->init(1);
    }
    public static function get_instance(){
        self::$_this = empty(self::$_this)? (new BProtection()): self::$_this;
        return self::$_this;
    }
	public function is_protected($id){
		return $this->in_parent_categories($id)||$this->in_categories($id)
		       || $this->in_posts($id) ||$this->in_pages($id)
		       || $this->in_attachments($id) || $this->in_custom_posts($id);
	}
    public function is_protected_post($id){
        return $this->in_posts($id);
    }
    public function is_protected_page($id){
        return $this->in_pages($id);
    }

    public function is_protected_attachment($id){
        return $this->in_attachments($id);
    }
    public function is_protected_custom_post($id){
        return $this->in_custom_posts($id);
    }

    public function is_protected_comment($id){
        return $this->in_comments($id);
    }

    public function is_protected_category($id){
        return $this->in_categories( $id);
    }
    public function is_protected_parent_category($id){
		return $this->in_parent_categories($id);
    }
	public function set_protection($items = array()){
        global $wpdb;
        $result = $this->protection_list;
		foreach ($items as $type=>$list)
			$result[$type] = $result[$type] + $list;
		$wpdb->update($wpdb->prefix. "swpm_membership_tbl", $result, array('id'=>1));
        $this->protection_list = $result;
	}
    public function unset_protection($items = array()){
        global $wpdb;
        $result = $this->protection_list;
        //:todo
		foreach ($items as $type=>$list)
			$result[$type] = $result[$type] - $list;
		$wpdb->update($wpdb->prefix. "swpm_membership_tbl", $result, array('id'=>1));
        $this->protection_list = $result;

    }
}
