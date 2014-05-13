<?php
class WPPM_Ultimate_Metaboxes extends WPPM_Ultimate{
	
	
	
	/**
	 * construct - setup all the actions
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function __construct(){
		
		//add meta boxes to post type edit
		add_action('add_meta_boxes', array(&$this, '_add_meta_boxes'));
		
		//register the css and js for post type edit
		add_action('admin_init', array(&$this, '_register_dependencies'));
		
		//save meta box values
		add_action('save_post', array(&$this, '_save_meta_boxes'), 1, 2);
		
		//delete attachments
		add_action('wp_ajax_project_delete_att', array(&$this, '_ajax_delete_attachment'));

	}//function
	
	


	/**
	 * register meta boxes for projects pages
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function _add_meta_boxes(){
		
			$this->boxes[] = add_meta_box( 'project_details', __('Project Details', WPPM_LANG), array(&$this, '_project_details_html'), 'projects', 'side', 'low');
			
			$this->boxes[] = add_meta_box( 'project_users', __('Project Users', WPPM_LANG), array(&$this, '_project_users_html'), 'projects', 'side', 'low');
			
			$this->boxes[] = add_meta_box( 'project_attachments', __('Project Attachments', WPPM_LANG), array(&$this, '_project_attachments_html'), 'projects', 'normal', 'high');
			
			$this->boxes[] = add_meta_box( 'project_donate', __('Project Support', WPPM_LANG), array(&$this, '_project_support_html'), 'projects', 'side', 'low');
			
	}//function
		



	/**
	 * register/load required js and css for projects post type
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * wordpress really should add a {$posttype}_enqueue_scripts action as use of print_styles is now depriciated
	 *
	 */
	function _register_dependencies() {
		
		global $pagenow, $typenow;
		
		if (empty($typenow) && !empty($_GET['post'])) {
			
			$post = get_post($_GET['post']);
			$typenow = $post->post_type;
			
		}//if
		
		if (is_admin() && $pagenow=='post-new.php' || $pagenow=='post.php' && $typenow=='projects') {
			
			//add datepicker|custom js to edit projects screen
			wp_enqueue_script('wppm-date-picker', WPPM_INC_URL.'js/jquery.ui.datepicker.min.js', array('jquery', 'jquery-ui-core'));
			
			wp_enqueue_script('wppm-admin', WPPM_INC_URL.'js/admin-js.js');
			
			//localise the js messages
			$values = array(
							'confirm_delete' => __('Are you sure you want to delete this attachment?', WPPM_LANG), 
							'delete_success' => __('Attachment Deleted!', WPPM_LANG)
							);
							
			wp_localize_script('wppm-admin', 'wppm_lang', $values);

			//add css for the datepicker and meta boxes styles
			wp_enqueue_style('wppm-custom-ui-theme',WPPM_INC_URL.'css/ui/custom-theme/jquery-ui-1.8.16.custom.css');
			
			wp_enqueue_style('wppm-metabox-styles',WPPM_INC_URL.'css/admin.css');
			
		}//if
		
	}//function
		
		
	/**
	 * project details metabox html
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function _project_details_html(){
		
		wp_nonce_field(WPPM_RELATIVE,'_wppm_project_meta_nonce');
		
		global $post;
		
		$meta = get_post_meta($post->ID, '_wppm_project_meta',true);
		
		echo '<table class="form-table">';
		
		
			echo '<tr>';
				echo '<td><label for="_wppm_project_meta_start_date">'.__('Start Date', WPPM_LANG).':</label></td>';
				$start = (isset($meta['_wppm_project_meta_start_date']))?$meta['_wppm_project_meta_start_date']:'';
				echo '<td><input type="text" class="datepicker large-text" name="_wppm_project_meta_start_date" size="10" value="'.$start.'" /></td>';
			echo '</tr>';
			
			echo '<tr>';
				echo '<td><label for="_wppm_project_meta_end_date">'.__('End Date', WPPM_LANG).':</label></td>';
				$end = (isset($meta['_wppm_project_meta_end_date']))?$meta['_wppm_project_meta_end_date']:'';
				echo '<td><input type="text" class="datepicker large-text" name="_wppm_project_meta_end_date" value="'.$end.'" /></td>';
			echo '</tr>';
			
			echo '<tr>';
				echo '<td><label for="_wppm_project_meta_job_number">'.__('Job No', WPPM_LANG).':</label></td>';
				$jobno = (isset($meta['_wppm_project_meta_job_number']))?$meta['_wppm_project_meta_job_number']:'';
				echo '<td><input type="text" class="large-text" name="_wppm_project_meta_job_number" size="16" value="'.$jobno.'" /></td>';
			echo '</tr>';
			
			echo '<tr>';
				echo '<td><label for="_wppm_project_meta_quote_number">'.__('Quote No', WPPM_LANG).':</label></td>';
				$quoteno = (isset($meta['_wppm_project_meta_quote_number']))?$meta['_wppm_project_meta_quote_number']:'';
				echo '<td><input type="text" class="large-text" name="_wppm_project_meta_quote_number" size="16" value="'.$quoteno.'" /></td>';
			echo '</tr>';
			
			echo '<tr>';
				echo '<td><label for="_wppm_project_meta_invoice_number">'.__('Invoice No', WPPM_LANG).':</label></td>';
				$invoiceno = (isset($meta['_wppm_project_meta_invoice_number']))?$meta['_wppm_project_meta_invoice_number']:'';
				echo '<td><input type="text" class="large-text" name="_wppm_project_meta_invoice_number" size="16" value="'.$invoiceno.'" /></td>';
			echo '</tr>';
			
			echo '<tr>';
				echo '<td><label for="_wppm_project_meta_paid">'.__('Paid', WPPM_LANG).':</label></td>';
				echo '<td><select name="_wppm_project_meta_paid" id="wppm_paid">';
					echo '<option value="No" '.selected($meta['_wppm_project_meta_paid'], 'No', false).'>'.__('No', WPPM_LANG).'</option>';
					echo '<option value="Part" '.selected($meta['_wppm_project_meta_paid'], 'Part', false).'>'.__('Part', WPPM_LANG).'</option>';
					echo '<option value="Yes" '.selected($meta['_wppm_project_meta_paid'], 'Yes', false).'>'.__('Yes', WPPM_LANG).'</option>';
					$paid = (isset($meta['_wppm_project_meta_paid_amount']))?$meta['_wppm_project_meta_paid_amount']:'';
					echo '</select> <span class="amount_right"><input id="wppm_paid_amount" type="text" name="_wppm_project_meta_paid_amount" size="9" value="'.$paid.'" /> %</span></td>';
			echo '</tr>';
			
			echo '<tr>';
				echo '<td><label for="_wppm_project_meta_completed">'.__('Completed', WPPM_LANG).':</label></td>';
				echo '<td><select name="_wppm_project_meta_completed">';
					$values = array(0,10,20,30,40,50,60,70,80,90,100);
					foreach($values as $value){
						echo '<option value="'.$value.'" '.selected($meta['_wppm_project_meta_completed'], $value, false).'>'.$value.'%</option>';
					}
				echo '</select></td>';
			echo '</tr>';
		
		echo '</table>';
		
	}//function
		
		
	/**
	 * project users html metabox
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function _project_users_html(){
		
		global $post;
		
		$meta = get_post_meta($post->ID, '_wppm_project_meta',true);
		
		echo '<!--';
			print_r($meta);
		echo '-->';
		
		echo '<table class="form-table">';
			
			$blogusers = get_users();
			
			foreach($blogusers as $user){
				echo '<tr>';
				
					echo '<td><input type="checkbox" name="_wppm_project_meta_users[]" value="'.$user->ID.'" ';
					
					if(isset($meta['_wppm_project_meta_users']) && is_array($meta['_wppm_project_meta_users'])){
						
						if(in_array($user->ID,$meta['_wppm_project_meta_users'])){
							
							echo 'checked="checked"';
							
						}//if
						
					}//if
					echo ' /></td>';
					
					echo '<td>'.$user->display_name.'</td>';
				
				echo '</tr>';
			}
		echo '</table>';
		
	}//function
		
		
		
	/**
	 * attachments metabox html
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function _project_attachments_html(){
		
		global $post;
		
		$meta = get_post_meta($post->ID, '_wppm_project_meta',true);
		
		$attachments = get_children( 
											array(
											'post_parent' => get_the_ID(),
											'post_type' => 'attachment',
											'orderby' => 'menu_order ASC, ID',
											'order' => 'DESC'
												)
											);
											
		if($attachments){
		
			echo '<table class="widefat">';
			
				echo '<thead><tr><th>'.__('Attachment Name', WPPM_LANG).'</th><th colspan="3">'.__('Attachment Url', WPPM_LANG).'</th></tr></thead>';
			
				echo '<tbody class="form-table">';
					
					foreach($attachments as $att){
						
						echo '<tr id="att-'.$att->ID.'">';
						
						echo '<td><a target="_blank" href="'.$att->guid.'">'.$att->post_title.'</a></td>';
						
						echo '<td>'.$att->guid.'</td>';
						
						echo '<td class="actions"><a href="'.admin_url('media.php?attachment_id='.$att->ID.'&action=edit').'">'.__('Edit', WPPM_LANG).'</a></td>';
						
						echo '<td class="actions"><a href="javascript:void(0);" data-att-id="'.$att->ID.'" class="delete-att">'.__('Delete', WPPM_LANG).'</a></td>';
						
						echo '</tr>';
						
					}
				
				echo '</tbody>';
				
			echo '</table>';
			
		}else{
			
			echo '<p class="no-attachments">'.__('There are no attachments for this project yet.', WPPM_LANG).'</p>';
			
		}
	}//function
	
	
	
	
	
	/**
	 * project support html metabox
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function _project_support_html(){
		
		_e('<p style="text-align:center"><strong>Want to show your appreciation for the plugin?</strong><br /><br /><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=P7CYJYR7623K6" traget="_blank"><img src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_LG.gif" /></a></p>', WPPM_LANG);
		
	}//function
		
		
		
		
		
		
		
		
	/**
	 * metabox save function adds post meta and creates new action for ghe notfication system
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function _save_meta_boxes($post_id, $post){
		
		if ( !wp_verify_nonce( isset($_POST['_wppm_project_meta_nonce']), WPPM_RELATIVE )) {
    		return $post->ID;
    	}//if
    	
    	if ( !current_user_can( 'edit_post', $post->ID )){
        	return $post->ID;
    	}//if
    	
        $meta = array();
        
        foreach($_POST as $key => $value){
        
        	if(preg_match("/_wppm_project_meta_/i",$key) && $value != ''){
        
        		$meta[$key] = $value;
        
        	}//if	
        
        }//foreach
        
        $meta = apply_filters('WPPM_before_meta_save',$meta, $meta);


        if(get_post_meta($post->ID, '_wppm_project_meta', true)) { // If the custom field already has a value
        
            update_post_meta($post->ID, '_wppm_project_meta', $meta);
            
        } else { // If the custom field doesn't have a value
        
            add_post_meta($post->ID, '_wppm_project_meta', $meta);
            
        }//else
        
        
		//sortable by completed
        if(isset($_POST['_wppm_project_meta_completed'])){
        	
        	$sortable = $_POST['_wppm_project_meta_completed'];
        	
        }else{
        	
        	$sortable = 0;
        	
        }
        
        if(get_post_meta($post->ID, '_wppm_custom_sort', true)) { // If the custom field already has a value
        
            update_post_meta($post->ID, '_wppm_custom_sort', $sortable);
            
        } else { // If the custom field doesn't have a value
        
            add_post_meta($post->ID, '_wppm_custom_sort', $sortable);
            
        }//else
        
        //sortable by end date
        if(isset($_POST['_wppm_project_meta_end_date'])){
       	
       	$sortable = strtotime($_POST['_wppm_project_meta_end_date'].' 00:00');
       	
       	}else{
       	
       		$sortable = 0;
       		
       	}
        
        if(get_post_meta($post->ID, '_wppm_custom_sort_end_date', true)) { // If the custom field already has a value
        
            update_post_meta($post->ID, '_wppm_custom_sort_end_date', $sortable);
            
        } else { // If the custom field doesn't have a value
        
            add_post_meta($post->ID, '_wppm_custom_sort_end_date', $sortable);
            
        }//else
        
        //sortable by paid
        if(isset($_POST['_wppm_project_meta_paid'])){
        	
        	if($_POST['_wppm_project_meta_paid'] == 'Yes'){
        		
        		$sortable = 1;
        		
        	}
        	
        	elseif($_POST['_wppm_project_meta_paid'] == 'Part' && !isset($_POST['_wppm_project_meta_paid_amount'])){
        		
        		$sortable = 0.5;
        	
        	}
        	
        	elseif($_POST['_wppm_project_meta_paid'] == 'Part' && isset($_POST['_wppm_project_meta_paid_amount'])){
        	
        		$string = preg_replace('#[^0-9]#','',strip_tags($_POST['_wppm_project_meta_paid_amount']));
        		$sortable = '0.'.$string;
        	
        	}
        	
        	elseif($_POST['_wppm_project_meta_paid'] == 'No'){
        	
        		$sortable = 0.00;
        	
        	}	
        	
        }else{
        
        	$sortable = 0;
        
        }
        
        
        if(get_post_meta($post->ID, '_wppm_custom_sort_paid', true)) { // If the custom field already has a value
        
            update_post_meta($post->ID, '_wppm_custom_sort_paid', $sortable);
        
        } else { // If the custom field doesn't have a value
        
            add_post_meta($post->ID, '_wppm_custom_sort_paid', $sortable);
        
        }//else
        
        
        
        return $post->ID;
        
	}//function
	



	/**
	 * ajax delete post attachments
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function _ajax_delete_attachment(){
		
		if(isset($_POST['att_delete_id']) && isset($_POST['_wppm_project_meta_nonce']) && wp_verify_nonce( $_POST['_wppm_project_meta_nonce'], plugin_basename(__FILE__))){
			
			if(wp_delete_attachment($_POST['att_delete_id'])){
				
				echo 'success';
			
			}else{
				
				echo __('An Error Occured, Please Try Again.', WPPM_LANG);
				
			}
		
		}
		
		die();
		
	}//function
	
		
		
	
	
}//class
?>