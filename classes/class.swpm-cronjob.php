<?php
/**
 * Description of BCronJob
 *
 * @author nur
 */
class SwpmCronJob {
    public function __construct() {
        add_action('swpm_account_status_event', array(&$this, 'update_account_status'));
        add_action('swpm_delete_pending_account_event',array(&$this, 'delete_pending_account'));
    }
    
    public function update_account_status(){
        global $wpdb;
        for($counter = 0;; $counter += 100){
            $query = $wpdb->prepare("SELECT member_id, membership_level, subscription_starts, account_state
                    FROM {$wpdb->prefix}swpm_members_tbl LIMIT %d, 100", $counter);
            $results = $wpdb->get_results($query);
            if (empty($results)) {break;}
            $expired = array();
            foreach($results as $result){
                $timestamp = SwpmUtils::get_expiration_timestamp($result);
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
    
    public function delete_pending_account(){
        global $wpdb;
        $interval = SwpmSettings::get_instance()->get_value('delete-pending-account');
        if (empty($interval)) {return;}
        for($counter = 0;; $counter += 100){
            $query = $wpdb->prepare("SELECT member_id
                                     FROM 
                                        {$wpdb->prefix}swpm_members_tbl 
                                    WHERE account_state='pending' 
                                         AND subscription_starts < DATE_SUB(NOW(), INTERVAL %d MONTH) LIMIT %d, 100", 
                                    $interval, $counter);
            $results = $wpdb->get_results($query);
            if (empty($results)) {break;}
            $to_delete = array();
            foreach($results as $result){               
                    $to_delete[] = $result->member_id;                           
            }
            if (count($to_delete)>0){
                SwpmLog::log_simple_debug("Auto deleting pending account.", true);
                $query = "DELETE FROM {$wpdb->prefix}swpm_members_tbl 
                          WHERE member_id IN (" . implode(',', $to_delete) . ")";
                $wpdb->query($query);
            }
        }        
    }
}
