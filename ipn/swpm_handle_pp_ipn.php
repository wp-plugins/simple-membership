<?php

include_once('swpm_handle_subsc_ipn.php');

class swpm_paypal_ipn_handler {

    var $last_error;                 // holds the last error encountered
    var $ipn_log = false;                    // bool: log IPN results to text file?
    var $ipn_log_file;               // filename of the IPN log
    var $ipn_response;               // holds the IPN response from paypal
    var $ipn_data = array();         // array contains the POST values for IPN
    var $fields = array();           // array holds the fields to submit to paypal
    var $sandbox_mode = false;

    function swpm_paypal_ipn_handler()
    {
        $this->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
      	$this->last_error = '';
      	$this->ipn_log_file = 'ipn_handle_debug_swpm.log';
      	$this->ipn_response = '';
    }

    function swpm_validate_and_create_membership()
    {
        // Check Product Name , Price , Currency , Receivers email ,
        $error_msg = "";

        // Read the IPN and validate
        $gross_total = $this->ipn_data['mc_gross'];
        $transaction_type = $this->ipn_data['txn_type'];
        $txn_id = $this->ipn_data['txn_id'];        
    	$payment_status = $this->ipn_data['payment_status'];
        
        //Check payment status
    	if (!empty($payment_status))
    	{
            if ($payment_status == "Denied") {
                $this->debug_log("Payment status for this transaction is DENIED. You denied the transaction... most likely a cancellation of an eCheque. Nothing to do here.", false);
                return false;
            }
            if ($payment_status == "Canceled_Reversal") {
                $this->debug_log("This is a dispute closed notification in your favour. The plugin will not do anyting.", false);
                return true;
            }
            if ($payment_status != "Completed" && $payment_status != "Processed" && $payment_status != "Refunded" && $payment_status != "Reversed")
            {
                $error_msg .= 'Funds have not been cleared yet. Transaction will be processed when the funds clear!';
                $this->debug_log($error_msg,false);
                return false;
            }
    	}

        //Check txn type
        if ($transaction_type == "new_case") {
            $this->debug_log('This is a dispute case. Nothing to do here.', true);
            return true;
        }
        
        $custom = $this->ipn_data['custom'];
        $delimiter = "&";
        $customvariables = array();
        $namevaluecombos = explode($delimiter, $custom);
        foreach ($namevaluecombos as $keyval_unparsed)
        {
            $equalsignposition = strpos($keyval_unparsed, '=');
            if ($equalsignposition === false)
            {
                $customvariables[$keyval_unparsed] = '';
                continue;
            }
            $key = substr($keyval_unparsed, 0, $equalsignposition);
            $value = substr($keyval_unparsed, $equalsignposition + 1);
            $customvariables[$key] = $value;
        }
        
        //Handle refunds
        if ($gross_total < 0)
        {
            // This is a refund or reversal
            $this->debug_log('This is a refund notification. Refund amount: '.$gross_total,true);
            swpm_handle_subsc_cancel_stand_alone($this->ipn_data,true);            
            return true;
        }
        if (isset($this->ipn_data['reason_code']) && $this->ipn_data['reason_code'] == 'refund'){
            $this->debug_log('This is a refund notification. Refund amount: '.$gross_total,true);
            swpm_handle_subsc_cancel_stand_alone($this->ipn_data,true);            
            return true;            
        }

        if (($transaction_type == "subscr_signup"))
        {
            $this->debug_log('Subscription signup IPN received... nothing to do here(handled by the subscription IPN handler)',true);
            // Code to handle the signup IPN for subscription
            $subsc_ref = $customvariables['subsc_ref'];

            if (!empty($subsc_ref))
            {
                $this->debug_log('swpm integration is being used... creating member account...',true);
                $swpm_id = $customvariables['swpm_id'];
                swpm_handle_subsc_signup_stand_alone($this->ipn_data,$subsc_ref,$this->ipn_data['subscr_id'],$swpm_id);
                //Handle customized subscription signup
            }
            return true;
        }
        else if (($transaction_type == "subscr_cancel") || ($transaction_type == "subscr_eot") || ($transaction_type == "subscr_failed"))
        {
            // Code to handle the IPN for subscription cancellation
            swpm_handle_subsc_cancel_stand_alone($this->ipn_data);
            $this->debug_log('Subscription cancellation IPN received... nothing to do here(handled by the subscription IPN handler)',true);
            return true;
        }
        else
        {
            $cart_items = array();
            $this->debug_log('Transaction Type: Buy Now/Subscribe',true);
            $item_number = $this->ipn_data['item_number'];
            $item_name = $this->ipn_data['item_name'];
            $quantity = $this->ipn_data['quantity'];
            $mc_gross = $this->ipn_data['mc_gross'];
            $mc_currency = $this->ipn_data['mc_currency'];

            $current_item = array(
            'item_number' => $item_number,
            'item_name' => $item_name,
            'quantity' => $quantity,
            'mc_gross' => $mc_gross,
            'mc_currency' => $mc_currency,
            );

            array_push($cart_items, $current_item);
        }

        $counter = 0;
        foreach ($cart_items as $current_cart_item)
        {
            $cart_item_data_num = $current_cart_item['item_number'];
            $cart_item_data_name = trim($current_cart_item['item_name']);
            $cart_item_data_quantity = $current_cart_item['quantity'];
            $cart_item_data_total = $current_cart_item['mc_gross'];
            $cart_item_data_currency = $current_cart_item['mc_currency'];
            if(empty($cart_item_data_quantity))
            {
                    $cart_item_data_quantity = 1;
            }
            $this->debug_log('Item Number: '.$cart_item_data_num,true);
            $this->debug_log('Item Name: '.$cart_item_data_name,true);
            $this->debug_log('Item Quantity: '.$cart_item_data_quantity,true);
            $this->debug_log('Item Total: '.$cart_item_data_total,true);
            $this->debug_log('Item Currency: '.$cart_item_data_currency,true);


            //*** Handle Membership Payment ***
            //--------------------------------------------------------------------------------------
            // ========= Need to find the (level ID) in the custom variable ============
            $subsc_ref = $customvariables['subsc_ref'];//Membership level ID
            $this->debug_log('Membership payment paid for membership level ID: '.$subsc_ref,true);
            if (!empty($subsc_ref))
            {
                $swpm_id = "";
                if(isset($customvariables['swpm_id'])){
                    $swpm_id = $customvariables['swpm_id'];
                }
                if ($transaction_type == "web_accept")
                {
                    $this->debug_log('swpm integration is being used... creating member account...',true);
                    swpm_handle_subsc_signup_stand_alone($this->ipn_data,$subsc_ref,$this->ipn_data['txn_id'],$swpm_id);
                }
                else if($transaction_type == "subscr_payment"){
                    //swpm_update_member_subscription_start_date_if_applicable($this->ipn_data);
                }
            }
            else
            {
                $this->debug_log('Membership level ID is missing in the payment notification! Cannot process this notification.',false);
            }
            //== End of Membership payment handling ==
            $counter++;
        }

        /*** Do Post payment operation and cleanup ***/
        //Save the transaction data
        $this->debug_log('Saving transaction data to the database table.', true);
        $this->ipn_data['gateway'] = 'paypal';
        $this->ipn_data['status'] = $this->ipn_data['payment_status'];
        SwpmTransactions::save_txn_record($this->ipn_data, $cart_items);
        $this->debug_log('Transaction data saved.', true);
        
        do_action('swpm_paypal_ipn_processed', $this->ipn_data);
        return true;
    }

