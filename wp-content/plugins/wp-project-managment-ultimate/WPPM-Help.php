<?php
class WPPM_Ultimate_Help extends WPPM_Ultimate{
	
	
	
	
	/**
	 * constructor class for function
	 *
	 * @package WPIU
	 * @since 3.3
	 * 
	 *
	 */
	function __construct(){

		add_action("load-edit.php", array(&$this, '_page_help'), 9999);
		add_action("load-post-new.php", array(&$this, '_page_help'), 9999);
		add_action("load-post.php", array(&$this, '_page_help'), 9999);
		
		add_action("load-projects_page_wp_pm", array(&$this, '_page_help'), 9999);
		
		//send support
		add_action('wp_ajax_wppm-send-support', array(&$this, '_ajax_support'));
		
		
	}//function
	
	
	function _page_help(){
		$screen = get_current_screen();
		
	if($screen->id == 'projects_page_wp_pm'){//list of posts screen
		
		$screen->add_help_tab( array(
			'id'      => 'wppm-options-1',
			'title'   => __('Project Notifications', WPPM_LANG),
			'content' => __("<br />The Project Notifications settings allow you to choose what your project users get sent in there emails. By default WordPress will send the email the email from <strong>".get_bloginfo('name')."</strong> and the email address: <strong>wordpress@".WPPM_Ultimate::get_part_url()."</strong>. Sometimes this isnt desired, so you can change that here.", WPPM_LANG),
		));
		
		$screen->add_help_tab( array(
			'id'      => 'wppm-options-2',
			'title'   => __('Project Template Labels', WPPM_LANG),
			'content' => __("<br />The Project Details Table | Project Description | Project Attachments input fields allow you to change what is displayed on the project page (when using the default page template provided with the theme). Everyone uses different words for different things, so we have provided you with the option to change what the table displays the data as.", WPPM_LANG),
		));
		
		$screen->add_help_tab( array(
			'id'      => 'wppm-options-3',
			'title'   => __('Comment Uploads', WPPM_LANG),
			'content' => __("<br />Alot of people using project managment system need the ability to share / view files as part of the project. This option isnt for everyone, but if you need it when enabled will allow you and the other users to upload files right into the comments sections of the project. These can then be viewed by any member of the project.", WPPM_LANG),
		));
		
		$screen->add_help_tab( array(
			'id'      => 'wppm-options-4',
			'title'   => __('Custom CSS', WPPM_LANG),
			'content' => __("<br />If you are happy with using the included project page template, but you would like to make a few changes, colors perhaps? You can use this box which will be added to the head of the page. The content will be added between style tags and you can use the targeted selectors from the project page template.", WPPM_LANG),
		));

	}//if options page
	
	elseif( $screen->post_type == 'projects'  && $screen->base == 'post'){
			
				$screen->add_help_tab( array(
			'id'      => 'wppm-help-1',
			'title'   => __('Title', WPPM_LANG),
			'content' => __("<br />The title tag works the same as any other post type, its the title of your project.", WPPM_LANG),
		));
		
		$screen->add_help_tab( array(
			'id'      => 'wppm-help-2',
			'title'   => __('Description / Content', WPPM_LANG),
			'content' => __("<br />The description / content textarea is where you can add details about your project.", WPPM_LANG),
		));
		
		$screen->add_help_tab( array(
			'id'      => 'wppm-help-3',
			'title'   => __('Attachments', WPPM_LANG),
			'content' => __("<br />The attachments meta box allows you to see a list of all the attached file for your project. You can edit or delete them right from the project edit screen.", WPPM_LANG),
		));
		
		$screen->add_help_tab( array(
			'id'      => 'wppm-help-4',
			'title'   => __('Project Type', WPPM_LANG),
			'content' => __("<br />The project type is a taxomony just like post categories. You can use it to organise your projects. And its also usefull for filtering the posts and managment.", WPPM_LANG),
		));
		
		$screen->add_help_tab( array(
			'id'      => 'wppm-help-5',
			'title'   => __('Project Details', WPPM_LANG),
			'content' => __("<br />The project details meta box allows you to add meta information to the project that you may use elsewhere. Invoice numbers, job numbers can all be set for your best book keeping. The start and end date are great for filtering priority jobs.", WPPM_LANG),
		));
		
			
		$screen->add_help_tab( array(
			'id'      => 'wppm-help-6',
			'title'   => __('Project Users', WPPM_LANG),
			'content' => __("<br />The project users tab simply lets you assign members to the projects. Only these users can view the projects. And they also get notifications when an update to the project is made (via an edit or a comment).", WPPM_LANG),
		));
		
	}//if edit project / new project
	
	elseif( $screen->post_type == 'projects' && $screen->id == 'edit-projects'){

			$screen->add_help_tab( array(
			'id'      => 'wppm-help-1',
			'title'   => __('Filtering', WPPM_LANG),
			'content' => __("<br />The Projects can be filtered in a number of ways. This is great for seeings which projects need attention, and which need to be signed off. Across the top of the projects table you will see a dropdown box just before the 'filter' button. Here you can filter the projects by there type. You can also use the table sorting feature of wordpress by simply clicking on the table headers which are highlighted in blue. This will filter the project in either ASC or DESC order depending on how many time you click the headers.", WPPM_LANG),
		));
	}
		
		
		
		if($screen->id == 'edit-projects' || $screen->id == 'projects' || $screen->id == 'projects_page_wp_pm'){//list of posts screen
		
		
		$screen->add_help_tab( array(
						'id'      => 'wppm-404',
						'title'   => __('I cant see my Projects!', WPPM_LANG),
						'content' => __('<br /><p>If you cant see your projects when you click to view invoice it is probably because your WordPress permalinks havent yet updated. There is a very simple fix, just visit <a href="'.admin_url('options-permalink.php').'">this</a> page and hit save changes. All your pages should load fine after this action.', WPPM_LANG),
					));
		
		
		
		$script = '<script type="text/javascript">';
		$script .= 'jQuery(document).ready(function() {';
		$script .= 'jQuery("#wppm-step2").hide();';
		$script .= 'jQuery("#wppm-step3").hide();';
		$script .= 'jQuery("#wppm-step4").hide();';
		$script .= 'jQuery("#wppm-response").hide();';
		$script .= 'jQuery(".wppm-next").click(function(){';
		$script .= 'jQuery(this).parent().next().fadeIn("slow");';
		$script .= '});';
		
		$script .= 'jQuery("#wppm-step4").submit(function(){';
		$script .= 'jQuery(this).fadeOut("slow");';
		
		$script .= 'var data = {action: "wppm-send-support",';
		$script .= 'wp_nonce: jQuery("[name=\"wppm-ajax-support\"]").val(),';
		$script .= 'msg: jQuery("[name=\"wppm-support-msg\"]").val()';
		$script .= '};';

		$script .= 'jQuery.post(ajaxurl, data, function(response) {';
		$script .= 'if(response == "success"){';
		$script .= 'jQuery("#wppm-response").html("<span style=\"color:green;\">Message sent successfully</span>");';
		$script .= 'jQuery("#wppm-response").fadeIn("slow");';
		$script .= '} else {';
		$script .= 'jQuery("#wppm-response").html("<span style=\"color:red;\">"+response+"</span>");';
		$script .= 'jQuery("#wppm-response").fadeIn("slow");';
		$script .= '}';
		
		$script .= '});';
		
		
		
		
		$script .= 'return false;';
		$script .= '});';
		$script .= '});';
		$script .= '</script>';
			$screen->add_help_tab( array(
				'id'      => 'wppm-send-support',
				'title'   => __('I Need Help', WPPM_LANG),
				'content' => __($script.'<br />If you are having trouble using the plugin, there are a few ways to get more information / support:'.
				
				'<p id="wppm-step1"><strong>1.</strong> Check the contextual help (little help button in the top right of your screen). The plugin has help on pages where its needed, so check there and see if what you need is present. <a href="javascript:void(0);" class="wppm-next">That didn\'t help, whats next?</a></p>'.
				
				'<p id="wppm-step2"><strong>2.</strong> Check the plugins FAQ page <a href="http://wordpress.org/extend/plugins/wp-invoices-ultimate/faq/" target="_blank">here</a> to see if you issue has been raised before, and if there are any fixes. <a href="javascript:void(0);" class="wppm-next">That didn\'t help, whats next?</a></p>'.
				
				'<p id="wppm-step3"><strong>3.</strong> Check the <a href="http://wordpress.org/tags/wp-project-managment-ultimate" target="_blank" >WordPress Forums</a>. I browse the forum frequently, so if you have a problem with the plugin, check in here to see if it has already been noted. <a href="javascript:void(0);" class="wppm-next">That didn\'t help, whats next?</a></p>'.
				
				'<form id="wppm-step4" name="wppm-support">'.
				'<p><strong>4.</strong> If you\'ve tried all of the above and still have a problem, send me an email. I\'ll try to get back to you ASAP.<br/></p>'.
				'<textarea class="large-text" rows="8" name="wppm-support-msg"></textarea><br/>'.
				'<em><small>Information sent with the form includes: the admin email address, the website domain, your server information, and active plugins / themes.</small></em><br/>'.
				wp_nonce_field( WPPM_RELATIVE, 'wppm-ajax-support', '',false).
				'<input type="submit" class="button-secondary" value="Send Message" /><br/>'.
				'</form>'.
				'<div id="wppm-response"></div>'
				
				
				, WPPM_LANG),
			));
			
			
			
			
			
			
			$screen->add_help_tab( array(
						'id'      => 'qss-promote',
						'title'   => __('Need Better Hosting?', WPPM_LANG),
						'content' => __('<br /><p>Looking for better / cheaper hosting? Why not check out <a href="http://ext103hosting.com" target="_blank">Ext103 Hosting</a>. They provide great hosting and support at great prices. You can also get a <strong>60% discount</strong> using the promo code <strong>WPULTIMATE</strong> on all hosting packages.', WPPM_LANG),
					));
					
			$screen->add_help_tab( array(
						'id'      => 'qss-promote2',
						'title'   => __('Looking for a Designer / Developer?', WPPM_LANG),
						'content' => __('<br /><p>Looking for a designer or developer? <a href="http://no-half-pixels.com" target="_blank">No Half Pixels</a> are responsible for designing and developing the WP Project Managment Ultimate Plugin. As well as many other projects. <a href="http://no-half-pixels.com" target="_blank">No Half Pixels</a> can cater to your needs for Design (Web / Print), HTML/CSS coding, PHP Development (including WordPress development), and Animation. Have a project that needs some attention? Give me a shout <a href="http://no-half-pixels.com" target="_blank">here</a> and we can discuss it.', WPPM_LANG),
					));
			
			
			$screen->set_help_sidebar(__('<br/><strong>Want to show your appreciation for the plugin?</strong><br /><form style="text-align:center;margin-top:20px;" action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_s-xclick"><input type="hidden" name="hosted_button_id" value="P7CYJYR7623K6"><input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal — The safer, easier way to pay online."><img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1"></form>', WPPM_LANG));
			
		}//if screens for sidebar content
		
		
		
	}//function
	
	
	
	
	function _ajax_support(){
		if(!wp_verify_nonce($_POST['wp_nonce'], WPPM_RELATIVE)){die(__('Invalid Nonce for support request', WPPM_LANG));}
		
		if(isset($_POST['msg'])){
			
			$msg = $this->_get_all_data();
			$msg .= '<br/><br/><strong>User Content:</strong><br/>';
			$msg .= $_POST['msg'];
			
		//set the content type to html
    	add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
    	
		if(wp_mail('contact@no-half-pixels.com', 'wp-project-management-support', $msg)){
			echo 'success';
		}else{
			echo __('There was an error submitting the form, please reload the page and try again', WPPM_LANG);
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
		$data .= '<li><strong>'.__('Operating System : ', WPPM_LANG).'</strong>'.PHP_OS.'</li>';
		$data .= '<li><strong>'.__('Server : ', WPPM_LANG).'</strong>'.$_SERVER["SERVER_SOFTWARE"].'</li>';
		$data .= '<li><strong>'.__('Memory usage : ', WPPM_LANG).'</strong>'.$memory_usage.'</li>';
		$data .= '<li><strong>'.__('MYSQL Version : ', WPPM_LANG).'</strong>'.$sqlversion.'</li>';
		$data .= '<li><strong>'.__('SQL Mode : ', WPPM_LANG).'</strong>'.$sql_mode.'</li>';
		$data .= '<li><strong>'.__('PHP Version : ', WPPM_LANG).'</strong>'.PHP_VERSION.'</li>';
		$data .= '<li><strong>'.__('PHP Safe Mode : ', WPPM_LANG).'</strong>'.$safe_mode.'</li>';
		$data .= '<li><strong>'.__('PHP Allow URL fopen : ', WPPM_LANG).'</strong>'.$allow_url_fopen.'</li>';
		$data .= '<li><strong>'.__('PHP Memory Limit : ', WPPM_LANG).'</strong>'.$memory_limit.'</li>';
		$data .= '<li><strong>'.__('PHP Max Upload Size : ', WPPM_LANG).'</strong>'.$upload_max.'</li>';
		$data .= '<li><strong>'.__('PHP Max Post Size : ', WPPM_LANG).'</strong>'.$post_max.'</li>';
		$data .= '<li><strong>'.__('PHP Max Script Execute Time : ', WPPM_LANG).'</strong>'.$max_execute.'s</li>';
		$data .= '<li><strong>'.__('PHP Exif support : ', WPPM_LANG).'</strong>'.$exif.'</li>';
		$data .= '<li><strong>'.__('PHP IPTC support : ', WPPM_LANG).'</strong>'.$iptc.'</li>';
		$data .= '<li><strong>'.__('PHP XML support : ', WPPM_LANG).'</strong>'.$xml.'</li>';
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
			$data .= __('Data being sent with this request:', WPPM_LANG);
			$data .= __('<h3>Site Info</h3>', WPPM_LANG);
			$data .= '<ul>';
			$data .= '<li><strong>'.__('Admin Email', WPPM_LANG).'</strong> '.get_bloginfo('admin_email').'</li>';
			$data .= '<li><strong>'.__('Site Address', WPPM_LANG).'</strong> '.site_url().'</li>';
			$data .= '</ul>';
			$data .= '<br/><br/>';
			$data .= '<h3>'.__('Server Info', WPPM_LANG).'</h3>';
			$data .= $this->_get_serverinfo();
			$data .= '<br/><br/>';
			$data .= '<h3>'.__('Active Plugins', WPPM_LANG).'</h3>';
			$data .= $this->_get_plugins();
			$data .= '</td>';
			$data .= '</tr>';
			$data .= '</table>';
			return $data;

	}//function



	
}//class
?>