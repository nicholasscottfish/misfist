<?php
/*
Plugin Name: WP Invoices Ultimate
Plugin URI: http://no-half-pixels.com/portfolio/wp-invoices-ultimate/
Description: Simple to use invoicing system that can intergrate with Paypal. Very simple, very flexble.
Author: Lee Mason
Version: 0.1.6
Author URI: http://no-half-pixels.com
License: GPL2
*/



/*  
	Copyright 2011  Lee Mason  (email : contact@no-half-pixels.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/*
:: NEW FOR 0.1.6 ::

- bug fix for date in metabox


:: TODO FOR NEXT VERSION ::

- add support for custom notification messages
- maybe add support for per invoice tax rules

*/



/**
 * Setup some statics, check wordpress version, load text domain
 *
 * @package WPIU
 * @since 3.3
 *
 * wordpress version needs to be 3.3 for some of the new features. mainly wp_editor()
 * use a static for lang makes coding easier
 */
global $wp_version;
if ( version_compare( $wp_version, '3.3', '<' ) ) {
	wp_die( __( 'Wordpress 3.3 or greater is required for this plugin to function.' ) );
}

if ( ! defined( 'WP_CONTENT_URL' ) ) {
	define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content' );
}

if ( ! defined( 'WP_PLUGIN_URL' ) ) {
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
}

define ( 'WPIU_PLUGIN', __FILE__ );

define ( 'WPIU_DIR', dirname(__FILE__).'/' );

define ( 'WPIU_URL', plugins_url('/',__FILE__) );

define ( 'WPIU_RELATIVE', plugin_basename(__FILE__) );					//to get plugin file

define ( 'WPIU_INC', dirname(__FILE__).'/includes/' );

define ( 'WPIU_INC_URL', plugins_url('/includes/',__FILE__) ); // for urls instead of DIR

define ( 'WPIU_IMG', plugins_url('/images/',__FILE__) );

define ( 'WPIU_OPTION', '_WPIU_Options' );

define ( 'WPIU_LANG', 'WPIU_LANG' );


require_once (WPIU_DIR . 'wp-invoice-metaboxes.php');
require_once (WPIU_DIR . 'wp-invoice-client.php');
require_once (WPIU_DIR . 'wp-invoice-notifications.php');
require_once (WPIU_DIR . 'wp-invoice-options.php');
require_once (WPIU_DIR . 'wp-invoice-ipn-log.php');
require_once (WPIU_DIR . 'wp-invoice-help.php');


global $WPIU;
$WPIU[] = new WPIU;
$WPIU[] = new WPIU_Metaboxes;
$WPIU[] = new WPIU_Client;
$WPIU[] = new WPIU_Notifications;
$WPIU[] = new WPIU_Options;
$WPIU[] = new WPIU_Ipn_Log;
$WPIU[] = new WPIU_Help;


require_once (WPIU_DIR . 'gateways/paypal.php');
$WPIU[] = new WPIU_Paypal;




class WPIU{
	
	
	
	/**
	 * Core class constructor
	 *
	 * @package WPIU
	 * @since 3.3
	 *
	 * hook class funcs into wordpress hooks
	 */
	function __construct(){
		
		load_plugin_textdomain( WPIU_LANG, false, basename( dirname( __FILE__ ) ) . '/languages' );//translations
		
		add_action('init', array(&$this,'_register_post_type'), 0);//register post type
		
		//add_filter('post_row_actions', array(&$this,'_action_row'), 10, 2);//filter the post row actions (add send reminder button) - doesnt work yet
		
		add_action('admin_head', array(&$this, '_post_type_css_head'));//custom icon for post type
		
		add_filter('post_updated_messages', array(&$this, '_set_post_type_messages'));//custom messages for post type
		
		add_filter('manage_edit-wpiu-invoices_columns', array(&$this, '_projects_edit_columns'));//custom headers in list projects table
		
		add_action('manage_posts_custom_column', array(&$this, '_post_type_column_data'));//custom data in list projects table
		
		add_filter('template_redirect', array(&$this,'_post_type_template_smart'));//load projects template
		
		
		//flush rules if needed
		register_activation_hook( __FILE__, array(&$this,'_activation') );
		register_deactivation_hook( __FILE__, array(&$this,'_deactivation') );
		
	}//function
	

	
	
	
	/**
	 * Registers the 'wpiu-invoices' post type
	 *
	 * @package WPIU
	 * @since 3.3
	 *
	 *
	 */
	function _register_post_type(){
		$labels = array(
		'name' => __( 'Invoices',WPIU_LANG),
		'singular_name' => __( 'Invoice',WPIU_LANG ),
		'add_new' => __('Add New',WPIU_LANG),
		'add_new_item' => __('Add New Invoice',WPIU_LANG),
		'edit_item' => __('Edit Invoice',WPIU_LANG),
		'new_item' => __('New Invoice',WPIU_LANG),
		'view_item' => __('View Invoice',WPIU_LANG),
		'search_items' => __('Search Invoice',WPIU_LANG),
		'not_found' =>  __('No Invoices found',WPIU_LANG),
		'not_found_in_trash' => __('No Invoices found in Trash',WPIU_LANG), 
		'parent_item_colon' => ''
	  );
	  
	  $args = array(
		'labels' => $labels,
		'public' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'rewrite' => true, 
		'query_var' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title','editor'),
		'menu_icon' => WPIU_IMG .'16/icon.png'
	  ); 
	  
	  register_post_type( __( 'wpiu-invoices' , WPIU_LANG), $args);
		
	}//function
	
	
	
