<?php

/**
 * Description of BMembershipLevel
 *
 * @author nur
 */
class SwpmMembershipLevel {
    const NO_EXPIRY  = 0;
    const DAYS       = 1;
    const WEEKS      = 2; 
    const MONTHS     = 3;
    const YEARS      = 4;
    const FIXED_DATE = 5;
    
    private static $_instance = null;

    private function __construct() {
        ;
    }

    public static function get_instance() {
        self::$_instance = empty(self::$_instance) ? new SwpmMembershipLevel() : self::$_instance;
        return self::$_instance;
    }

    public function create() {
        global $wpdb;
        $level = SwpmTransfer::$default_level_fields;
        $form = new SwpmLevelForm($level);
        if ($form->is_valid()) {
            $level_info = $form->get_sanitized();
            $wpdb->insert($wpdb->prefix . "swpm_membership_tbl", $level_info);
            $id = $wpdb->insert_id;
            $custom = apply_filters('swpm_admin_add_membership_level', array());
            $this->save_custom_fields($id, $custom);
            $message = array('succeeded' => true, 'message' => SwpmUtils::_('Membership Level Creation Successful.'));
            SwpmTransfer::get_instance()->set('status', $message);
            wp_redirect('admin.php?page=simple_wp_membership_levels');
            return;
        }
        $message = array('succeeded' => false, 'message' => SwpmUtils::_('Please correct the following:'), 'extra' => $form->get_errors());
        SwpmTransfer::get_instance()->set('status', $message);
    }

    public function edit($id) {
        global $wpdb;
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE id = %d", $id);
        $level = $wpdb->get_row($query, ARRAY_A);
        $form = new SwpmLevelForm($level);
        if ($form->is_valid()) {
            $wpdb->update($wpdb->prefix . "swpm_membership_tbl", $form->get_sanitized(), array('id' => $id));
            //@todo meta table and collect all relevant info and pass as argument
            $custom = apply_filters('swpm_admin_edit_membership_level', array(), $id);
            $this->save_custom_fields($id, $custom);
            $message = array('succeeded' => true, 'message' => SwpmUtils::_('Updated Successfully.'));
            SwpmTransfer::get_instance()->set('status', $message);
            wp_redirect('admin.php?page=simple_wp_membership_levels');
            return;
        }
        $message = array('succeeded' => false, 'message' => SwpmUtils::_('Please correct the following:'), 'extra' => $form->get_errors());
        SwpmTransfer::get_instance()->set('status', $message);
    }
    private function save_custom_fields($level_id, $data){
        $custom_obj = SwpmMembershipLevelCustom::get_instance_by_id($level_id);
        foreach ($data as $item){
            $custom_obj->set($item);
        }
    }
}
