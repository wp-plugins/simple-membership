<?php

function swpm_handle_subsc_signup_stand_alone($ipn_data,$subsc_ref,$unique_ref,$swpm_id='')
{
    global $wpdb;
    $settings = SwpmSettings::get_instance();
    $members_table_name = $wpdb->prefix . "swpm_members_tbl";
    $membership_level_table = $wpdb->prefix . "swpm_membership_tbl";
    $membership_level = $subsc_ref;

    swpm_debug_log_subsc("swpm_handle_subsc_signup_stand_alone(). Custom value: ".$ipn_data['custom'],true);
    $custom_vars = parse_str($ipn_data['custom']);
    
    if(empty($swpm_id))
    {
        //Lets try to find an existing user profile for this payment
        $email = $ipn_data['payer_email'];
        $query_db = $wpdb->get_row($wpdb->prepare("SELECT * FROM $members_table_name WHERE email = %s", $email), OBJECT);
        if(!$query_db){//try to retrieve the member details based on the unique_ref
            swpm_debug_log_subsc("Could not find any record using the given email address (".$email."). Attempting to query database using the unique reference: ".$unique_ref,true);
            if(!empty($unique_ref)){
                    $query_db = $wpdb->get_row($wpdb->prepare("SELECT * FROM $members_table_name WHERE subscr_id = %s", $unique_ref), OBJECT);
                    $swpm_id = $query_db->member_id;
            }
            else{
                    swpm_debug_log_subsc("Unique reference is missing in the notification so we have to assume that this is not a payment for an existing member.",true);
            }
        }
        else
        {
            $swpm_id = $query_db->member_id;
            swpm_debug_log_subsc("Found a match in the member database. Member ID: ".$swpm_id,true);
        }
    }

    if (!empty($swpm_id))
    {
        //This is payment from an existing member/user. Update the existing member account
        swpm_debug_log_subsc("Modifying the existing membership profile... Member ID: ".$swpm_id,true);
        
        //Upgrade the member account        
        $account_state = 'active';//This is renewal or upgrade of a previously active account. So the status should be set to active
        $subscription_starts = (date ("Y-m-d"));
        $subscr_id = $unique_ref;
       
        $resultset = $wpdb->get_row($wpdb->prepare("SELECT * FROM $members_table_name where member_id=%d", $swpm_id), OBJECT);
        if(!$resultset){
            swpm_debug_log_subsc("ERROR! Could not find a member account record for the given Member ID: ".$swpm_id,false);
            return;
        }
        $old_membership_level = $resultset->membership_level;

        swpm_debug_log_subsc("Upgrading the current membership level (".$old_membership_level.") of this member to the newly paid level (".$membership_level.")", true);
        $updatedb = $wpdb->prepare("UPDATE $members_table_name SET account_state=%s, membership_level=%d,subscription_starts=%s,subscr_id=%s WHERE member_id=%d", $account_state, $membership_level, $subscription_starts, $subscr_id, $swpm_id);
        $results = $wpdb->query($updatedb);
        do_action('swpm_membership_changed', array('member_id'=>$swpm_id, 'from_level'=>$old_membership_level, 'to_level'=>$membership_level));

        //Set Email details for the account upgrade notification
        $email = $ipn_data['payer_email'];
        $subject = $settings->get_value('upgrade-complete-mail-subject');
        if (empty($subject)){
            $subject = "Member Account Upgraded";
        }
        $body = $settings->get_value('upgrade-complete-mail-body');
        if (empty($body)){
            $body = "Your account has been upgraded successfully";
        }
        $from_address = get_option('admin_email');
        $login_link = $settings->get_value('login-page-url');

        $tags1 = array("{first_name}","{last_name}","{user_name}","{login_link}");
        $vals1 = array($resultset->first_name,$resultset->last_name,$resultset->user_name,$login_link);
        $email_body = str_replace($tags1,$vals1,$body);
        $headers = 'From: '.$from_address . "\r\n";
    }// End of existing user account upgrade
    else
    {
        // create new member account
        $default_account_status = $settings->get_value('default-account-status', 'active');
        
        $data = array();
        $data['user_name'] ='';
        $data['password'] = '';

        $data['first_name'] = $ipn_data['first_name'];
        $data['last_name'] = $ipn_data['last_name'];
        $data['email'] = $ipn_data['payer_email'];
        $data['membership_level'] = $membership_level;
        $data['subscr_id'] = $unique_ref;
        $data['gender'] = 'not specified';

        swpm_debug_log_subsc("Creating new member account. Membership level ID: ".$membership_level,true);

        $data['address_street'] = $ipn_data['address_street'];
        $data['address_city'] = $ipn_data['address_city'];
        $data['address_state'] = $ipn_data['address_state'];
        $data['address_zipcode'] = $ipn_data['address_zip'];
        $data['country'] = $ipn_data['address_country'];
        $data['member_since']  = $data['subscription_starts'] = $data['last_accessed'] = date ("Y-m-d");
        $data['account_state'] = $default_account_status;
        $reg_code = uniqid();
        $md5_code = md5($reg_code);
        $data['reg_code'] = $md5_code;
        $data['referrer'] = $data['extra_info'] = $data['txn_id'] = '';
        $data['subscr_id']= $subscr_id;
        $data['last_accessed_from_ip'] = isset($user_ip) ? $user_ip : '';//Save the users IP address

        $wpdb->insert($members_table_name,  $data);//Create the member record
        $results = $wpdb->get_row($wpdb->prepare("SELECT * FROM $members_table_name where subscr_id=%s and reg_code=%s",$subscr_id, $md5_code), OBJECT);
        $id = $results->member_id; //Alternatively use $wpdb->insert_id;
        if(empty($id)){
            swpm_debug_log_subsc("Error! Failed to insert a new member record. This request will fail.",false);
            return;
        }
        
        $separator='?';
        $url = $settings->get_value('registration-page-url');
        if(strpos($url,'?')!==false){$separator='&';}

        $reg_url = $url.$separator.'member_id='.$id.'&code='.$md5_code;
        swpm_debug_log_subsc("Member signup URL: ".$reg_url,true);

        $subject = $settings->get_value('reg-prompt-complete-mail-subject');
        if (empty($subject)){
            $subject = "Please complete your registration";
        }
        $body = $settings->get_value('reg-prompt-complete-mail-body');
        if (empty($body)){
            $body = "Please use the following link to complete your registration. \n {reg_link}";
        }
        $from_address = $settings->get_value('email-from');

        $tags = array("{first_name}","{last_name}","{reg_link}");
        $vals = array($data['first_name'],$data['last_name'],$reg_url);
        $email_body = str_replace($tags,$vals,$body);
        $headers = 'From: '.$from_address . "\r\n";
    }

    wp_mail($email,$subject,$email_body,$headers);
    swpm_debug_log_subsc("Member signup/upgrade completion email successfully sent to: ".$email,true);
}

