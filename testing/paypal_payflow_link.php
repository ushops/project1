<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  PayPal	
  Released under the Apache 2.0 License - Paypal
  @aurthor DriveDev (rock mutchler)
*/

  class paypal_payflow_link {
    var $code, $title, $description, $enabled;

// class constructor
    function paypal_payflow_link() {
      global $order;

      $this->signature = 'paypal|paypal_payflow_link|1.2|2.2';

      $this->code = 'paypal_payflow_link';
      $this->title = MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_TEXT_TITLE;
      $this->public_title = MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_TEXT_PUBLIC_TITLE;
      $this->description = MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_STATUS == 'True') ? true : false);
      $this->SECURETOKENID;
      $this->SECURETOKEN;
      $this->tmpOrder;

      if ((int)MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();

    }

// class methods
    function update_status() {
      global $order;
		$this->tmpOrder = $order;
      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_ZONE > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_ZONE . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->delivery['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }
      }
    }

    function javascript_validation() {
      return false;
    }

    function selection() {
      $selection = array('id' => $this->code,
                         'module' => $this->public_title);
      return $selection;
    }

    function pre_confirmation_check() {
      if((MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_TRANSACTION_METHOD == 'Sale')){
      	$trxType = 'S'; 
      }else{
      	$trxType = 'A';
      }
      	$order = $this->tmpOrder;
      	$nvpStr = 'USER=' . (tep_not_null(MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_USERNAME) ? MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_USERNAME : MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_VENDOR)
      		. '&VENDOR='.MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_VENDOR
      		. '&PARTNER='.MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_PARTNER
      		. '&PWD='.MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_PASSWORD;
		$nvpStr .= "&TRXTYPE=" . $trxType . "&CURRENCY=" . $order->info['currency']
			. "&TENDER=C" //C = Credit Card
			. "&AMT=" . $order->info['total']
			. "&VERBOSITY=HIGH" 
			. "&BILLTOFIRSTNAME=" . $order->customer['firstname']
			. "&BILLTOLASTNAME=" . $order->customer['lastname']
			. "&BILLTOSTREET=" . $order->customer['street_address'] 
			. "&BILLTOSTREET2=" . $order->customer['suburb']
			. "&BILLTOCITY=" . $order->customer['city']
			. "&BILLTOSTATE=" . $order->customer['state']
			. "&BILLTOZIP=" . $order->customer['postcode']
			. "&BILLTOPHONENUM=" . $order->customer['telephone']
			. "&EMAIL=" . $order->customer['email_address']
			. "&SHIPTOFIRSTNAME=" . $order->delivery['firstname']
			. "&SHIPTOLASTNAME=" . $order->delivery['lastname']
			. "&SHIPTOSTREET=" . $order->delivery['street_address'] 
			. "&SHIPTOSTREET2=" . $order->delivery['suburb']
			. "&SHIPTOCITY=" . $order->delivery['city']
			. "&SHIPTOSTATE=" . $order->delivery['state']
			. "&SHIPTOZIP=" . $order->delivery['postcode'];
		$this->token = $this->getToken($nvpStr);
		$_SESSION['tk']['SECURETOKEN'] = $this->SECURETOKEN;
		$_SESSION['tk']['SECURETOKENID'] = $this->SECURETOKENID;
		return false;
    }

    function confirmation() {
    	global $order;
      	$confirmation = array();
        $types_array = array();
        $today = getdate();
        $months_array = array();
        for ($i=1; $i<13; $i++) {
          $months_array[] = array('id' => sprintf('%02d', $i), 'text' => strftime('%B',mktime(0,0,0,$i,1,2000)));
        }

        $year_valid_from_array = array();
        for ($i=$today['year']-10; $i < $today['year']+1; $i++) {
          $year_valid_from_array[] = array('id' => strftime('%y',mktime(0,0,0,1,1,$i)), 'text' => strftime('%Y',mktime(0,0,0,1,1,$i)));
        }

        $year_expires_array = array();
        for ($i=$today['year']; $i < $today['year']+10; $i++) {
          $year_expires_array[] = array('id' => strftime('%y',mktime(0,0,0,1,1,$i)), 'text' => strftime('%Y',mktime(0,0,0,1,1,$i)));
        }
		$confirmation['fields'] = array(array('title' => '', 'field' => tep_draw_hidden_field('SECURETOKEN', $this->SECURETOKEN)),
									array('title' => '', 'field' => tep_draw_hidden_field('SECURETOKENID',$this->SECURETOKENID)),
									array('title' => '', 'field' => tep_draw_hidden_field('MODE', 'test')));
										
      if (MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_TRANSACTION_SERVER == 'Live') {
	  	$confirmation['iframe']['src']='paypal_payflow_HS.php?mode=live&SECURETOKEN='.$this->SECURETOKEN.'&SECURETOKENID='.$this->SECURETOKENID;
      }else{
      	$confirmation['iframe']['src']='paypal_payflow_HS.php?mode=test&SECURETOKEN='.$this->SECURETOKEN.'&SECURETOKENID='.$this->SECURETOKENID;
      }
	  $confirmation['iframe']['width'] = 490;
	  $confirmation['iframe']['height'] = 565;
	 
      return $confirmation;
    }

    function process_button() {
      return false;
    }

    function before_process() {
      global $HTTP_POST_VARS, $order, $sendto;
		// nada
		return false;
    }

    function getToken($apiStr) {
      global $HTTP_POST_VARS, $order, $sendto;		
        if (MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_TRANSACTION_SERVER == 'Live') {
          $api_url = 'https://payflowpro.paypal.com';
        } else {
          $api_url = 'https://pilot-payflowpro.paypal.com';
        }
		$this->SECURETOKEN = md5(trim(time() . date('l jS \of F Y h:i:s A')));
        $params = array(
        				'SECURETOKENID' => $this->SECURETOKEN,
        				'CREATESECURETOKEN' => 'Y'
        );
        $apiStr .= '&';
        foreach ($params as $key => $value){
        	$apiStr .= $key .'='.$value.'&';
        }
        $apiStr = substr($apiStr, 0, strlen($apiStr)-1);
	    // setting the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $api_url);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
	
		// turning off the server and peer verification(TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
	
		// setting the NVP $my_api_str as POST FIELD to curl
		curl_setopt($ch, CURLOPT_POSTFIELDS, $apiStr);
	
		// getting response from server
		$httpResponse = curl_exec($ch);
		$respArr = explode('&',trim($httpResponse));
		foreach ($respArr as $line){
			list($key, $value) = explode('=', $line);
			if(in_array($key, array('SECURETOKENID','SECURETOKEN'))){
				$this->$key = $value;
			}
		}
		
        return $httpResponse;    	
    }
    
    function after_process() {
      return false;
    }

    function get_error() {
      return false;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Payflow', 'MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_STATUS', 'False', 'Do you want to accept PayPal Payflow payments?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Vendor', 'MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_VENDOR', '', 'Your merchant login ID that you created when you registered for the Website Payments account.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('User', 'MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_USERNAME', '', 'If you set up one or more additional users on the account, this value is the ID of the user authorised to process transactions. If, however, you have not set up additional users on the account, USER has the same value as VENDOR.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Password', 'MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_PASSWORD', '', 'The 6- to 32-character password that you defined while registering for the account.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Partner', 'MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_PARTNER', 'PayPal', 'The ID provided to you by the authorised PayPal Reseller who registered you for the Payflow SDK. If you purchased your account directly from PayPal.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Server', 'MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_TRANSACTION_SERVER', 'Live', 'Use the live or testing (sandbox) gateway server to process transactions?', '6', '0', 'tep_cfg_select_option(array(\'Live\', \'Sandbox\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Method', 'MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_TRANSACTION_METHOD', 'Sale', 'The processing method to use for each transaction.', '6', '0', 'tep_cfg_select_option(array(\'Authorization\', \'Sale\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Card Acceptance Page', 'MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_CARD_INPUT_PAGE', 'PayPal Hosted', 'The location to accept card information. Either on the Checkout Confirmation page or the Checkout Payment page.', '6', '0', 'tep_cfg_select_option(array(\'PayPal Hosted\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value.', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('cURL Program Location', 'MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_CURL', '/usr/bin/curl', 'The location to the cURL program application.', '6', '0' , now())");
   }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_STATUS', 'MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_VENDOR', 'MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_USERNAME', 'MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_PASSWORD', 'MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_PARTNER', 'MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_TRANSACTION_SERVER', 'MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_TRANSACTION_METHOD', 'MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_CARD_INPUT_PAGE', 'MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_ZONE', 'MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_ORDER_STATUS_ID', 'MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_SORT_ORDER', 'MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_CURL');
    }

    function sendTransactionToGateway($url, $parameters, $headers = null) {
      $header = array();

      $server = parse_url($url);

      if (!isset($server['port'])) {
        $server['port'] = ($server['scheme'] == 'https') ? 443 : 80;
      }

      if (!isset($server['path'])) {
        $server['path'] = '/';
      }

      if (isset($server['user']) && isset($server['pass'])) {
        $header[] = 'Authorization: Basic ' . base64_encode($server['user'] . ':' . $server['pass']);
      }

      if (!empty($headers) && is_array($headers)) {
        $header = array_merge($header, $headers);
      }

      if (function_exists('curl_init')) {
        $curl = curl_init($server['scheme'] . '://' . $server['host'] . $server['path'] . (isset($server['query']) ? '?' . $server['query'] : ''));
        curl_setopt($curl, CURLOPT_PORT, $server['port']);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters);

        if (!empty($header)) {
          curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }

        $result = curl_exec($curl);

        curl_close($curl);
      } else {
        exec(escapeshellarg(MODULE_PAYMENT_PAYPAL_PAYFLOW_LINK_CURL) . ' -d ' . escapeshellarg($parameters) . ' "' . $server['scheme'] . '://' . $server['host'] . $server['path'] . (isset($server['query']) ? '?' . $server['query'] : '') . '" -P ' . $server['port'] . ' -k' . (!empty($header) ? ' -H ' . escapeshellarg(implode("\r\n", $header)) : ''), $result);
        $result = implode("\n", $result);
      }

      return $result;
    }

// format prices without currency formatting
    function format_raw($number, $currency_code = '', $currency_value = '') {
      global $currencies, $currency;

      if (empty($currency_code) || !$this->is_set($currency_code)) {
        $currency_code = $currency;
      }

      if (empty($currency_value) || !is_numeric($currency_value)) {
        $currency_value = $currencies->currencies[$currency_code]['value'];
      }

      return number_format(tep_round($number * $currency_value, $currencies->currencies[$currency_code]['decimal_places']), $currencies->currencies[$currency_code]['decimal_places'], '.', '');
    }
  }
?>
