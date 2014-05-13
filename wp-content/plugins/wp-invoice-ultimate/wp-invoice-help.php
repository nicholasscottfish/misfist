<?php
class WPIU_Help extends WPIU{
	
	
	
	
	/**
	 * constructor class for function
	 *
	 * @package WPIU
	 * @since 3.3
	 * 
	 *
	 */
	function __construct(){
		
		//add_action("load-wpiu-invoices", array(&$this, '_page_help'));
		//add_action("load-wpiu-invoices", array(&$this, '_page_help'));
		add_action("load-edit.php", array(&$this, '_page_help'));
		add_action("load-post-new.php", array(&$this, '_page_help'));
		add_action("load-post.php", array(&$this, '_page_help'));
		
		add_action("load-wpiu-invoices_page_wp_iu", array(&$this, '_page_help'));
		add_action("load-wpiu-invoices_page_wp_iu_log", array(&$this, '_page_help'));
		
		//send support
		add_action('wp_ajax_wpiu-send-support', array(&$this, '_ajax_support'));
		
		
	}//function
	
	
	function _page_help(){
		$screen = get_current_screen();
		
		
		if($screen->id == 'edit-wpiu-invoices'){//list of posts screen
			
			
		}//if list posts screen
		
		if($screen->id == 'wpiu-invoices'){//list of posts screen
		
			$screen->add_help_tab( array(
				'id'      => 'wpiu-help-1',
				'title'   => __('Title', WPIU_LANG),
				'content' => __("<br />The title tag works the same as any other post type, its the title of your invoice.", WPIU_LANG),
			));
			
			$screen->add_help_tab( array(
				'id'      => 'wpiu-help-2',
				'title'   => __('Description / Content', WPIU_LANG),
				'content' => __("<br />The description / content textarea is where you can add details about your invoice.", WPIU_LANG),
			));
			
			$screen->add_help_tab( array(
				'id'      => 'wpiu-help-3',
				'title'   => __('Invoice Details', WPIU_LANG),
				'content' => __("<br />The details meta box is used to add some filtering data to your invoice, it is what identifies your invoices when doing your accounts.<br/><br/>You can set the due date of the invoice,<br/>a job number associated with the invoice (optional),<br/>invoice number (required, random one generated for you, but can be over ridden),<br/>and a filed for payment amount (can be set manually or updated via the paypal payment system).", WPIU_LANG),
			));
			
			$screen->add_help_tab( array(
				'id'      => 'wpiu-help-4',
				'title'   => __('Invoice Client', WPIU_LANG),
				'content' => __("<br />The invoice client meta box will just give you a list of available users to assign the invoice to, or you can click the link to create a new user. Some of the users information with displayed dynamically to help you determine if its the right client.<br/><br/>If you want to edit the client you can just click on the link below the client information which will take you strait to the users page.", WPIU_LANG),
			));
			
			$screen->add_help_tab( array(
				'id'      => 'wpiu-help-5',
				'title'   => __('Invoice Items', WPIU_LANG),
				'content' => __("<br />The invoice items meta box is where you add the meat of your invoice. The fields are pretty straight forward:<br/><br/>".
				"<strong>Title:</strong> A short description of the invoiced item (detailed information should be added in the description area).<br/>".
				"<strong>Qty:</strong> If its a fixed invoice amount you can just put 1, if its a hourly paid job, put in the hours youve done.<br/>".
				"<strong>Unit Cost:</strong> Linked to the previous field, if its a fixed price job, put in your fixed price, if its hourly paid you can enter you hourly rate.<br/>".
				"<strong>Item Total:</strong> The item title is just the <strong>Qty</strong> and <strong>Unit</strong> cost field calculated to get your total.<br/><br/>".
				"<strong>Sub Total:</strong> The sub total is a calculation of all the items totals.<br/>".
				"<strong>Total:</strong> The total is your invoice total after all calculations have been done.<br/><br/>".
				"<em>To update the totals you need to save the invoice by either the normal save/update button, or the one below totals saying (Update Invoice). This is the safest method for generating correct values which is why it is not ajax based.</em>", WPIU_LANG),
			));

			
		}//if list posts screen
		
		
		if($screen->id == 'wpiu-invoices_page_wp_iu'){
			
			
			
		}//if options screen
		
		if($screen->id == 'edit-wpiu-invoices' || $screen->id == 'wpiu-invoices' || $screen->id == 'wpiu-invoices_page_wp_iu' || $screen->id == 'wpiu-invoices_page_wp_iu_log'){//list of posts screen
		
		
		$screen->add_help_tab( array(
						'id'      => 'wpiu-404',
						'title'   => __('I cant see my invoices!', WPIU_LANG),
						'content' => __('<br /><p>If you cant see your invoices when you click to view invoice it is probably because your WordPress permalinks havent yet updated. There is a very simple fix, just visit <a href="'.admin_url('options-permalink.php').'">this</a> page and hit save changes. All your pages should load fine after this action.', WPIU_LANG),
					));
		
		
		
		$script = '<script type="text/javascript">';
		$script .= 'jQuery(document).ready(function() {';
		$script .= 'jQuery("#wpiu-step2").hide();';
		$script .= 'jQuery("#wpiu-step3").hide();';
		$script .= 'jQuery("#wpiu-step4").hide();';
		$script .= 'jQuery("#wpiu-response").hide();';
		$script .= 'jQuery(".wpiu-next").click(function(){';
		$script .= 'jQuery(this).parent().next().fadeIn("slow");';
		$script .= '});';
		
		$script .= 'jQuery("#wpiu-step4").submit(function(){';
		$script .= 'jQuery(this).fadeOut("slow");';
		
		$script .= 'var data = {action: "wpiu-send-support",';
		$script .= 'wp_nonce: jQuery("[name=\"wpiu-ajax-support\"]").val(),';
		$script .= 'msg: jQuery("[name=\"wpiu-support-msg\"]").val()';
		$script .= '};';

		$script .= 'jQuery.post(ajaxurl, data, function(response) {';
		$script .= 'if(response == "success"){';
		$script .= 'jQuery("#wpiu-response").html("<span style=\"color:green;\">Message sent successfully</span>");';
		$script .= 'jQuery("#wpiu-response").fadeIn("slow");';
		$script .= '} else {';
		$script .= 'jQuery("#wpiu-response").html("<span style=\"color:red;\">"+response+"</span>");';
		$script .= 'jQuery("#wpiu-response").fadeIn("slow");';
		$script .= '}';
		
		$script .= '});';
		
		
		
		
		$script .= 'return false;';
		$script .= '});';
		$script .= '});';
		$script .= '</script>';
			$screen->add_help_tab( array(
				'id'      => 'wpiu-send-support',
				'title'   => __('I Need Help', WPIU_LANG),
				'content' => __($script.'<br />If you are having trouble using the plugin, there are a few ways to get more information / support:'.
				
				'<p id="wpiu-step1"><strong>1.</strong> Check the contextual help (little help button in the top right of your screen). The plugin has help on pages where its needed, so check there and see if what you need is present. <a href="javascript:void(0);" class="wpiu-next">That didn\'t help, whats next?</a></p>'.
				
				'<p id="wpiu-step2"><strong>2.</strong> Check the plugins FAQ page <a href="http://wordpress.org/extend/plugins/wp-invoices-ultimate/faq/" target="_blank">here</a> to see if you issue has been raised before, and if there are any fixes. <a href="javascript:void(0);" class="wpiu-next">That didn\'t help, whats next?</a></p>'.
				
				'<p id="wpiu-step3"><strong>3.</strong> Check the <a href="http://wordpress.org/tags/wp-invoice-ultimate" target="_blank" >WordPress Forums</a>. I browse the forum frequently, so if you have a problem with the plugin, check in here to see if it has already been noted. <a href="javascript:void(0);" class="wpiu-next">That didn\'t help, whats next?</a></p>'.
				
				'<form id="wpiu-step4" name="wpiu-support">'.
				'<p><strong>4.</strong> If you\'ve tried all of the above and still have a problem, send me an email. I\'ll try to get back to you ASAP.<br/></p>'.
				'<textarea class="large-text" rows="8" name="wpiu-support-msg"></textarea><br/>'.
				'<em><small>Information sent with the form includes: the admin email address, the website domain, your server information, and active plugins / themes.</small></em><br/>'.
				wp_nonce_field( WPIU_RELATIVE, 'wpiu-ajax-support', '',false).
				'<input type="submit" class="button-secondary" value="Send Message" /><br/>'.
				'</form>'.
				'<div id="wpiu-response"></div>'
				
				
				, WPIU_LANG),
			));
			
			
			
			
			
			
			$screen->add_help_tab( array(
						'id'      => 'qss-promote',
						'title'   => __('Need Better Hosting?', WPIU_LANG),
						'content' => __('<br /><p>Looking for better / cheaper hosting? Why not check out <a href="http://ext103hosting.com" target="_blank">Ext103 Hosting</a>. They provide great hosting and support at great prices. You can also get a <strong>60% discount</strong> using the promo code <strong>WPULTIMATE</strong> on all hosting packages.', WPIU_LANG),
					));
					
			$screen->add_help_tab( array(
						'id'      => 'qss-promote2',
						'title'   => __('Looking for a Designer / Developer?', WPIU_LANG),
						'content' => __('<br /><p>Looking for a designer or developer? <a href="http://no-half-pixels.com" target="_blank">No Half Pixels</a> are responsible for designing and developing the WP Invoices Ultimate Plugin. As well as many other projects. <a href="http://no-half-pixels.com" target="_blank">No Half Pixels</a> can cater to your needs for Design (Web / Print), HTML/CSS coding, PHP Development (including WordPress development), and Animation. Have a project that needs some attention? Give me a shout <a href="http://no-half-pixels.com" target="_blank">here</a> and we can discuss it.', WPIU_LANG),
					));
			
			
			$screen->set_help_sidebar(__('<br/><strong>Want to show your appreciation for the plugin?</strong><br /><form style="text-align:center;margin-top:20px;" action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_s-xclick"><input type="hidden" name="hosted_button_id" value="DVEW7VSBB8NYS"><input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal — The safer, easier way to pay online."><img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1"></form>', WPIU_LANG));
			
		}//if screens for sidebar content
		
		
		
	}//function
	
	
	
	
	function _ajax_support(){
		if(!wp_verify_nonce($_POST['wp_nonce'], WPIU_RELATIVE)){die(__('Invalid Nonce for support request', WPIU_LANG));}
		
		if(isset($_POST['msg'])){
			
			$msg = $this->_get_all_data();
			$msg .= '<br/><br/><strong>User Content:</strong><br/>';
			$msg .= $_POST['msg'];
			
		//set the content type to html
    	add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
    	
		if(wp_mail('contact@no-half-pixels.com', 'wp-invoice-support', $msg)){
			echo 'success';
		}else{
			echo __('There was an error submitting the form, please reload the page and try again', WPIU_LANG);
		}
			
		}
		
		die();
		
	}//function
	
	
	