	function _action_row($actions, $post){
    //check for your post type
    if ($post->post_type == 'wpiu-invoices'){
    	
        $actions['send_reminder'] = '<a href="'.esc_url( wp_nonce_url( add_query_arg(array('post_type' => 'wpiu-invoices', 'action' => 'send-invoice-reminder', 'invoice-id' => $post->ID), admin_url('edit.php')) , 'bulk-posts')).'">Send Reminder</a>';
        
        unset($actions['inline hide-if-no-js']);
    }
    return $actions;
}
	
	
	
	/**
	 * add some inline css for post type 'wpiu-invoices' admin pages
	 *
	 * @package WPIU
	 * @since 3.3
	 *
	 * used to change the icon used on the pages - needs a hook in core
	 *
	 */
	function _post_type_css_head() {global $post_type;

		if ( ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'wpiu-invoices' ) || ( isset( $post_type ) && $post_type == 'wpiu-invoices' ) ){
			
			echo '<style type="text/css">'."\n";
			echo '#icon-edit {background:transparent url("'. WPIU_IMG .'32/icon.png") no-repeat;}'."\n";
			echo '.column-invoice_details {width:20% !important;}'."\n";
			echo '.column-invoice_client {width:11% !important;}'."\n";
			echo '.column-invoice_due {width:8% !important;}'."\n";
			echo '.column-invoice_paid {width:8%;text-align:center !important;}'."\n";
			echo '</style>'."\n";

		}//if
		
	}//function
	

	
	
	
	
	/**
	 * change the default notification messages for custom post type 'wpiu-invoices'
	 *
	 * @package WPIU
	 * @since 3.3
	 *
	 * needs a hook in core
	 *
	 */
	function _set_post_type_messages($messages){
		global $post;
		$messages['wpiu-invoices'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __('Invoice updated. <a href="%s">View Invoice</a>',WPIU_LANG), esc_url( get_permalink($post->ID) ) ),
			2 => __('Custom field updated.',WPIU_LANG),
			3 => __('Custom field deleted.',WPIU_LANG),
			4 => __('Invoice updated.',WPIU_LANG),
			/* translators: %s: date and time of the revision */
			5 => isset($_GET['revision']) ? sprintf( __('Invoice restored to revision from %s',WPIU_LANG), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __('Invoice published. <a href="%s">View Invoice</a>',WPIU_LANG), esc_url( get_permalink($post->ID) ) ),
			7 => __('Invoice saved.',WPIU_LANG),
			8 => sprintf( __('Invoice submitted. <a target="_blank" href="%s">Preview Invoice</a>',WPIU_LANG), esc_url( add_query_arg( 'preview', 'true', get_permalink($post->ID) ) ) ),
			9 => sprintf( __('Invoice scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Invoice</a>',WPIU_LANG),
			  // translators: Publish box date format, see http://php.net/date
			  date_i18n( __( 'M j, Y @ G:i' ,WPIU_LANG), strtotime( $post->post_date ) ), esc_url( get_permalink($post->ID) ) ),
			10 => sprintf( __('Invoice draft updated. <a target="_blank" href="%s">Preview Invoice</a>',WPIU_LANG), esc_url( add_query_arg( 'preview', 'true', get_permalink($post->ID) ) ) ),
		);

		return $messages;
	}//function
	
	
	
	/**
	 * add some custom headers to the list of custom posts 'wpiu-invoices'
	 *
	 * @package WPIU
	 * @since 3.3
	 *
	 * this onlys registers the thead/tfoot labels - another func hooks the fields 
	 *
	 */
	function _projects_edit_columns($columns){
		
		//set new name for date
		$columns['date'] = __('Created',WPIU_LANG);
		
		//take date off array for order
		$date = array_pop($columns);
		//add new cols and re-add date to the end of stack
		$columns['invoice_details'] = __('Invoice Details',WPIU_LANG);
		$columns['invoice_client'] = __('Client',WPIU_LANG);
		$columns['invoice_due'] = __('Due Date',WPIU_LANG);
		$columns['invoice_paid'] = __('Status',WPIU_LANG);
		$columns['date'] = $date;
		//return the columns
		return $columns;
	}//function


	/**
	 * display project meta in the posts view screen
	 *
	 * @package WPIU
	 * @since 3.3
	 *
	 * this is displayed in the table area and hooks into the fields created above with _projects_edit_columns();
	 *
	 */
	function _post_type_column_data($column){
		
		global $post;
		
		$meta = get_post_meta($post->ID, '_wpiu_invoice_meta', true);//get the project meta into array
		
		switch ($column){
			
			case 'invoice_details':
			
				//display some the meta fileds
				echo __('<strong>Job No:</strong> ', WPIU_LANG);
				
				echo (isset($meta['_wpiu_invoice_meta_job_number']))?$meta['_wpiu_invoice_meta_job_number']:__('N/A', WPIU_LANG);

				echo __('<br/><strong>Invoice No:</strong> ', WPIU_LANG);
				
				echo (isset($meta['_wpiu_invoice_meta_invoice_number']))?$meta['_wpiu_invoice_meta_invoice_number']:__('N/A', WPIU_LANG);
				
			break;
			case 'invoice_client':
			
				if(isset($meta['_wpiu_invoice_meta_client'])){//if project has users
					
						$data = get_userdata($meta['_wpiu_invoice_meta_client']);
						
						echo '<a href="'.admin_url('user-edit.php?user_id='.$data->ID).'">'.$data->display_name.'</a>';//echo profile edit link for each user

						
				}//if
				else{
					echo __('Not Assigned', WPIU_LANG);
				}
							
			break;
			case 'invoice_due':
			
				echo (isset($meta['_wpiu_invoice_meta_due_date']))?$meta['_wpiu_invoice_meta_due_date']:__('N/A', WPIU_LANG);
			
			break;
			case 'invoice_paid':
			
			if(isset($meta['_wpiu_invoice_meta_paid']) && $meta['_wpiu_invoice_meta_paid'] >= $meta['_wpiu_invoice_meta_total']){
				$echo = '<span style="color:green;font-weight:bold;">Paid</span>';
			}elseif($meta['_wpiu_invoice_meta_paid'] <= $meta['_wpiu_invoice_meta_total'] && $meta['_wpiu_invoice_meta_paid'] != '0.00'){
				$echo = '<span style="color:orange;font-weight:bold;">Part Paid</span>';
			}else{
				$echo = '<span style="color:red;font-weight:bold;">Not Paid</span>';
			}
			
			echo $echo;
			
			break;
			
		}//switch
		
	}//function




	/**
	 * smart page template loading
	 *
	 * @package WPIU
	 * @since 3.3
	 *
	 * if theme has a template for the projects post type (using wp naming structure 'single-projects.php') load that, 
	 * if not load template from plugin.
	 *
	 */	
	function _post_type_template_smart(){
		
		global $post;
		
		$template_name = 'single-wpiu-invoices.php';
		
		if ( is_single() && 'wpiu-invoices' == get_post_type() ){
			
		    $template = locate_template(array($template_name), true);
		    
		    if(empty($template)) {
		      
		      include(WPIU_DIR . 'template/' . $template_name);
		      
		      exit();
		    }//if empty template
		    
		}//if
		
	}//function
	
	
	
	
	function _activation() {
    	$this->_register_post_type();
    	flush_rewrite_rules();
	}
	
	function _deactivation() {
    	flush_rewrite_rules();
	}

	
	
	
}//class



?>