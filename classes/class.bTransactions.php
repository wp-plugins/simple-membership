<?php

/*
 * Provides some helpful functions to deal with the transactions
 */

class BTransactions {

    static function save_txn_record($ipn_data, $items = array()) {
        global $wpdb;
        
        $current_date = date("Y-m-d");
        $custom_var = BTransactions::parse_custom_var($ipn_data['custom']);
        
        $txn_data = array();
        $txn_data['email'] = $ipn_data['payer_email'];
        $txn_data['first_name'] = $ipn_data['first_name'];
        $txn_data['last_name'] = $ipn_data['last_name'];
        $txn_data['last_name'] = $ipn_data['last_name'];
        $txn_data['ip_address'] = $ipn_data['ip'];
        $txn_data['member_id'] = $ipn_data['swpm_id'];
        $txn_data['membership_level'] = $custom_var['subsc_ref'];

        $txn_data['txn_date'] = $current_date;
        $txn_data['txn_id'] = $ipn_data['txn_id'];
        $txn_data['subscr_id'] = $ipn_data['subscr_id'];
        $txn_data['reference'] = $custom_var['reference'];
        $txn_data['payment_amount'] = $ipn_data['mc_gross'];
        $txn_data['gateway'] = $ipn_data['gateway'];
        $txn_data['status'] = $ipn_data['status'];
        
        $wpdb->insert($wpdb->prefix . "swpm_payments_tbl", $txn_data);
        
    }

    static function parse_custom_var($custom) {
        $delimiter = "&";
        $customvariables = array();

        $namevaluecombos = explode($delimiter, $custom);
        foreach ($namevaluecombos as $keyval_unparsed) {
            $equalsignposition = strpos($keyval_unparsed, '=');
            if ($equalsignposition === false) {
                $customvariables[$keyval_unparsed] = '';
                continue;
            }
            $key = substr($keyval_unparsed, 0, $equalsignposition);
            $value = substr($keyval_unparsed, $equalsignposition + 1);
            $customvariables[$key] = $value;
        }
        
        return $customvariables;
    }

}