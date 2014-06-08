<?php
include_once('class.bProtectionBase.php');
class BPermission extends bProtectionBase{
    private static $_this = array();

    private function __construct($level_id) {
        $this->init($level_id);
    }

    public static function get_instance($level_id) {
        if (!isset(self::$_this[$level_id]))
            self::$_this[$level_id] = new BPermission($level_id);
        return self::$_this[$level_id];
    }

    public function is_permitted($id) {
        return $this->post_in_parent_categories($id) || $this->post_in_categories($id) || $this->in_posts($id) || $this->in_pages($id) || $this->in_attachments($id) || $this->in_custom_posts($id);
    }

    public function is_permitted_attachment($id) {
        return (($this->bitmap & 16) === 16) && $this->in_attachments($id);
    }

    public function is_permitted_custom_post($id) {
        return (($this->bitmap & 32) === 32) && $this->in_custom_posts($id);
    }

    public function is_permitted_category($id) {
        return (($this->bitmap & 1) === 1) && $this->in_categories($id);
    }

    public function is_post_in_permitted_category($post_id) {
        return (($this->bitmap & 1) === 1) && $this->post_in_categories($post_id);
    }

    public function is_permitted_post($id) {
        return (($this->bitmap & 4) === 4) && $this->in_posts($id);
    }

    public function is_permitted_page($id) {
        return (($this->bitmap & 8) === 8) && $this->in_pages($id);
    }

    public function is_permitted_comment($id) {
        return (($this->bitmap & 2) === 2) && $this->in_comments($id);
    }

    public function is_post_in_permitted_parent_category($post_id) {
        return (($this->bitmap & 1) === 1) && $this->post_in_parent_categories($post_id);
    }

    public function is_permitted_parent_category($id) {
        return (($this->bitmap & 1) === 1) && $this->in_parent_categories($id);
    }
}
