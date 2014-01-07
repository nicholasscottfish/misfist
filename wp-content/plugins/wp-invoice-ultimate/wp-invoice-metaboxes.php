<?php
class WPIU_Metaboxes extends WPIU{
	
	
	
	/**
	 * Core class constructor
	 *
	 * @package WPIU
	 * @since 3.3
	 *
	 * hook class funcs into wordpress hooks
	 */
	function __construct(){
		
		//add meta boxes to post type edit
		add_action('add_meta_boxes', array(&$this, '_add_meta_boxes'));
		
		//register the css and js for post type edit
		add_action('admin_enqueue_scripts', array(&$this, '_register_dependencies'));
		
		//save meta box values
		add_action('save_post', array(&$this, '_save_meta_boxes'), 1, 2);
		
		//ajax get client details
		add_action('wp_ajax_nopriv_get_client_data', array(&$this, '_get_client_data'));
		add_action('wp_ajax_get_client_data', array(&$this, '_get_client_data'));
		
	}//function
	
	
	
	
	
	/**
	 * register meta boxes for projects pages
	 *
	 * @package WPIU
	 * @since 3.3
	 *
	 */
	function _add_meta_boxes(){
		
			$this->boxes[] = add_meta_box( 'invoice_notifications', __('Invoice Notifications', WPIU_LANG), array(&$this, '_invoice_notifications_html'), 'wpiu-invoices', 'side', 'high');
			
			$this->boxes[] = add_meta_box( 'invoice_details', __('Invoice Details', WPIU_LANG), array(&$this, '_invoice_details_html'), 'wpiu-invoices', 'side', 'low');
			
			$this->boxes[] = add_meta_box( 'invoice_client', __('Invoice Client', WPIU_LANG), array(&$this, '_invoice_client_html'), 'wpiu-invoices', 'side', 'low');
			
			$this->boxes[] = add_meta_box( 'invoice_items', __('Invoice Items', WPIU_LANG), array(&$this, '_invoice_items_html'), 'wpiu-invoices', 'normal', 'high');
			
			$this->boxes[] = add_meta_box( 'invoice_trans', __('Invoice Transactions', WPIU_LANG), array(&$this, '_invoice_trans_html'), 'wpiu-invoices', 'normal', 'high');

			$this->boxes[] = add_meta_box( 'invoice_donate', __('Invoice Support', WPIU_LANG), array(&$this, '_invoice_support_html'), 'wpiu-invoices', 'normal', 'high');
			
	}//function
	
	
	
	
	/**
	 * register/load required js and css for wpiu-invoices post type
	 *
	 * @package WPIU
	 * @since 3.3
	 *
	 * wordpress really should add a {$posttype}_enqueue_scripts action as use of print_styles is now depriciated
	 *
	 */
	function _register_dependencies($pagenow) {
		
		global $typenow;
		
		if (empty($typenow) && !empty($_GET['post'])) {
			
			$post = get_post($_GET['post']);
			$typenow = $post->post_type;
			
		}//if
		
		if (is_admin() && $pagenow=='post-new.php' || $pagenow=='post.php' && $typenow=='wpiu-invoices') {
			
			//add datepicker|custom js to edit projects screen
			wp_enqueue_script('wpiu-date-picker', WPIU_INC_URL.'js/jquery.ui.datepicker.min.js', array('jquery', 'jquery-ui-core'));
			
			wp_enqueue_script('wpiu-admin', WPIU_INC_URL.'js/admin-js.js');
			$values = array(
							'confirm_delete' => __('Do you really want to remove this item?', WPIU_LANG)
							);
			wp_localize_script('wpiu-admin', 'wpiu', $values);

			//add css for the datepicker and meta boxes styles
			wp_enqueue_style('wpiu-custom-ui-theme',WPIU_INC_URL.'css/ui/custom-theme/jquery-ui-1.8.16.custom.css');
			
			wp_enqueue_style('wpiu-metabox-styles',WPIU_INC_URL.'css/admin.css');
			
		}//if
		
	}//function
	
	
	/**
	 * invoice email metabox html
	 *
	 * @package WPIU
	 * @since 3.3
	 *
	 */
	function _invoice_notifications_html(){
		
		global $post;
		
		$meta = get_post_meta($post->ID, '_wpiu_invoice_meta',true);
		
		echo '<table class="form-table">';
		
					
			echo '<tr>';
				
				if(isset($meta['_wpiu_invoice_meta_emailed'])){
					echo '<td><label for="_wpiu_invoice_meta_email_reminder">'.__('Invoice Sent', WPIU_LANG).':</label></td>';
					echo '<td>';
					echo '<input type="submit" class="button-secondary" name="_wpiu_invoice_meta_email_reminder" value="'.__('Email Reminder', WPIU_LANG).'" />';
					echo '<input type="hidden" name="_wpiu_invoice_meta_emailed" value="1" />';
					echo '</td>';
				}else{
					echo '<td><label for="_wpiu_invoice_meta_email">'.__('Invoice Not Sent', WPIU_LANG).':</label></td>';
					echo '<td><input type="submit" class="button-primary" name="_wpiu_invoice_meta_email" value="'.__('Email Invoice', WPIU_LANG).'" /></td>';
				}
				
			echo '</tr>';
			
			
			
		
		echo '</table>';
		
		
	}//function

	
		
		
	/**
	 * invoice details metabox html
	 *
	 * @package WPIU
	 * @since 3.3
	 *
	 */
	function _invoice_details_html(){
		
		wp_nonce_field(WPIU_RELATIVE,'_wpiu_invoice_meta_nonce');
		
		global $post;
		
		$meta = get_post_meta($post->ID, '_wpiu_invoice_meta',true);
		
		echo '<table class="form-table">';
		
					
			echo '<tr>';
				echo '<td><label for="_wpiu_invoice_meta_due_date">'.__('Due Date', WPIU_LANG).':</label></td>';
				echo '<td><input type="text" class="datepicker large-text" name="_wpiu_invoice_meta_due_date" value="'.$meta['_wpiu_invoice_meta_due_date'].'" /></td>';
			echo '</tr>';

			echo '<tr>';
				echo '<td><label for="_wpiu_invoice_meta_job_number">'.__('Job No', WPIU_LANG).':</label></td>';
				echo '<td><input type="text" class="large-text" name="_wpiu_invoice_meta_job_number" size="16" value="'.$meta['_wpiu_invoice_meta_job_number'].'" /></td>';
			echo '</tr>';
			
			echo '<tr>';
				echo '<td><label for="_wpiu_invoice_meta_invoice_number">'.__('Invoice No', WPIU_LANG).':</label></td>';
				if(isset($meta['_wpiu_invoice_meta_invoice_number'])){
					$invoicenum = $meta['_wpiu_invoice_meta_invoice_number'];
				}else{
					$invoicenum = date('YmdHis');
				}
				echo '<td><input type="text" class="large-text" name="_wpiu_invoice_meta_invoice_number" size="16" value="'.$invoicenum.'" /></td>';
			echo '</tr>';
			
			echo '<tr>';
				echo '<td><label for="_wpiu_invoice_meta_paid">'.__('Paid', WPIU_LANG).':</label></td>';
				if(!isset($meta['_wpiu_invoice_meta_paid'])){$meta['_wpiu_invoice_meta_paid'] = '0.00';}
				echo '<td><input type="text" name="_wpiu_invoice_meta_paid" size="9" class="numeric" value="'.$meta['_wpiu_invoice_meta_paid'].'" /></td>';
			echo '</tr>';
			
			
			
		
		echo '</table>';
		
	}//function	
	
	
	
	
	/**
	 * invoice clients html metabox
	 *
	 * @package WPIU
	 * @since 3.3
	 *
	 */
	function _invoice_client_html(){
		
		global $post;
		
		$meta = get_post_meta($post->ID, '_wpiu_invoice_meta',true);
		
		echo '<!--';
			print_r($meta);
		echo '-->';
		
		echo '<table class="form-table">';
			
			$blogusers = get_users();
			
			echo '<tr>';
				echo '<td>';
					echo '<select name="_wpiu_invoice_meta_client">';
						foreach($blogusers as $user){
							echo '<option value="'.$user->ID.'" '.selected($meta['_wpiu_invoice_meta_client'], $user->ID, false).'>'.$user->display_name.'</option>';	
						}//foreach
					echo '</select>';
					echo 'or <a href="'.admin_url('user-new.php').'" target="_blank">'.__('Add New Client', WPIU_LANG).'</a>';
				echo '</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td style="padding:0px;">';	
					echo '<div id="wpiu_user_meta">'.__('No Client Selected', WPIU_LANG).'</div>';
				echo '</td>';
			echo '</tr>';
			
			
		echo '</table>';
		
	}//function




