<?php
/**
 * Description of BCronJob
 *
 * @author nur
 */
class BCronJob {
    public function __construct() {
        add_action('swpm_account_status_event', array(&$this, 'updateAccountStatus'));
    }
    public function updateAccountStatus(){
        global $wpdb;
        for($counter = 0;; $counter += 100){
            $query = $wpdb->prepare("SELECT member_id, membership_level, subscription_starts, account_state
                    FROM {$wpdb->prefix}swpm_members_tbl LIMIT %d, 100", $counter);
            $results = $wpdb->get_results($query);
            if (empty($results)) {break;}
            $expired = array();
            foreach($results as $result){
                $timestamp = BUtils::get_expiration_timestamp($result);
                if ($timestamp < time() && $result->account_state == 'active'){
                    $expired[] = $result->member_id;
                }
            }
            if (count($expired)>0){
                $query = "UPDATE {$wpdb->prefix}swpm_members_tbl 
                SET account_state='expired'  WHERE member_id IN (" . implode(',', $expired) . ")";
                $wpdb->query($query);
            }
        }
    }
}
