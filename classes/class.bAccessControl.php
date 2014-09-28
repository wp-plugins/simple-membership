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
                if($perms->is_permitted($id)) {return true;}
                $this->lastError = apply_filters ('swpm_restricted_post_msg', BUtils::_('You are not allowed to view this content')) ;
                return false;
            }
            $error_msg = BUtils::_( 'You need to login to view this content. ' ) . BSettings::get_instance()->get_login_link();
            $this->lastError = apply_filters('swpm_not_logged_in_post_msg', $error_msg);
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
                if($perms->is_permitted_comment($id)) {return true; }
                $this->lastError = apply_filters ('swpm_restricted_comment_msg', BUtils::_("You are not allowed to view this content") );
                return false;
            }
            $this->lastError = apply_filters('swpm_not_logged_in_comment_msg', BUtils::_("You need to login to view this content. ")
                    . BSettings::get_instance()->get_login_link());
            return false;
        }
        return true;
    }
    public function why(){
        return $this->lastError;
    }
    public function filter_post($id,$content){
        if(in_array($id, $this->moretags)) {return $content; }
        if($this->can_i_read_post($id)) {return $content; }
        $moretag = BSettings::get_instance()->get_value('enable-moretag');
        if (empty($moretag)){
            return $this->lastError;
        }
        $post = get_post($id);
        $post_segments = explode( '<!--more-->', $post->post_content);

        if (count($post_segments) >= 2){
            if (BAuth::get_instance()->is_logged_in()){
                $error_msg = '<div class="swpm-margin-top-10">' . BUtils::_("You do no have permission to view rest of the content") . '</div>';
                $this->lastError = apply_filters ('swpm_restricted_more_tag_msg', $error_msg);
            }
            else {
                $error_msg = '<div class="swpm-margin-top-10">' . BUtils::_("You need to login to view the rest of the content. ") . BSettings::get_instance()->get_login_link() . '</div>';
                $this->lastError = apply_filters('swpm_not_logged_in_more_tag_msg', $error_msg);
            }

            return do_shortcode($post_segments[0]) . $this->lastError;
        }

        return $this->lastError;
    }
    public function filter_comment($id,$content){
        if($this->can_i_read_comment($id)) { return $content; }
        return $this->lastError;
    }
    public function filter_post_with_moretag($id, $more_link, $more_link_text){
        $this->moretags[] = $id;
        if($this->can_i_read_post($id)) {
            return $more_link;
        }
        $msg = BUtils::_("You need to login to view the rest of the content. ") . BSettings::get_instance()->get_login_link();
        return apply_filters('swpm_not_logged_in_more_tag_msg', $msg);
    }
}