	/**
	 * invoice items html metabox
	 *
	 * @package WPIU
	 * @since 3.3
	 *
	 */
	function _invoice_items_html(){
		
		global $post;
		
		$options = get_option(WPIU_OPTION);
		$currency = WPIU_Options::get_currency();
		$tax = (isset($options['invoice_payment_tax']))?$options['invoice_payment_tax'].'%':'0%';
		
		$meta = get_post_meta($post->ID, '_wpiu_invoice_meta',true);

		
		echo '<table class="widefat" id="invoice_items">';
			
			echo '<thead><tr>';
				echo '<th width="16"></th>';
				echo '<th>'.__('Title', WPIU_LANG).'</th>';
				echo '<th width="40">'.__('Qty', WPIU_LANG).'</th>';
				echo '<th width="60">'.__('Unit Cost', WPIU_LANG).'</th>';
				echo '<th width="80">'.__('Item Total', WPIU_LANG).'</th>';
			echo '</tr></thead>';
			
			echo '<tbody>';
			
			
			
			
			if(isset($meta['_wpiu_invoice_meta_item']) && is_array($meta['_wpiu_invoice_meta_item']['title'])){
			$index = 0;
			while($index < count($meta['_wpiu_invoice_meta_item']['title'])){
				if($meta['_wpiu_invoice_meta_item']['title'][$index] != ''){
				echo '<tr class="invoice_item_row">';
					echo '<td>';
						echo '<a href="javascript:void(0);" class="wpiu_delete_item"><img src="'.WPIU_IMG.'16/delete.png" /></a>';
					echo '</td>';				
					echo '<td>';
						echo '<input type="text" class="large-text" name="_wpiu_invoice_meta_item[title][]" value="'.$meta['_wpiu_invoice_meta_item']['title'][$index].'" placeholder="'.__('Item Title', WPIU_LANG).'" />';
					echo '</td>';				
					echo '<td>';
						echo '<input type="text" class="large-text numeric" name="_wpiu_invoice_meta_item[qty][]" value="'.$meta['_wpiu_invoice_meta_item']['qty'][$index].'" placeholder="'.__('1', WPIU_LANG).'" />';
					echo '</td>';				
					echo '<td>';
						echo $currency.'<input type="text" size="4" class="numeric" name="_wpiu_invoice_meta_item[unit_price][]" value="'.$meta['_wpiu_invoice_meta_item']['unit_price'][$index].'" placeholder="'.__('10.00', WPIU_LANG).'" />';
					echo '</td>';				
					echo '<td>';
						echo $currency.'<input type="text" size="6" class="disabled" name="_wpiu_invoice_meta_item[sub_total][]" value="'.$meta['_wpiu_invoice_meta_item']['sub_total'][$index].'" disabled="disabled" />';
					echo '</td>';			
				echo '</tr>';
				}
			
			$index++;
			}//while there are items
			}//if there are items
			
			echo '<tr class="invoice_item_row">';
				echo '<td>';
					echo '<a href="javascript:void(0);" class="wpiu_delete_item"><img src="'.WPIU_IMG.'16/delete.png" /></a>';
				echo '</td>';				
				echo '<td>';
					echo '<input type="text" class="large-text" name="_wpiu_invoice_meta_item[title][]" value="" placeholder="'.__('Item Title', WPIU_LANG).'" />';
				echo '</td>';				
				echo '<td>';
					echo '<input type="text" class="large-text numeric" name="_wpiu_invoice_meta_item[qty][]" value="0" placeholder="'.__('1', WPIU_LANG).'" />';
				echo '</td>';				
				echo '<td>';
					echo $currency.'<input type="text" size="4" class="numeric" name="_wpiu_invoice_meta_item[unit_price][]" value="0.00" placeholder="'.__('10.00', WPIU_LANG).'" />';
				echo '</td>';				
				echo '<td>';
					echo $currency.'<input type="text" size="6" class="disabled" name="_wpiu_invoice_meta_item[sub_total][]" value="0.00" disabled="disabled" />';
				echo '</td>';			
			echo '</tr>';
		
			
			echo '</tbody>';
			
						
			echo '<tbody>';
				echo '<tr>';
					echo '<td colspan="5" style="text-align:right;padding-top:20px;padding-bottom:20px;"><a id="invoice_add_another" size="6" class="button-secondary" href="javascript:void(0);">Add new item</a></td>';
				echo '</tr>';
				
				echo '<tr>';
					$sub = (isset($meta['_wpiu_invoice_meta_sub_total']))?$meta['_wpiu_invoice_meta_sub_total']:'0.00';
					echo '<td colspan="4" style="text-align:right;">'.__('Sub Total: ', WPIU_LANG).'</td><td>'.$currency.'<input type="text" size="6" class="disabled" name="_wpiu_invoice_meta_sub_total" value="'.$sub.'" disabled="disabled" /></td>';
				echo '</tr>';
				
				
				echo '<tr>';
					$taxtotal = (isset($meta['_wpiu_invoice_meta_tax']))?$meta['_wpiu_invoice_meta_tax']:'0.00';
					echo '<td colspan="4" style="text-align:right;">'.__('Tax Total: ', WPIU_LANG).'('.$tax.')</td><td>'.$currency.'<input type="text" size="6" class="disabled" name="_wpiu_invoice_meta_sub_total" value="'.$taxtotal.'" disabled="disabled" /></td>';
				echo '</tr>';
				
				
				echo '<tr>';
					$total = (isset($meta['_wpiu_invoice_meta_total']))?$meta['_wpiu_invoice_meta_total']:'0.00';
					echo '<td colspan="4" style="text-align:right;">'.__('Total: ', WPIU_LANG).'</td><td>'.$currency.'<input type="text" size="6" class="disabled" name="_wpiu_invoice_meta_total" value="'.$total.'" disabled="disabled" /></td>';
				echo '</tr>';
				
				
				echo '<tr>';
					echo '<td colspan="5" style="text-align:right;padding-top:20px;padding-bottom:20px;"><input type="submit" class="button-primary" value="Update Invoice" /></td>';
				echo '</tr>';

				
				
			echo '</tbody>';
		
		echo '</table>';
		
	}//function
	
	


