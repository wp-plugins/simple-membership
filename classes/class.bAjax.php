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
        $member_id = filter_input(INPUT_GET, 'member_id');
        if (!is_email($field_value)){
            echo '[ "' . $field_id .  '",false, "'.BUtils::_('Invalid Email Address').'" ]' ;
            exit;            
        }
        $table = $wpdb->prefix . "swpm_members_tbl";
        $query = $wpdb->prepare("SELECT member_id FROM $table WHERE email = %s", $field_value);
        $db_id = $wpdb->get_var($query) ;
        $exists = ($db_id > 0) && $db_id != $member_id;
        echo '[ "' . $field_id . (($exists) ? '",false, "&chi;&nbsp;'.BUtils::_('Aready taken').'"]' : '",true, "&radic;&nbsp;Available"]');
        exit;
    }

    public static function validate_user_name_ajax() {
        global $wpdb;
        $field_value = filter_input(INPUT_GET, 'fieldValue');
        $field_id = filter_input(INPUT_GET, 'fieldId');
        $table = $wpdb->prefix . "swpm_members_tbl";
        $query = $wpdb->prepare("SELECT COUNT(*) FROM $table WHERE user_name = %s", $field_value);
        $exists = $wpdb->get_var($query) > 0;
        echo '[ "' . $field_id . (($exists) ? '",false,"&chi;&nbsp;'. BUtils::_('Aready taken'). '"]' :
            '",true,"&radic;&nbsp;'.BUtils::_('Available'). '"]');
        exit;
    }
}
