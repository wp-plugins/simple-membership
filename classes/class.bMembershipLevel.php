<?php

/**
 * Description of BMembershipLevel
 *
 * @author nur
 */
class BMembershipLevel {

    private static $_instance = null;

    private function __construct() {
        ;
    }

    public static function get_instance() {
        self::$_instance = empty(self::$_instance) ? new BMembershipLevel() : self::$_instance;
        return self::$_instance;
    }

    public function create() {
        global $wpdb;
        $level = BTransfer::$default_level_fields;
        $form = new BLevelForm($level);
        if ($form->is_valid()) {
            $level_info = $form->get_sanitized();
            $wpdb->insert($wpdb->prefix . "swpm_membership_tbl", $level_info);
            $id = $wpdb->insert_id;
            $custom = apply_filters('swpm_admin_add_membership_level', array());
            $this->save_custom_fields($id, $custom);
            $message = array('succeeded' => true, 'message' => 'Membership Level Creation Successful.');
            BTransfer::get_instance()->set('status', $message);
            wp_redirect('admin.php?page=simple_wp_membership_levels');
            return;
        }
        $message = array('succeeded' => false, 'message' => 'Please correct the following:', 'extra' => $form->get_errors());
        BTransfer::get_instance()->set('status', $message);
    }

    public function edit($id) {
        global $wpdb;
        $query = "SELECT * FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE id = $id";
        $level = $wpdb->get_row($query, ARRAY_A);
        $form = new BLevelForm($level);
        if ($form->is_valid()) {
            $wpdb->update($wpdb->prefix . "swpm_membership_tbl", $form->get_sanitized(), array('id' => $id));
            //@todo meta table and collect all relevant info and pass as argument
            $custom = apply_filters('swpm_admin_edit_membership_level', array(), $id);
            $this->save_custom_fields($id, $custom);
            $message = array('succeeded' => true, 'message' => 'Updated Successfully.');
            BTransfer::get_instance()->set('status', $message);
            wp_redirect('admin.php?page=simple_wp_membership_levels');
        }
        $message = array('succeeded' => false, 'message' => 'Please correct the following:', 'extra' => $form->get_errors());
        BTransfer::get_instance()->set('status', $message);
    }
    private function save_custom_fields($level_id, $data){
        $custom_obj = BMembershipLevelCustom::get_instance_by_id($level_id);
        foreach ($data as $item){
            $custom_obj->set($item);
        }
    }
}