	/**
	 * invoice trans html metabox
	 *
	 * @package WPIU
	 * @since 3.3
	 *
	 */
	function _invoice_trans_html(){
		
				
		global $post;
		
		$meta = get_post_meta($post->ID, '_wpiu_invoice_meta_trans',true);
		
		if($meta){

		
		echo '<table class="widefat" id="invoice_trans">';
		
		echo '<thead><tr>';
				echo '<th>'.__('Amount', WPIU_LANG).'</th>';	
				echo '<th>'.__('Date', WPIU_LANG).'</th>';
				echo '<th>'.__('Transaction ID', WPIU_LANG).'</th>';
				echo '<th>'.__('Details', WPIU_LANG).'</th>';		
		echo '</tr></thead>';
		
		echo '<tbody>';
		
		foreach(array_reverse($meta) as $trans){
			echo '<tr>';
				echo '<td>'.$trans['mc_gross'].'</td>';
				echo '<td>'.$trans['payment_date'].'</td>';
				echo '<td><a target="_blank" href="https://www.paypal.com/uk/cgi-bin/webscr?cmd=_view-a-trans&id='.$trans['txn_id'].'">'.$trans['txn_id'].'</a></td>';

				echo '<td width="50%">';
					echo '<a href="javascript:void(0);" class="trans_show">'.__('Show Details', WPIU_LANG).'</a>';
					echo '<div class="details">';
						foreach($trans as $transk => $transv){
							echo '<strong>'.$transk.': </strong> '.$transv.'<br/>'; 	
						}
					echo '</div>';
				echo '</td>';
			echo '</tr>';
		}//foreach
		
		echo '</tbody>';

		
		
		echo '</table>';			
			
			
			
		}//meta	
	}//function



	
	
