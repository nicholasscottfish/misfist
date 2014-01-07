<?php
class WPPM_Ultimate_Options extends WPPM_Ultimate{
	
	/**
	 * setup some class vars - one for page hook -help guides / etc | one for option name
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * 
	 *
	 */
	
	public $page_hook;
	public $option_name = WPPM_OPTION;
	
	
	
	/**
	 * constructor - adds all the functions to wp actions
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * 
	 *
	 */
	function __construct(){
		
		if ( is_admin() ){
			add_action('admin_menu', array(&$this, '_add_page'));
		}//if
		
		add_action('admin_init', array(&$this, '_register_settings') );
		
		add_action( 'admin_notices', array(&$this,'_admin_notices'));
		
	}//function
	
	
	
	
	/**
	 * internal function for displaying input fileds - reduces markup
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * removed support for wysiwyg as wp 3.3 contains wp_editor() function
	 *
	 */
	function _field($type, $options_name, $option, $id = '', $length = '', $value = '', $placeholder = ''){
		
		$options_array = get_option($options_name);
		
		switch($type){
			
			case 'checkbox':
			
				echo "<input ".checked( isset($options_array[$option]), 1 , false)." id='".$id."' name='".$options_name."[".$option."]' type='checkbox' value ='1'/>";
				
			break;
			case 'text':
			
				echo "<input id='".$id."' name='".$options_name."[".$option."]"."' size='".$length."' type='text' value='";
				echo isset($options_array[$option]) ? $options_array[$option] : '';
				echo "' placeholder='{$placeholder}'/>";

			break;
			case 'password':
			
				echo "<input id='".$id."' name='".$options_name."[".$option."]"."' size='".$length."' type='password' value='";
				echo isset($options_array[$option]) ? $options_array[$option] : '';
				echo "' />"; 
				
			break;
			case 'textarea':
			
				echo "<textarea id='".$id."' name='".$options_name."[".$option."]"."' rows='4' cols='60' type='textarea'>";
				echo isset($options_array[$option]) ? $options_array[$option] : '';
				echo "</textarea>";
				
			break;
			case 'select':
			
				echo "<select id='".$id."' name='".$options_name."[".$option."]"."'>";
				
					foreach($value as $item) {
						echo "<option value='$item' ".selected($options_array[$option], $item, false).">$item</option>";
					}//foreach
					
				echo "</select>";
				
			break;
			case 'file':
			
				echo '<input type="file" name="'.$option.'" size="'.$length.'"/>';
				
			break;
	
		}//switch
		
	}//function
	
	
	
	
	
