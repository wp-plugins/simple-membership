<?php

/**
 * BMemberUtils
 *
 * @author nur
 */
class SwpmMemberUtils {

    public static function is_member_logged_in() {
        $auth = SwpmAuth::get_instance();
        if ($auth->is_logged_in()) {
            return true;
        } else {
            return false;
        }
    }

    public static function get_logged_in_members_id() {
        $auth = SwpmAuth::get_instance();
        if (!$auth->is_logged_in()) {
            return SwpmUtils::_("User is not logged in.");
        }
        return $auth->get('member_id');
    }

    public static function get_logged_in_members_username() {
        $auth = SwpmAuth::get_instance();
        if (!$auth->is_logged_in()) {
            return SwpmUtils::_("User is not logged in.");
        }
        return $auth->get('user_name');
    }
    
    public static function get_logged_in_members_level() {
        $auth = SwpmAuth::get_instance();
        if (!$auth->is_logged_in()) {
            return SwpmUtils::_("User is not logged in.");
        }
        return $auth->get('membership_level');
    }

    public static function get_logged_in_members_level_name() {
        $auth = SwpmAuth::get_instance();
        if ($auth->is_logged_in()) {
            return $auth->get('alias');
        }
        return SwpmUtils::_("User is not logged in.");
    }

}