	/**
	 * invoice support html metabox
	 *
	 * @package WPIU
	 * @since 3.3
	 *
	 */
	function _invoice_support_html(){
		
		_e('<p style="text-align:center"><strong>Want to show your appreciation for the plugin?</strong><br /><br /><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DVEW7VSBB8NYS" traget="_blank"><img src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_LG.gif" /></a></p>', WPIU_LANG);
				
	}//function



	
	
	
	
	
	/**
	 * metabox save function adds post meta and creates new action for ghe notfication system
	 *
	 * @package WPIU
	 * @since 3.3
	 *
	 */
	function _save_meta_boxes($post_id, $post){
		
		if ( !wp_verify_nonce( isset($_POST['_wpiu_invoice_meta_nonce']), WPIU_RELATIVE )) {
    		return $post->ID;
    	}//if
    	
    	if ( !current_user_can( 'edit_post', $post->ID )){
        	return $post->ID;
    	}//if
    	
        $meta = array();
        
        foreach($_POST as $key => $value){
        
        	if(preg_match("/_wpiu_invoice_meta_/i",$key) && $value != ''){
        
        		$meta[$key] = $value;
        
        	}//if	
        
        }//foreach
        
        
        if(isset($_POST['_wpiu_invoice_meta_email']) && $meta['_wpiu_invoice_meta_client'] && $meta['_wpiu_invoice_meta_emailed'] != 1){
        	
        	//send invoice
        	WPIU_Notifications::_send_invoice($meta, $post->ID);
        	//set flag
        	$meta['_wpiu_invoice_meta_emailed'] = 1;
        		
        }else{
        	unset($meta['_wpiu_invoice_meta_email']);
        }
        
        
        if(isset($_POST['_wpiu_invoice_meta_email_reminder']) && $meta['_wpiu_invoice_meta_client']){
        	
        	//send invoice reminder
        	WPIU_Notifications::_send_invoice_reminder($meta, $post->ID);
        	
        }else{
        	unset($meta['_wpiu_invoice_meta_email_reminder']);
        }
        
        
        
        
        $meta['_wpiu_invoice_meta_sub_total'] = 0;
        //calculate the sub totals for each item
        if(isset($meta['_wpiu_invoice_meta_item']) && is_array($meta['_wpiu_invoice_meta_item']['title'])){
			$index = 0;
			while($index < count($meta['_wpiu_invoice_meta_item']['title'])){
				
				//format the unit price first
				$meta['_wpiu_invoice_meta_item']['unit_price'][$index] = number_format($meta['_wpiu_invoice_meta_item']['unit_price'][$index], 2, '.', '');
				//add up row and add to $meta
				$meta['_wpiu_invoice_meta_item']['sub_total'][$index] = number_format($meta['_wpiu_invoice_meta_item']['unit_price'][$index] * $meta['_wpiu_invoice_meta_item']['qty'][$index], 2, '.', '');
				//add row total to sub total
				$meta['_wpiu_invoice_meta_sub_total'] = number_format($meta['_wpiu_invoice_meta_sub_total'] + $meta['_wpiu_invoice_meta_item']['sub_total'][$index], 2, '.', '');
				
				//increment the array
				$index++;
			}//while
        }//if
        
        
      	
      	
      	//add final total - later needs to sum tax etc, but for now just total
		$meta['_wpiu_invoice_meta_total'] = $meta['_wpiu_invoice_meta_sub_total'];
		
		$options = get_option(WPIU_OPTION);
		if(isset($options['invoice_payment_tax'])){
			
			//$totalpercent = 100 + $options['invoice_payment_tax']; // for ex 105%
			
			$percentage = 1 . '.' . $options['invoice_payment_tax'];
			
			$fulltotal = number_format($meta['_wpiu_invoice_meta_total'] * $percentage , 2, '.', '');
			
			$taxamount = number_format($fulltotal - $meta['_wpiu_invoice_meta_total'], 2, '.', '');
			
			$meta['_wpiu_invoice_meta_tax'] = $taxamount;
			$meta['_wpiu_invoice_meta_total'] = $fulltotal;
			
		}else{
			
			$meta['_wpiu_invoice_meta_tax'] = '0.00';
			
		}//if tax
        
 


        if(get_post_meta($post->ID, '_wpiu_invoice_meta', true)) { // If the custom field already has a value
        
            update_post_meta($post->ID, '_wpiu_invoice_meta', $meta);
            
        } else { // If the custom field doesn't have a value
        
            add_post_meta($post->ID, '_wpiu_invoice_meta', $meta);
            
        }//else


        return $post->ID;
        
	}//function
	
	
	