	/**
	 * add options page to post type
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * 
	 *
	 */
	function _add_page() {
				$this->page_hook = add_submenu_page('edit.php?post_type=projects',__('WP Project Managment Ultimate Options',WPPM_LANG),__('Managment Options',WPPM_LANG),'administrator','wp_pm', array(&$this,'_page_html'));

		
	}//function
	
	
	
	
	/**
	 * register the settings sections for use on the options page
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 *
	 */
	function _register_settings(){
				register_setting( $this->option_name.'_group', $this->option_name, array(&$this,'_validate_options'));		
		
		add_settings_section($this->option_name.'_section', __('Project Notifications',WPPM_LANG), array(&$this,'project_notify_intro'), __FILE__);
		
		
			add_settings_field('project_notify_name_options', __('Notification From Name',WPPM_LANG), array(&$this,'project_notify_name_options'), __FILE__, $this->option_name.'_section');
			
			add_settings_field('project_notify_email_options', __('Notification From Email',WPPM_LANG), array(&$this,'project_notify_email_options'), __FILE__, $this->option_name.'_section');
			
			
		
		add_settings_section($this->option_name.'_section1', __('Project Details Table',WPPM_LANG), array(&$this,'project_table_intro'), __FILE__);
		
		
			add_settings_field('project_title_options', __('Project Title',WPPM_LANG), array(&$this,'project_title_options'), __FILE__, $this->option_name.'_section1');
			
			add_settings_field('project_users_options', __('Project Users',WPPM_LANG), array(&$this,'project_users_options'), __FILE__, $this->option_name.'_section1');
			
			add_settings_field('project_start_options', __('Project Start Date',WPPM_LANG), array(&$this,'project_start_options'), __FILE__, $this->option_name.'_section1');
			
			add_settings_field('project_end_options', __('Project End Date',WPPM_LANG), array(&$this,'project_end_options'), __FILE__, $this->option_name.'_section1');
			
			add_settings_field('project_completed_options', __('Project Completed',WPPM_LANG), array(&$this,'project_completed_options'), __FILE__, $this->option_name.'_section1');
			
			add_settings_field('project_paid_options', __('Project Paid',WPPM_LANG), array(&$this,'project_paid_options'), __FILE__, $this->option_name.'_section1');
			
		
		
		add_settings_section($this->option_name.'_section2', __('Project Description',WPPM_LANG), array(&$this,'project_description_intro'), __FILE__);
		
		
			add_settings_field('project_description_options', __('Project Description Header',WPPM_LANG), array(&$this,'project_description_options'), __FILE__, $this->option_name.'_section2');
			
			
			
			
		add_settings_section($this->option_name.'_section3', __('Project Attachments',WPPM_LANG), array(&$this,'project_attachments_intro'), __FILE__);
		
		
			add_settings_field('project_description_options', __('Project Attachments Header',WPPM_LANG), array(&$this,'project_attachments_options'), __FILE__, $this->option_name.'_section3');
			
			
			
			
		add_settings_section($this->option_name.'_section4', __('Project Comment Uploads',WPPM_LANG), array(&$this,'project_comments_uploads_intro'), __FILE__);
		
		
			add_settings_field('project_comment_options', __('Project Comment Uploads',WPPM_LANG), array(&$this,'project_comments_uploads_options'), __FILE__, $this->option_name.'_section4');
			
			add_settings_field('project_comment_options_2', __('Project Comment Upload Heading',WPPM_LANG), array(&$this,'project_comments_uploads_options_2'), __FILE__, $this->option_name.'_section4');
			
			add_settings_field('project_comment_options_3', __('Project Comment Upload Description',WPPM_LANG), array(&$this,'project_comments_uploads_options_3'), __FILE__, $this->option_name.'_section4');
			
	
	add_settings_section($this->option_name.'_section5', __('Custom CSS',WPPM_LANG), array(&$this,'project_css_intro'), __FILE__);
	
			add_settings_field('project_css_options', __('Project Page CSS',WPPM_LANG), array(&$this,'project_css_options'), __FILE__, $this->option_name.'_section5');
			
				}//function
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function project_notify_intro(){
		
		echo __('<p>Here you can choose to add custom "From" name and email address, this is what is displayed as the sender in the project notification emails:</p>',WPPM_LANG);
		
	}//function
	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function project_notify_name_options(){
		
		$this->_field('text', $this->option_name, 'project_notify_name', $this->option_name, '30', '', __('Your Name',WPPM_LANG));

	}//function
	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function project_notify_email_options(){
		
		$this->_field('text', $this->option_name, 'project_notify_email', $this->option_name, '30', '', __('projects',WPPM_LANG));
		
		echo '<span class="description">@'.parent::get_part_url().'</span>';

	}//function
	
	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function project_table_intro(){
		
		echo __('<p>Use these options to change the text displayed in the Project Details Table. This table is displayed at the top of the project page and you can change the following titles:</p>',WPPM_LANG);
		
	}//function
	
	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function project_title_options(){
		
		$this->_field('text', $this->option_name, 'project_table_title', $this->option_name, '30', '', __('Project:',WPPM_LANG));

	}//function
	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function project_users_options(){
		
		$this->_field('text', $this->option_name, 'project_table_users', $this->option_name, '30', '', __('Users:',WPPM_LANG));

	}//function
	
	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function project_start_options(){
		
		$this->_field('text', $this->option_name, 'project_table_start_date', $this->option_name, '30', '', __('Start Date:',WPPM_LANG));

	}//function
	
	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function project_end_options(){
		
		$this->_field('text', $this->option_name, 'project_table_end_date', $this->option_name, '30', '', __('End Date:',WPPM_LANG));

	}//function
	
	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function project_completed_options(){
		
		$this->_field('text', $this->option_name, 'project_table_completed', $this->option_name, '30', '', __('Completed:',WPPM_LANG));

	}//function
	
	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function project_paid_options(){
		
		$this->_field('text', $this->option_name, 'project_table_paid', $this->option_name, '30', '', __('Paid:',WPPM_LANG));
		
		echo '<br/><br/>';
		
	}//function
	
	
	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function project_description_intro(){
		
		echo __('<p>The project description Header is displayed above the projects content. Leave blank to display no header. Or choose to input a custom header title.</p>',WPPM_LANG);
		
	}//function
	
	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function project_description_options(){
		
		$this->_field('text', $this->option_name, 'project_description_title', $this->option_name, '67', '', __('Project Details',WPPM_LANG));
		
		echo '<br/><br/>';
		
	}//function
	
	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */	
	function project_attachments_intro(){
		
		echo __('<p>The project attachments Header is displayed above the list of the projects attachments. Leave blank to display no header. Or choose to input a custom header title.</p>',WPPM_LANG);
		
	}//function
	
	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function project_attachments_options(){
		
		$this->_field('text', $this->option_name, 'project_attachments_title', $this->option_name, '67', '', __('Project Attachments',WPPM_LANG));
		
		echo '<br/><br/>';
		
	}//function
	
	
	
	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function project_comments_uploads_intro(){
		
		echo __('<p>The project comments section contains an option for users to upload files and images to comments. There are options to customize what text / messages you shouw for these fields. They are as follows:</p>',WPPM_LANG);
		
	}//function
	
	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function project_comments_uploads_options(){
		
		$this->_field('select', $this->option_name, 'project_comment_uploads_allowed', $this->option_name.'_project_comment_uploads_allowed', '', array("Enable", "Disable"));echo __(' Comment Uploads',WPPM_LANG);
		
		echo '<br/><br/>';
		
	}//function
	
	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function project_comments_uploads_options_2(){
		
		$this->_field('text', $this->option_name, 'project_comment_uploads_title', $this->option_name, '67', '', __('File / Image Uploads',WPPM_LANG));

		echo '<br/><br/>';
		
	}//function
	
	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function project_comments_uploads_options_3(){
		
		$options = get_option($this->option_name);
		
		wp_editor( $options['project_comment_uploads_desc'], 'projectcommentuploads', array('textarea_name' => $this->option_name.'[project_comment_uploads_desc]', 'textarea_rows' => 4) );
		
		echo '<br/><br/>';
		
	}//function
	
	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function project_css_intro(){
		
		echo __('<p>The project page has some default styling already setup for a generic look. You can change this by adding custom CSS here to intergrate the page more into your sites styling.</p>',WPPM_LANG);
	
	}//function
	
	
	
	
	/**
	 * settings section - registered above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function project_css_options(){
		
		$this->_field('textarea', $this->option_name, 'project_css', $this->option_name);

	}//function
	
	
	
	
	/**
	 * the page html
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function _page_html(){$options = get_option($this->option_name);?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				
				var selector = jQuery('#_WPPM_Options_project_comment_uploads_allowed');
				
				if(selector.val() == 'Disable'){
					jQuery(selector).parent().parent().next().fadeOut('slow');
					jQuery(selector).parent().parent().next().next().fadeOut('slow');	
				}
				
				jQuery(selector).change(function(){
					if(jQuery(this).val() == 'Enable'){jQuery(this).parent().parent().next().fadeIn('slow');jQuery(this).parent().parent().next().next().fadeIn('slow');}
					if(jQuery(this).val() == 'Disable'){jQuery(this).parent().parent().next().fadeOut('slow');jQuery(this).parent().parent().next().next().fadeOut('slow');}
				});
				
			});
		</script>
				<div class="wrap">
			<img src="<?php echo WPPM_IMG .'32/icon.png';?>" style="margin:14px 6px 0px 0px;float:left;"/>
			<h2 style="display:inline-block;"><?php echo get_admin_page_title(); ?></h2>
				<form method="post" action="options.php" enctype="multipart/form-data">					<?php settings_fields($this->option_name.'_group');
							$options = get_option($this->option_name);
							do_settings_sections(__FILE__); ?>
					<p><br/><br/><input type="submit" name="<?php echo $this->option_name;?>[save]" value="Save Changes" class="button-primary" /></p>				</form>				<!-- <?php $options = get_option($this->option_name); print_r($options);?> -->
				<div class="clear"></div>
		</div>	<?php }//function
	


	
	
	
	
	
	
	/**
	 * vaildate options callback
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * adds settings updated error to page as well
	 *
	 */
	function _validate_options($plugin_options){
		//validate the file upload
		$keys = array_keys($_FILES);$i = 0;
		foreach ( $_FILES as $image ) {// if a files was upload
		if ($image['size']) {// if it is an image
		if ( preg_match('/(jpg|jpeg|png|gif|ico)$/', $image['type']) ) {$override = array('test_form' => false);// save the file, and store an array, containing its location in $file
		$file = wp_handle_upload( $image, $override );$plugin_options[$keys[$i]] = $file['url'];} else {// Not an image. 
		$options = get_option($this->option_name);$plugin_options[$keys[$i]] = $options[$logo];// Die and let the user know that they made a mistake.
		wp_die('No image was uploaded. Please try again');}}// Else, the user didn't upload a file.// Retain the image that's already on file.
		else {$options = get_option($this->option_name);$plugin_options[$keys[$i]] = $options[$keys[$i]];}$i++;}

		add_settings_error(__FILE__, 'settings_updated', __('Settings Updated',WPPM_LANG), 'updated' );		return $plugin_options;	}//function
	
	
	
	/**
	 * hooks the admin notices to the options page
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * needed so the update message can be used
	 *
	 */
	function _admin_notices() {
		
    	settings_errors(__FILE__);
    	
    }//function
	
}//class
?>