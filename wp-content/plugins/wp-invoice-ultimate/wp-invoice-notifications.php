<?php
class WPIU_Notifications extends WPIU{
	
	
	
	
	/**
	 * constructor class for function
	 *
	 * @package WPIU
	 * @since 3.3
	 * 
	 *
	 */
	function __construct(){
		
		
		
	}//function
	
	
	
	/**
	 * send invoice first time
	 *
	 * @package WPIU
	 * @since 3.3
	 * 
	 *
	 */
	function _send_invoice($meta, $id){
		
		WPIU_Notifications::_email_template($meta, 'send', $id);

	}//function
	
	
	
	
	/**
	 * send invoice reminder
	 *
	 * @package WPIU
	 * @since 3.3
	 * 
	 *
	 */
	function _send_invoice_reminder($meta, $id){
		
		WPIU_Notifications::_email_template($meta, 'reminder', $id);

	}//function
	
	
	
	/**
	 * send payment notification
	 *
	 * @package WPIU
	 * @since 3.3
	 * 
	 *
	 */
	function _send_payment_update($meta, $status, $id, $debug = ''){
		
		WPIU_Notifications::_email_template($meta, $status, $id, $debug);

	}//function
	
	
	
	
	/**
	 * private function for sending emails
	 *
	 * @package WPIU
	 * @since 3.3
	 * 
	 *
	 */
	function _email_template($meta, $status, $id = '', $debug = ''){
		$post = get_post($id);
		
		//get userdata for email template
		$user = get_userdata($meta['_wpiu_invoice_meta_client']);
		
		//set the to email field with user
		$to = $user->user_email;
		
		//filter the from name and email if custom options set
		if(WPIU_Options::option('invoice_notify_name')){
    		add_filter('wp_mail_from_name', create_function('', 'return "'.WPIU_Options::option('invoice_notify_name').'";'));
    	}else{
    		add_filter('wp_mail_from_name', create_function('', 'return "'.get_bloginfo('name').' - Invoices";'));
    	}//if
    	if(WPIU_Options::option('invoice_notify_email')){
    		add_filter('wp_mail_from', create_function('', 'return "'.WPIU_Options::option('invoice_notify_email').'@'.WPIU_Options::get_part_url().'";'));
    	}else{
    		add_filter('wp_mail_from', create_function('', 'return "invoices@'.WPIU_Options::get_part_url().'";'));
    	}//if
    	
    	//set the content type to html
    	add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
		
		
		
		
		//set the subject
		if($status == 'send'){		
			if(WPIU_Options::option('invoice_notify_send_subject')){
				$subject = str_replace('%%title%%', $post->post_title, WPIU_Options::option('invoice_notify_send_subject'));
				$subject = str_replace('%%number%%', $meta['_wpiu_invoice_meta_invoice_number'], $subject);
			}else{
				$subject = __('WP Invoice - '.$post->post_title.' - #'.$meta['_wpiu_invoice_meta_invoice_number'].' has been created!', WPIU_LANG);	
			}
		}elseif($status == 'reminder'){
			if(WPIU_Options::option('invoice_notify_send_reminder_subject')){
				$subject = str_replace('%%title%%', $post->post_title, WPIU_Options::option('invoice_notify_send_reminder_subject'));
				$subject = str_replace('%%number%%', $meta['_wpiu_invoice_meta_invoice_number'], $subject);
			}else{
				$subject = __('WP Invoice - '.$post->post_title.' - #'.$meta['_wpiu_invoice_meta_invoice_number'].' needs payment!', WPIU_LANG);	
			}	
		}elseif($status = 'payment'){
			if(WPIU_Options::option('invoice_notify_paid')){
				$subject = str_replace('%%title%%', $post->post_title, WPIU_Options::option('invoice_notify_paid'));
				$subject = str_replace('%%number%%', $meta['_wpiu_invoice_meta_invoice_number'], $subject);
			}else{
				$subject = __('WP Invoice - '.$post->post_title.' - #'.$meta['_wpiu_invoice_meta_invoice_number'].' has a new payment!.', WPIU_LANG);	
			}
		}elseif($status = 'failed_payment'){
				$subject = __('WP Invoice - '.$post->post_title.' - #'.$meta['_wpiu_invoice_meta_invoice_number'].' has had an attempted payment with errors.', WPIU_LANG);	
		}else{
				$subject = __('WP Invoice - '.$post->post_title.' - #'.$meta['_wpiu_invoice_meta_invoice_number'].' has been updated.', WPIU_LANG);	
		}
		
		
		
		
		
		
		//set the message
		$message = "";
		
		if($status == 'payment' || $status == 'failed_payment'){
			$response = wp_remote_get(add_query_arg(array('email' => 'true', 'proccessing' => 'true'), get_permalink($post->ID)));
			if($debug != ''){wp_mail(get_option('admin_email'), __('Invoice Payment Error', WPIU_LANG), $debug);}
		}else{
			$response = wp_remote_get(add_query_arg(array('email' => 'true'), get_permalink($post->ID)));
		}

		if(!is_wp_error($response)){
			$message = $response['body'];
		}
		
    	
    
		
		
		//send mail
		wp_mail($to, $subject, $message);
		
		
		
	}//function
	
	
	
	
	
	
}//class
?>