	/**
	 * ajax get client data function
	 *
	 * @package WPIU
	 * @since 3.3
	 * setup so it can be used on the front end to get the client data as well as using it for the invoice creation
	 *
	 */
	function _get_client_data($client_id){
		if(!empty($_POST['client_id'])) {	
			$user = get_userdata($_POST['client_id']);
		}elseif(!empty($client_id)){	
			$user = get_userdata($client_id);
		}

			echo '<table class="form-table">';
			
			echo '<tr>';
			echo '<td><label>'.__('Name', WPIU_LANG).':</label></td>';
			echo '<td><input type="text" class="large-text" value="'.$user->display_name.'" disabled="disabled" /></td>';
			echo '</tr>';
			
			echo '<tr>';
			echo '<td><label>'.__('ID', WPIU_LANG).':</label></li>';
			echo '<td><input type="text" class="large-text" value="'.$user->ID.'" disabled="disabled" /></td>';
			echo '</tr>';
			
			echo '<tr>';
			echo '<td><label>'.__('Email', WPIU_LANG).':</label></td>';
			echo '<td><input type="text" class="large-text" value="'.$user->user_email.'" disabled="disabled" /></td>';
			echo '</tr>';
			
			echo '<tr>';
			echo '<td><label>'.__('User Since', WPIU_LANG).':</label></td>';
			echo '<td><input type="text" class="large-text" value="'.$user->user_registered.'" disabled="disabled" /></td>';
			echo '</tr>';
			
			echo '<tr>';
			echo '<td><label>'.__('Company', WPIU_LANG).':</label></td>';
			echo '<td><input type="text" class="large-text" value="'.get_the_author_meta('company_name',$user->ID).'" disabled="disabled" /></td>';
			echo '</tr>';
			
			echo '<tr>';
			echo '<td><label>'.__('Phone', WPIU_LANG).':</label></td>';
			echo '<td><input type="text" class="large-text" value="'.get_the_author_meta('company_phone',$user->ID).'" disabled="disabled" /></td>';
			echo '</tr>';
			
			echo '<tr>';
			echo '<td><label>'.__('Street', WPIU_LANG).':</label></td>';
			echo '<td><input type="text" class="large-text" value="'.get_the_author_meta('company_street',$user->ID).'" disabled="disabled" /></td>';
			echo '</tr>';
			
			echo '<tr>';
			echo '<td><label>'.__('City', WPIU_LANG).':</label></td>';
			echo '<td><input type="text" class="large-text" value="'.get_the_author_meta('company_city',$user->ID).'" disabled="disabled" /></td>';
			echo '</tr>';
			
			echo '<tr>';
			echo '<td><label>'.__('Zip', WPIU_LANG).':</label></td>';
			echo '<td><input type="text" class="large-text" value="'.get_the_author_meta('company_zip',$user->ID).'" disabled="disabled" /></td>';
			echo '</tr>';
			
			echo '<tr>';
			echo '<td colspan="2" style="text-align:center;"><a href="'.admin_url('user-edit.php?user_id='.$user->ID).'" target="_blank" class="button-secondary">'.__('Edit Client', WPIU_LANG).'</a></td>';
			echo '</tr>';
		
		
			echo '</table>';
			
			
			
			
			
			
			
			
			if($_POST['doing_ajax']){
				die();
			}	
	}//function	
	
	
	
}//class
?>