		/**
	 * gets server info for support request
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	private function _get_serverinfo() {
		global $wpdb;
		$sqlversion = $wpdb->get_var("SELECT VERSION() AS version");
		$mysqlinfo = $wpdb->get_results("SHOW VARIABLES LIKE 'sql_mode'");
		if (is_array($mysqlinfo)) $sql_mode = $mysqlinfo[0]->Value;
		if (empty($sql_mode)) $sql_mode = __('Not set');
		if(ini_get('safe_mode')) $safe_mode = __('On');
		else $safe_mode = __('Off');
		if(ini_get('allow_url_fopen')) $allow_url_fopen = __('On');
		else $allow_url_fopen = __('Off');
		if(ini_get('upload_max_filesize')) $upload_max = ini_get('upload_max_filesize');
		else $upload_max = __('N/A');
		if(ini_get('post_max_size')) $post_max = ini_get('post_max_size');
		else $post_max = __('N/A');
		if(ini_get('max_execution_time')) $max_execute = ini_get('max_execution_time');
		else $max_execute = __('N/A');
		if(ini_get('memory_limit')) $memory_limit = ini_get('memory_limit');
		else $memory_limit = __('N/A');
		if (function_exists('memory_get_usage')) $memory_usage = round(memory_get_usage() / 1024 / 1024, 2) . __(' MByte');
		else $memory_usage = __('N/A');
		if (is_callable('exif_read_data')) $exif = __('Yes'). " ( V" . substr(phpversion('exif'),0,4) . ")" ;
		else $exif = __('No');
		if (is_callable('iptcparse')) $iptc = __('Yes');
		else $iptc = __('No');
		if (is_callable('xml_parser_create')) $xml = __('Yes');
		else $xml = __('No');
		$data = '<ul>';
		$data .= '<li><strong>'.__('Operating System : ', WPIU_LANG).'</strong>'.PHP_OS.'</li>';
		$data .= '<li><strong>'.__('Server : ', WPIU_LANG).'</strong>'.$_SERVER["SERVER_SOFTWARE"].'</li>';
		$data .= '<li><strong>'.__('Memory usage : ', WPIU_LANG).'</strong>'.$memory_usage.'</li>';
		$data .= '<li><strong>'.__('MYSQL Version : ', WPIU_LANG).'</strong>'.$sqlversion.'</li>';
		$data .= '<li><strong>'.__('SQL Mode : ', WPIU_LANG).'</strong>'.$sql_mode.'</li>';
		$data .= '<li><strong>'.__('PHP Version : ', WPIU_LANG).'</strong>'.PHP_VERSION.'</li>';
		$data .= '<li><strong>'.__('PHP Safe Mode : ', WPIU_LANG).'</strong>'.$safe_mode.'</li>';
		$data .= '<li><strong>'.__('PHP Allow URL fopen : ', WPIU_LANG).'</strong>'.$allow_url_fopen.'</li>';
		$data .= '<li><strong>'.__('PHP Memory Limit : ', WPIU_LANG).'</strong>'.$memory_limit.'</li>';
		$data .= '<li><strong>'.__('PHP Max Upload Size : ', WPIU_LANG).'</strong>'.$upload_max.'</li>';
		$data .= '<li><strong>'.__('PHP Max Post Size : ', WPIU_LANG).'</strong>'.$post_max.'</li>';
		$data .= '<li><strong>'.__('PHP Max Script Execute Time : ', WPIU_LANG).'</strong>'.$max_execute.'s</li>';
		$data .= '<li><strong>'.__('PHP Exif support : ', WPIU_LANG).'</strong>'.$exif.'</li>';
		$data .= '<li><strong>'.__('PHP IPTC support : ', WPIU_LANG).'</strong>'.$iptc.'</li>';
		$data .= '<li><strong>'.__('PHP XML support : ', WPIU_LANG).'</strong>'.$xml.'</li>';
		$data .= '</ul>';
		return $data;
	}//function


	/**
	 * gets plugin info for support request
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	private function _get_plugins(){
		$plugins = get_plugins();
				$data = '<ul>';
					foreach($plugins as $plugin){
						$data .= '<li><strong>'.$plugin['Name'].'</strong> - '.$plugin['Version'].'</li>';
					}
				$data .= '</ul>';
				return $data;
	}//function
	
	
	
	/**
	 * gets plugin info for support request
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	private function _get_all_data(){
	
			$data = '<table class="form-table">';
			$data .= '<tr valign="top">';
			$data .= '<td>';
			$data .= __('Data being sent with this request:', WPIU_LANG);
			$data .= __('<h3>Site Info</h3>', WPIU_LANG);
			$data .= '<ul>';
			$data .= '<li><strong>'.__('Admin Email', WPIU_LANG).'</strong> '.get_bloginfo('admin_email').'</li>';
			$data .= '<li><strong>'.__('Site Address', WPIU_LANG).'</strong> '.site_url().'</li>';
			$data .= '</ul>';
			$data .= '<br/><br/>';
			$data .= '<h3>'.__('Server Info', WPIU_LANG).'</h3>';
			$data .= $this->_get_serverinfo();
			$data .= '<br/><br/>';
			$data .= '<h3>'.__('Active Plugins', WPIU_LANG).'</h3>';
			$data .= $this->_get_plugins();
			$data .= '</td>';
			$data .= '</tr>';
			$data .= '</table>';
			return $data;

	}//function



	
}//class
?>