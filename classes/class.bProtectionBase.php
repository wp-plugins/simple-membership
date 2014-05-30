<?php
abstract class BProtectionBase{
	protected $bitmap;
	protected $posts;
	protected $pages;
	protected $comments;
	protected $categories;
	protected $attachments;
	protected $custom_posts;
	protected $details;
	private function __construct(){}
	protected function init($level_id){
        global $wpdb;
        $this->owning_level_id = $level_id;
        $query = "SELECT * FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE id =". $level_id;
        $result = $wpdb->get_row($query);
        $this->bitmap       = isset($result->permissions)? $result->permissions:0;
        $this->posts        = isset($result->post_list)? unserialize($result->post_list): array();
        $this->pages        = isset($result->page_list)? unserialize($result->page_list): array();
        $this->comments     = isset($result->comment_list)? unserialize($result->comment_list): array();
        $this->categories   = isset($result->category_list)? unserialize($result->category_list): array();
        $this->attachments  = isset($result->attachment_list)? unserialize($result->attachment_list): array();
        $this->custom_posts = isset($result->custom_post_list)? unserialize($result->custom_post_list): array();
        $this->details = (array)$result;
	}
    protected function in_posts($id){
        return in_array($id, (array)$this->posts);
    }
    protected function in_pages($id){
        return in_array($id, (array)$this->pages);
    }
    protected function in_attachments($id){
        return in_array($id, (array)$this->attachments);
    }
    protected function in_custom_posts($id){
        return in_array($id, (array)$this->custom_posts);
    }
    protected function in_comments($id){
        return in_array($id,(array) $this->comments);
    }
    protected function in_categories($id){
        return in_category((array)$this->categories, $id);
    }
    protected function in_parent_categories($id){
        $cats = get_the_category($id);
        $parents = array();
        foreach ($cats as $key => $cat) {
            $parents = array_merge($parents,explode(',',get_category_parents($cat->cat_ID,false,',')));
        }
        $parents = array_unique($parents);
        foreach($parents as $parent){
            if(empty($parent)) continue;
            if(in_array(get_cat_ID($parent), (array)$this->categories)) return true;
        }
        return false;
    }
	public function update_perms($post_id, $set, $type){
		$list  = null;
		$index = '';
		switch($type){
			case 'page':
			$list  = $this->pages;
			$index = 'page_list';
			break;
			case 'post':
			$list  = $this->posts;
			$index = 'post_list';
			break;
			case 'attachment':
			$list = $this->attachments;
			$index = 'attachment_list';
			break;
			default:
				if(in_array($type, get_post_types(array('public'   => true,'_builtin' => false)))){
					$list  = $this->custom_posts;
					$index = 'custom_post_list';
				}
			break;
		}
		if(!empty($index)){
			if($set){
				$list[] = $post_id;
				$list = array_unique($list);
			}
			else{
				foreach($list as $k=>$v)if($v===$post_id) unset($list[$k]);
			}
			$this->details[$index] = $list;
		}
		return $this;
	}
	public function save(){
		global $wpdb;
		$data = array();
		$list_type = array('page_list','post_list','attachment_list','custom_post_list','comment_list','category_list');
		foreach($this->details as $key=>$value){
			if($key == 'id') continue;
			if(is_serialized($value)||!in_array($key,$list_type))
				$data[$key] = $value;
			else
				$data[$key] = serialize($value);
		}
		$wpdb->update($wpdb->prefix. "swpm_membership_tbl", $data, array('id'=>$this->owning_level_id));
	}
	public function get($key){
		if(isset($this->details[$key]))
			return $this->details[$key];
		return "";
	}
}
