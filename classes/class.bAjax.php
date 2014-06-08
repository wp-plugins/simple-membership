<?php
/**
 * Description of BAjax
 *
 * @author nur
 */
class BAjax {
    public static function validate_email_ajax() {
        global $wpdb;
        $field_value = filter_input(INPUT_GET, 'fieldValue');
        $field_id = filter_input(INPUT_GET, 'fieldId');
        $table = $wpdb->prefix . "swpm_members_tbl";
        $email = esc_sql($field_value);
        $query = $wpdb->prepare("SELECT COUNT(*) FROM $table WHERE email = %s", $email);
        $exists = $wpdb->get_var($query) > 0;
        echo '[ "' . $field_id . (($exists) ? '",false, "&chi;&nbsp;Aready taken"]' : '",true, "&radic;&nbsp;Available"]');
        exit;
    }

    public static function validate_user_name_ajax() {
        global $wpdb;
        $field_value = filter_input(INPUT_GET, 'fieldValue');
        $field_id = filter_input(INPUT_GET, 'fieldId');
        $table = $wpdb->prefix . "swpm_members_tbl";
        $user = esc_sql($field_value);
        $query = $wpdb->prepare("SELECT COUNT(*) FROM $table WHERE user_name = %s", $user);
        $exists = $wpdb->get_var($query) > 0;
        echo '[ "' . $field_id . (($exists) ? '",false,"&chi;&nbsp;Aready taken"]' : '",true,"&radic;&nbsp;Available"]');
        exit;
    }
}