    function swpm_validate_ipn()
    {
      // parse the paypal URL
      $url_parsed=parse_url($this->paypal_url);

      // generate the post string from the _POST vars aswell as load the _POST vars into an arry
      $post_string = '';
      foreach ($_POST as $field=>$value) {
         $this->ipn_data["$field"] = $value;
         $post_string .= $field.'='.urlencode(stripslashes($value)).'&';
      }

      $this->post_string = $post_string;
      $this->debug_log('Post string : '. $this->post_string,true);

      $post_string.="cmd=_notify-validate"; // append ipn command

      // open the connection to paypal
      if($this->sandbox_mode){//connect to PayPal sandbox
	      $uri = 'ssl://'.$url_parsed['host'];
	      $port = '443';
	      $fp = fsockopen($uri,$port,$err_num,$err_str,30);
      }
      else{//connect to live PayPal site using standard approach
      	$fp = fsockopen($url_parsed['host'],"80",$err_num,$err_str,30);
      }

      if(!$fp)
      {
         // could not open the connection.  If loggin is on, the error message
         // will be in the log.
         $this->debug_log('Connection to '.$url_parsed['host']." failed.fsockopen error no. $errnum: $errstr",false);
         return false;

      }
      else
      {
         // Post the data back to paypal
         fputs($fp, "POST $url_parsed[path] HTTP/1.1\r\n");
         fputs($fp, "Host: $url_parsed[host]\r\n");
         fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
         fputs($fp, "Content-length: ".strlen($post_string)."\r\n");
         fputs($fp, "Connection: close\r\n\r\n");
         fputs($fp, $post_string . "\r\n\r\n");

         // loop through the response from the server and append to variable
         while(!feof($fp)) {
            $this->ipn_response .= fgets($fp, 1024);
         }

         fclose($fp); // close connection

         $this->debug_log('Connection to '.$url_parsed['host'].' successfuly completed.',true);
      }

      //if (eregi("VERIFIED",$this->ipn_response))
      if (strpos($this->ipn_response, "VERIFIED") !== false)
      {
         // Valid IPN transaction.
         $this->debug_log('IPN successfully verified.',true);
         return true;

      }
      else
      {
         // Invalid IPN transaction.  Check the log for details.
         $this->debug_log('IPN validation failed.',false);
         return false;
      }
   }

    function debug_log($message,$success,$end=false)
    {
        SwpmLog::log_simple_debug($message, $success, $end);
    }
}

// Start of IPN handling (script execution)

$ipn_handler_instance = new swpm_paypal_ipn_handler();

$settings = SwpmSettings::get_instance();
$debug_enabled = $settings->get_value('enable-debug');
if(!empty($debug_enabled))//debug is enabled in the system
{
	$debug_log = "log.txt"; // Debug log file name
	echo 'Debug logging is enabled. Check the '.$debug_log.' file for debug output.';
	$ipn_handler_instance->ipn_log = true;
	$ipn_handler_instance->ipn_log_file = $debug_log;
	if(empty($_POST))
	{
            $ipn_handler_instance->debug_log('This debug line was generated because you entered the URL of the ipn handling script in the browser.',true,true);
            exit;
	}
}

$sandbox_enabled = $settings->get_value('enable-sandbox-testing');
if(!empty($sandbox_enabled)) // Sandbox testing enabled
{
    $ipn_handler_instance->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
    $ipn_handler_instance->sandbox_mode = true;
}

$ipn_handler_instance->debug_log('Paypal Class Initiated by '.$_SERVER['REMOTE_ADDR'],true);

// Validate the IPN
if ($ipn_handler_instance->swpm_validate_ipn())
{
    $ipn_handler_instance->debug_log('Creating product Information to send.',true);

    if(!$ipn_handler_instance->swpm_validate_and_create_membership()){
        $ipn_handler_instance->debug_log('IPN product validation failed.',false);
    }
}
$ipn_handler_instance->debug_log('Paypal class finished.',true,true);
