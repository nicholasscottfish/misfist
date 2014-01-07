<?php
class WPIU_Paypal extends WPIU{
	
	
	
	
	/**
	 * constructor class for function
	 *
	 * @package WPIU
	 * @since 3.3
	 * 
	 *
	 */
	function __construct(){
		add_action('template_redirect', array(&$this, '_wpiu_paypal_ipn'));
		add_filter( 'query_vars', array( $this, '_add_var' ));
	}//function
	
	/**
	 * Add our query var to the list of query vars
	 */
	public function _add_var($public_query_vars) {
		$public_query_vars[] = 'action';
		$public_query_vars[] = 'invoice_id';
		return $public_query_vars;
	}
	
	
	
	/**
	 * add additional profile fields for all users
	 *
	 * @package WPIU
	 * @since 3.3
	 * add meta info to each user for use in the invoicing system
	 * - has little java hack below with some weird marging styling with profile fields as no current option to add new sections to the profile field
	 * - could of added extra forms + fields etc, but trying to use the wp preffered way of adding meta. this could nbe extended to allow the use required.
	 *
	 */
	function _wpiu_paypal_ipn(){
		
		if (get_query_var( 'action' ) == 'wpiu_paypal_ipn' && get_query_var( 'invoice_id' )) {
			
			$invoice_id = $_GET['invoice_id'];
	
			$meta = array();
			$meta = get_post_meta($invoice_id, '_wpiu_invoice_meta_trans',true);
			$postmeta = get_post_meta($invoice_id, '_wpiu_invoice_meta',true);
			
			$ipnlog = array();
			$ipnlog - get_option(WPIU_OPTION.'_ipn_log');
			
			
			
			//strip slashes
			$_POST = stripslashes_deep($_POST);
	
			//get the values into a meta array for later use. and also ipn log
			$meta[] = $_POST;
			$ipnlog[] = $_POST;
			
			
			
			// add the paypal cmd to the post array
	        $_POST['cmd'] = '_notify-validate';
	
	        // send the message back to PayPal just as we received it
	        $params = array( 
	        	'body' => $_POST,
				'timeout' 	=> 30
	        );
	
			//set paypal url
	        $paypal_adr = 'https://www.paypal.com/cgi-bin/webscr';
	            
	        // post it all back to paypal to get a response code
	        $response = wp_remote_post( $paypal_adr, $params );
	        
	        // Retry
			if ( is_wp_error($response) ) {
				$params['sslverify'] = false;
				$response = wp_remote_post( $paypal_adr, $params );
			}
	        
			
	        // cleanup
	        unset($_POST['cmd']);
	        
	        //get options array
	        $options = get_option(WPIU_OPTION);
	        
	        //check response
	        if ( !is_wp_error($response) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 && (strcmp( $response['body'], "VERIFIED") == 0) && $_POST['payment_status'] == 'Completed' && $_POST['receiver_email'] == $options['invoice_payment_email']) {
	        	
	        		update_post_meta($invoice_id, '_wpiu_invoice_meta_trans', $meta);
	        		
	        		//update the paid amount
	        		$postmeta['_wpiu_invoice_meta_paid'] += $_POST['mc_gross'];
	        		update_post_meta($invoice_id, '_wpiu_invoice_meta',$postmeta);
	        		
	        		//update the ipn log
	        		update_option(WPIU_OPTION.'_ipn_log', $ipnlog);
	        		
	        		//send email
	        		WPIU_Notifications::_send_payment_update($postmeta, 'payment', $invoice_id);
	        		
	        }else{
	        	WPIU_Notifications::_send_payment_update($postmeta, 'failed_payment', $invoice_id, print_r($_POST, true));	
	        }
				
				
			//kill wp
			exit();
		}//if empty posts
	}//function





	
}//class
?>