function swpm_handle_subsc_cancel_stand_alone($ipn_data,$refund=false)
{
    if($refund)
    {
        $subscr_id = $ipn_data['parent_txn_id'];
        swpm_debug_log_subsc("Refund notification check - check if a member account needs to be deactivated... parent_txn_id: ".$ipn_data['parent_txn_id'],true);
    }
    else
    {
        $subscr_id = $ipn_data['subscr_id'];
    }

    if(empty($subscr_id)){
        swpm_debug_log_subsc("No subscr_id associated with this transaction. Nothing to do here.",true);
        return;
    }
    
    global $wpdb;
    $members_table_name = $wpdb->prefix . "swpm_members_tbl";

    swpm_debug_log_subsc("Retrieving member account from the database. Subscr_id: ".$subscr_id, true);
    $resultset = $wpdb->get_row($wpdb->prepare("SELECT * FROM $members_table_name where subscr_id=%s", $subscr_id), OBJECT);
    if($resultset)
    {
        //Deactivate this account as it is a refund or cancellation
        $member_id = $resultset->member_id;
        $account_state = 'inactive';
        $updatedb = $wpdb->prepare("UPDATE $members_table_name SET account_state=%s WHERE member_id=%s", $account_state, $member_id);
        $resultset = $wpdb->query($updatedb);
        swpm_debug_log_subsc("Subscription cancellation received! Member account deactivated. Member ID: ".$member_id, true);
    }
    else
    {
    	swpm_debug_log_subsc("No member found for the given subscriber ID: ".$subscr_id,false);
    	return;
    }
}

function swpm_update_member_subscription_start_date_if_applicable($ipn_data)
{
    global $wpdb;
    $members_table_name = $wpdb->prefix . "swpm_members_tbl";
    $membership_level_table = $wpdb->prefix . "swpm_membership_tbl";
    $email = $ipn_data['payer_email'];
    $subscr_id = $ipn_data['subscr_id'];
    $account_state = SwpmSettings::get_instance()->get_value('default-account-status', 'active');
    swpm_debug_log_subsc("Updating subscription start date if applicable for this subscription payment. Subscriber ID: ".$subscr_id." Email: ".$email,true);

    //We can also query using the email address
    $query_db = $wpdb->get_row($wpdb->prepare("SELECT * FROM $members_table_name WHERE subscr_id = %s", $subscr_id), OBJECT);
    if($query_db){
        $swpm_id = $query_db->member_id;
        $current_primary_level = $query_db->membership_level;
        swpm_debug_log_subsc("Found a record in the member table. The Member ID of the account to check is: ".$swpm_id." Membership Level: ".$current_primary_level,true);

        $subscription_starts = (date ("Y-m-d"));

        $updatedb = $wpdb->prepare("UPDATE $members_table_name SET account_state=%s,subscription_starts=%s WHERE member_id=%d", $account_state, $subscription_starts, $swpm_id);
        $resultset = $wpdb->query($updatedb);
        swpm_debug_log_subsc("Updated the member profile with current date as the subscription start date.",true);                
    }else{
        swpm_debug_log_subsc("Did not find a record in the members table for subscriber ID: ".$subscr_id,true);
    }
}

function swpm_debug_log_subsc($message,$success,$end=false) 
{
    $settings = SwpmSettings::get_instance();
    $debug_enabled = $settings->get_value('enable-debug');
    if (empty($debug_enabled)) {//Debug is not enabled
        return;
    }

    $debug_log_file_name = SIMPLE_WP_MEMBERSHIP_PATH . 'log.txt';

    // Timestamp
    $text = '['.date('m/d/Y g:i A').'] - '.(($success)?'SUCCESS :':'FAILURE :').$message. "\n";
    if ($end) {
    	$text .= "\n------------------------------------------------------------------\n\n";
    }
    // Write to log
    $fp=fopen($debug_log_file_name,'a');
    fwrite($fp, $text );
    fclose($fp);  // close file
}
