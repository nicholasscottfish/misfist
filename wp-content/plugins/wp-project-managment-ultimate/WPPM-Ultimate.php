<?php
/*
Plugin Name: WP Project Managment Ultimate
Plugin URI: http://no-half-pixels.com/portfolio/wp-project-managment-ultimate/
Description: Simple to use project managment post type for designers / freelancers / anyone. Set project specifics add description and project details, then monitor the project through the comments section, with special front end image/file upload in comments. Very simple, very easy. Use the built in comment system of WordPress to increase your productivity.
Author: Lee Mason
Version: 1.0.7
Author URI: http://no-half-pixels.com
License: GPL2
*/



/*  Copyright 2011  Lee Mason  (email : contact@no-half-pixels.com)

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

:: NEW TO 1.0.7 ::

- bug fixes

*/



/**
 * Setup some statics, check wordpress version, load text domain
 *
 * @package WPPM
 * @since 3.3
 *
 * wordpress version needs to be 3.3 for some of the new features. mainly wp_editor()
 * use a static for lang makes coding easier
 */
global $wp_version;
if ( version_compare( $wp_version, '3.3', '<' ) ) {
	wp_die( __( 'Wordpress 3.0 or greater is required for this plugin to function.' ) );
}

if ( ! defined( 'WP_CONTENT_URL' ) ) {
	define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content' );
}

if ( ! defined( 'WP_PLUGIN_URL' ) ) {
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
}

define ( 'WPPM_PLUGIN', __FILE__ );

define ( 'WPPM_DIR', dirname(__FILE__).'/' );

define ( 'WPPM_URL', plugins_url('/',__FILE__) );

define ( 'WPPM_RELATIVE', plugin_basename(__FILE__) );					//to get plugin file

define ( 'WPPM_INC', dirname(__FILE__).'/includes/' );

define ( 'WPPM_INC_URL', plugins_url('/includes/',__FILE__) ); // for urls instead of DIR

define ( 'WPPM_IMG', plugins_url('/images/',__FILE__) );

define ( 'WPPM_OPTION', '_WPPM_Options' );

define ( 'WPPM_LANG', 'WPPM_LANG' );





/**
 * Require files for functionality
 *
 * @package WPPM
 * @since 3.3
 *
 * all files include classes for specific parts of the plugins operation, classes used further down
 */
require_once( WPPM_DIR.'WPPM-Options.php' );
require_once( WPPM_DIR.'WPPM-Metaboxes.php' );
require_once( WPPM_DIR.'WPPM-Notifications.php' );
require_once( WPPM_DIR.'WPPM-Usage.php' );
require_once( WPPM_DIR.'WPPM-Help.php' );

require_once( WPPM_DIR.'WPPM-Widget.php' );
require_once( WPPM_DIR.'WPPM-Shortcode.php' );


/**
 * Create global $WPPM object and load classes
 *
 * @package WPPM
 * @since 3.3
 *
 * global not required for anything yet but may in future
 */
global $WPPM;
$WPPM[] = new WPPM_Ultimate;//core class - adds post type and taxonomy
$WPPM[] = new WPPM_Ultimate_Options;//options page class
$WPPM[] = new WPPM_Ultimate_Metaboxes;//metaboxes class
$WPPM[] = new WPPM_Ultimate_Notifications;//notifications class
$WPPM[] = new WPPM_Ultimate_Usage;//usage class
$WPPM[] = new WPPM_Ultimate_Help;//help class



/**
 * Core Class
 *
 * @package WPPM
 * @since 3.3
 *
 * core class - sets up the post type and taxomony, and all associated features
 */
class WPPM_Ultimate{
	
	
	/**
	 * Construct core class
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * add all required actions
	 */
	function __construct(){
		
		load_plugin_textdomain( WPPM_LANG, false, basename( dirname( __FILE__ ) ) . '/languages' );//translations
		
		add_action('init', array(&$this,'_register_post_type'));//register post type
		
		add_action('init', array(&$this,'_register_project_type'));//register post type types taxonomy
		
		add_action('admin_head', array(&$this, '_post_type_css_head'));//custom icon for post type
		
		add_filter('post_updated_messages', array(&$this, '_set_post_type_messages'));//custom messages for post type
		
		add_filter('manage_edit-projects_columns', array(&$this, '_projects_edit_columns'));//custom headers in list projects table
		
		add_action('manage_posts_custom_column', array(&$this, '_post_type_column_data'));//custom data in list projects table
		
		add_filter( 'manage_edit-projects_sortable_columns', array(&$this, '_post_type_sortable_columns'));//tell wordpress whats sortable
		
		add_filter( 'request', array(&$this, '_post_type_sort_function'));
		
		add_action( 'restrict_manage_posts', array(&$this ,'_filter_by_taxonomy') );//add select box to filer by tax type
		
		add_filter('template_redirect', array(&$this,'_post_type_template_smart'));//load projects template
		
		add_action('wp_enqueue_scripts', array(&$this,'_post_type_css'));//load projects css
		
		if(WPPM_Ultimate_Usage::option('project_comment_uploads_allowed') == 'Enable'){//if comment uploads allowed
		
			add_filter('comment_text', array(&$this, '_insert_comment_formatting'));//format inserts
			
			add_filter( 'comment_form', array(&$this, '_add_iframe_upload_to_comments') );//add the upload form
			
			add_action('wp_ajax_nopriv_wppm_ajax_comment_upload', array(&$this, 'ajax_comment_upload'));//ajax page upload form
			
			add_action('wp_ajax_wppm_ajax_comment_upload', array(&$this, 'ajax_comment_upload'));//ajax page upload form
		
		}//if
		
		if(WPPM_Ultimate_Usage::option('project_css')){//if custom css option
		
			add_action('wp_head', array(&$this, '_custom_css'));//add custom css to head
			
		}//if
		
		
		//flush rules if needed
		register_activation_hook( __FILE__, array(&$this,'_activation') );
		register_deactivation_hook( __FILE__, array(&$this,'_deactivation') );
		
		
	}//function
	
	
	
	/**
	 * Registers the 'projects' post type
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 *
	 */
	function _register_post_type(){
		$labels = array(
		'name' => __( 'Projects',WPPM_LANG),
		'singular_name' => __( 'Project',WPPM_LANG ),
		'add_new' => __('Add New',WPPM_LANG),
		'add_new_item' => __('Add New Project',WPPM_LANG),
		'edit_item' => __('Edit Project',WPPM_LANG),
		'new_item' => __('New Project',WPPM_LANG),
		'view_item' => __('View Project',WPPM_LANG),
		'search_items' => __('Search Projects',WPPM_LANG),
		'not_found' =>  __('No Projects found',WPPM_LANG),
		'not_found_in_trash' => __('No Projects found in Trash',WPPM_LANG), 
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
		'supports' => array('title','editor','comments'),
		'menu_icon' => WPPM_IMG .'16/icon.png'
	  ); 
	  
	  register_post_type( __( 'projects' , WPPM_LANG), $args);
		
		
	}//function
	
	
	
	
	/**
	 * Registers taxomony 'project-type' for post type 'projects'
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 */
	function _register_project_type(){
		
	  $labels = array(
	    'name' => _x( 'Project Type', 'taxonomy general name' ),
	    'singular_name' => _x( 'Type', 'taxonomy singular name' ),
	    'search_items' =>  __( 'Search Types' ),
	    'all_items' => __( 'All Types' ),
	    'parent_item' => __( 'Parent Type' ),
	    'parent_item_colon' => __( 'Parent Type:' ),
	    'edit_item' => __( 'Edit Type' ), 
	    'update_item' => __( 'Update Type' ),
	    'add_new_item' => __( 'Add New type' ),
	    'new_item_name' => __( 'New Type Name' ),
	    'menu_name' => __( 'Type' ),
	  ); 
	  
	  $args = array(
	    'hierarchical' => true,
	    'labels' => $labels,
	    'show_ui' => true,
	    'query_var' => true,
	    'rewrite' => array( 'slug' => 'type' ),
	  );
	
	  register_taxonomy( 'project-type', array('projects'), $args);
	  
	}//function
	
	
	
	/**
	 * add some inline css for post type 'projects' admin pages
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * used to change the icon used on the pages - needs a hook in core
	 *
	 */
	function _post_type_css_head() {global $post_type;

		if ( ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'projects' ) || ( isset( $post_type ) && $post_type == 'projects' ) ){
			
			echo '<style type="text/css">'."\n";
			echo '#icon-edit {background:transparent url("'. WPPM_IMG .'32/icon.png") no-repeat;}'."\n";
			echo '.column-details {width:15% !important;}'."\n";
			echo '.column-users {width:11% !important;}'."\n";
			echo '.column-start_date, .column-end_date {width:8% !important;}'."\n";
			echo '.column-completed {width:8%;text-align:center !important;}'."\n";
			echo '.column-paid {width:8%;text-align:center !important;}'."\n";
			echo '.paid {text-align:center;}'."\n";
			echo '</style>'."\n";
			
		}//if
		
	}//function
	
	
	
	

	/**
	 * change the default notification messages for custom post type 'projects'
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * needs a hook in core
	 *
	 */
	function _set_post_type_messages($messages){
		global $post;
		$messages['projects'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __('Project updated. <a href="%s">View Project</a>',WPPM_LANG), esc_url( get_permalink($post->ID) ) ),
			2 => __('Custom field updated.',WPPM_LANG),
			3 => __('Custom field deleted.',WPPM_LANG),
			4 => __('Project updated.',WPPM_LANG),
			/* translators: %s: date and time of the revision */
			5 => isset($_GET['revision']) ? sprintf( __('Project restored to revision from %s',WPPM_LANG), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __('Project published. <a href="%s">View Project</a>',WPPM_LANG), esc_url( get_permalink($post->ID) ) ),
			7 => __('Project saved.',WPPM_LANG),
			8 => sprintf( __('Project submitted. <a target="_blank" href="%s">Preview Project</a>',WPPM_LANG), esc_url( add_query_arg( 'preview', 'true', get_permalink($post->ID) ) ) ),
			9 => sprintf( __('Project scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Project</a>',WPPM_LANG),
			  // translators: Publish box date format, see http://php.net/date
			  date_i18n( __( 'M j, Y @ G:i' ,WPPM_LANG), strtotime( $post->post_date ) ), esc_url( get_permalink($post->ID) ) ),
			10 => sprintf( __('Project draft updated. <a target="_blank" href="%s">Preview Project</a>',WPPM_LANG), esc_url( add_query_arg( 'preview', 'true', get_permalink($post->ID) ) ) ),
		);

		return $messages;
	}//function
	
	
	
	
	
	/**
	 * add some custom headers to the list of custom posts 'projects'
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * this onlys registers the thead/tfoot labels - another func hooks the fields 
	 *
	 */
	function _projects_edit_columns($columns){
		
		$columns['date'] = __('Created',WPPM_LANG);
		
		$newcolumns = array(
			'details' => __('Job Details',WPPM_LANG),
			'users' => __('Users',WPPM_LANG),
			'start_date' => __('Start Date',WPPM_LANG),
			'end_date' => __('End Date',WPPM_LANG),
			'completed' => __('Completed',WPPM_LANG),
			'paid' => __('Paid',WPPM_LANG)
			);
		
		
		$this->array_insert($columns, $newcolumns, 2);
				
		return $columns;
	}//function
	
	
	/**
	 * private function for use above
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * this reformats an array and allows easy insertion of array values into specific sections of the array
	 *
	 */
	private function array_insert(&$array, $insert, $position) {
		settype($array, "array");
		settype($insert, "array");
		settype($position, "int");
		if($position==0) {
		    $array = array_merge($insert, $array);
		} else {
		    if($position >= (count($array)-1)) {
		        $array = array_merge($array, $insert);
		    } else {
		        $head = array_slice($array, 0, $position);
		        $tail = array_slice($array, $position);
		        $array = array_merge($head, $insert, $tail);
		    }//if
		}//if
	}//function


	/**
	 * display project meta in the posts view screen
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * this is displayed in the table area and hooks into the fields created above with _projects_edit_columns();
	 *
	 */
	function _post_type_column_data($column){
		
		global $post;
		
		$meta = get_post_meta($post->ID, '_wppm_project_meta', true);//get the project meta into array
		
		switch ($column){
			
			case 'details':
				
				//display some the meta fileds
				echo __('<strong>Job No:</strong> ', WPPM_LANG);
				
				echo (isset($meta['_wppm_project_meta_job_number']))?$meta['_wppm_project_meta_job_number']:__('N/A', WPPM_LANG);
				
				echo __('<br/><strong>Quote No:</strong> ', WPPM_LANG);
				
				echo (isset($meta['_wppm_project_meta_quote_number']))?$meta['_wppm_project_meta_quote_number']:__('N/A', WPPM_LANG);
				
				echo __('<br/><strong>Invoice No:</strong> ', WPPM_LANG);
				
				echo (isset($meta['_wppm_project_meta_invoice_number']))?$meta['_wppm_project_meta_invoice_number']:__('N/A', WPPM_LANG);
				
				
				//get the project terms and display them as a comma seperated list
				$taxonomy = 'type';
				
				$types = get_the_terms($post->ID,$taxonomy);
				
				if (is_array($types)) {
					
				    foreach($types as $key => $type) {
				    	
				        $edit_link = get_term_link($type,$taxonomy);
				        
				        $types[$key] = '<a href="'.$edit_link.'">' . $type->name . '</a>';
				    }//foreach
				    
				    echo __('<br/><strong>Project Type:</strong> ', WPPM_LANG).implode(', ',$types);
				
				}//if
				
			break;
			case 'users':
			
				if(isset($meta['_wppm_project_meta_users'])){//if project has users
					
					foreach($meta['_wppm_project_meta_users'] as $user){//seperate each user and get users data
						
						$data = get_userdata($user);
						
						echo '<a href="'.admin_url('user-edit.php?user_id='.$data->ID).'">'.$data->display_name.'</a><br/>';//echo profile edit link for each user
						
					}//foreach
						
				}//if
				
			break;
			case 'start_date':
			
				echo (isset($meta['_wppm_project_meta_start_date']))?$meta['_wppm_project_meta_start_date']:__('N/A', WPPM_LANG);
				
			break;
			case 'end_date':
			
				echo (isset($meta['_wppm_project_meta_end_date']))?$meta['_wppm_project_meta_end_date']:__('N/A', WPPM_LANG);
				
			break;
			case 'completed':
			
				echo (isset($meta['_wppm_project_meta_completed']))?$meta['_wppm_project_meta_completed'].'%':__('N/A', WPPM_LANG);
				
			break;
			case 'paid':
			
				if(isset($meta['_wppm_project_meta_paid'])){//if paid meta exists
					
					echo $meta['_wppm_project_meta_paid'];
					
					if(isset($meta['_wppm_project_meta_paid_amount'])){//if paid meta has an amount
						
						echo ': '.$meta['_wppm_project_meta_paid_amount'].'%';
						
					}
					
				}else{
					
					echo __('N/A', WPPM_LANG);
					
				}//else
				
			break;
			
		}//switch
		
	}//function
	
	/**
	 * setup which custom columns can be used as sortable
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * this adds the required html/css/javascript needed for sortables
	 *
	 */
	function _post_type_sortable_columns($columns){
		
		$columns['completed'] = 'completed';
		$columns['end_date'] = 'end_date';
		$columns['paid'] = 'paid';
		
		return $columns;
			
	}//function
	
	
	
	/**
	 * setup the way wp sorts custom data
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * uses custom created meta fields to allow wordpress to sort data and tell it how to sort.
	 * core needs an update to allow order by function to accept part of a meta value array
	 * had to add additional post meta (more bulk) to get this function properly, and its all duplicate data!!!!!
	 *
	 */
	function _post_type_sort_function($vars){
		
		if ( isset( $vars['orderby'] ) && 'completed' == $vars['orderby'] ) {
			
			$vars = array_merge( $vars, array(
											'meta_key' => '_wppm_custom_sort',
											'orderby' => 'meta_value_num'
											)
								);
		}//if
		
		if ( isset( $vars['orderby'] ) && 'end_date' == $vars['orderby'] ) {
			
			$vars = array_merge( $vars, array(
											'meta_key' => '_wppm_custom_sort_end_date',
											'orderby' => 'meta_value_number'
											)
								);
		}//if
		
		if ( isset( $vars['orderby'] ) && 'paid' == $vars['orderby'] ) {
			
			$vars = array_merge( $vars, array(
											'meta_key' => '_wppm_custom_sort_paid',
											'orderby' => 'meta_value_number'
											)
								);
		}//if
		 
		return $vars;
	}
	
	
	
	
	/**
	 * add a select drop down to all user to filter posts via the taxomony 'project-types'
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * wordpress needs to add a arg value to register taxomony to do this by default
	 *
	 */
	function _filter_by_taxonomy() {
	
	    // only display these taxonomy filters on desired custom post_type listings
	    global $typenow;
	    
	    if ($typenow == 'projects') {
	
	        // create an array of taxonomy slugs you want to filter by - if you want to retrieve all taxonomies, could use get_taxonomies() to build the list
	        $filters = array('project-type');
	
	        foreach ($filters as $tax_slug) {
	        	
	            // retrieve the taxonomy object
	            $tax_obj = get_taxonomy($tax_slug);
	            
	            $tax_name = $tax_obj->labels->name;
	            
	            // retrieve array of term objects per taxonomy
	            $terms = get_terms($tax_slug);
	
	            // output html for taxonomy dropdown filter
	            echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
	            
	            echo "<option value=''>Show All $tax_name</option>";
	            
	            foreach ($terms as $term) {
	            	
	                // output each select option line, check against the last $_GET to show the current option selected
	                echo '<option value='. $term->slug, $_GET[$tax_slug] == $term->slug ? ' selected="selected"' : '','>' . $term->name .' (' . $term->count .')</option>';
	                
	            }//foreach
	            
	            echo "</select>";
	        
	        }//foreach
	    
	    }//if
	    
	}//function



	
	
	
	
	
	/**
	 * smart page template loading
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * if theme has a template for the projects post type (using wp naming structure 'single-projects.php') load that, 
	 * if not load template from plugin.
	 *
	 */	
	function _post_type_template_smart(){
		
		global $post;
		
		$template_name = 'single-projects.php';
		
		if ( is_single() && 'projects' == get_post_type() ){
			
		    $template = locate_template(array($template_name), true);
		    
		    if(empty($template)) {
		      
		      include(WPPM_DIR . 'template/' . $template_name);
		      
		      exit();
		    }//if empty template
		    
		}//if
		
	}//function
	

	/**
	 * Register the css for projects page
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * uses the same logic as above for loading the template - if theme has it then dont load
	 *
	 */
	function _post_type_css(){
		
		if ( is_single() && 'projects' == get_post_type() && !is_admin()){
			
		    $template = locate_template(array('single-projects.php'), true);
		    
		    if(empty($template)) {

				wp_enqueue_style('wppm-projects', WPPM_URL . 'template/single-projects.css');
				
		    }//if no template
		    
		}//if
		
	}//function
	
	
	/**
	 * parse comment text and search for [img] [file] tags
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * filters the comment text and replaces the tags with html formatting
	 *
	 */
	function _insert_comment_formatting($comment){
		
		$comment = preg_replace(
								"#([[]img[]])(.*)([[]/img[]])#e",
								"('<a href=\"$2\" rel=\"lightbox\" class=\"lightbox\"><img style=\"width:100px;height:auto;\" src=\"$2\" /></a>')",
								$comment
								);
		
		$comment = preg_replace(
								"#([[]file[]])(.*)([[]/file[]])#e",
								"('<a href=\"$2\" class=\"project file\">$2</a>')",
								$comment
								);
		
		return $comment;
		
	}//function
	
	
	/**
	 * add the iframe markup and additional infor below the comment form
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * adds a wp nonce and the post id to the admin ajax action 'wpp_ajax_comment_upload'
	 *
	 */
	function _add_iframe_upload_to_comments($post_id){
		
		if ( 'projects' == get_post_type() ){
			
			$options = get_option(WPPM_OPTION);
			
    		echo '<div id="project-uploads-wrapper">';
    		
    			echo '<h4 id="project-uploads-title">'.WPPM_Ultimate_Usage::option('project_comment_uploads_title',__('File / Image Uploads',WPPM_LANG)).'</h4>';
    			
    			echo '<iframe id="project_upload_frame" scrolling="no" frameborder="0" allowTransparency="true" src="'.admin_url('admin-ajax.php').'?action=wppm_ajax_comment_upload&post_id='.$post_id.'&_wp_nonce='.wp_create_nonce('wpp_comment_uploads').'" name="upload_form"></iframe>';
    			echo WPPM_Ultimate_Usage::option('project_comment_uploads_desc','<p id="project-uploads-desc">'.__('Select and upload files here and they will be added to your comment.',WPPM_LANG)).'</p>';
    			
    		echo '</div>';
    		
		}//if
		
    }//function
    
    
	/**
	 * Add custom css from options page if users adds
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * simply adds an inline styling block if the user has some custom css they want added to the page.
	 *
	 */
    function _custom_css(){
    	
    	if ( 'projects' == get_post_type() && WPPM_option('project_css') != ''){
    		
    		echo '<style type="text/css">'."\n";
    		
    			echo WPPM_option('project_css')."\n";
    			
    		echo '</style>'."\n";
    	}//if
    	
    }//function
    
    
    
    
	/**
	 * gets the site url and removes the prepended part of the domain so we can use the domain for email address
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * simply removes the http:// | https:// | www. from the domian (if its there)
	 *
	 */
	function get_part_url(){

		//$email = preg_replace("`(http://|https://)?(www.)?(.*?)`is","$3", site_url());
		// Get the site domain and get rid of www.
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );
		if ( substr( $sitename, 0, 4 ) == 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}
		return $sitename;
		
		//return $email;
	}
    

	/**
	 * inserts a new attachment to the media library and adds it to the post
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * used from the comment upload form
	 *
	 */
	function insert_attachment($file_handler,$post_id,$setthumb='false') {
 
		// check to make sure its a successful upload
		if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();
		
		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		require_once(ABSPATH . "wp-admin" . '/includes/media.php');
		
		$attach_id = media_handle_upload( $file_handler, $post_id );
		
		if ($setthumb) update_post_meta($post_id,'_thumbnail_id',$attach_id);
		
		return $attach_id;
		
	}//function
	
	

	/**
	 * display the upload form when requested from the ajax page
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * uploads the files using insert_attachment(); 
	 * and uses javascript to send the url in [tags] back to the comment text field in the parent window
	 *
	 */
	function ajax_comment_upload(){
		
		if (!wp_verify_nonce($_GET['_wp_nonce'], 'wpp_comment_uploads') ) die('Invalid Nonce Check!');?>
		
			<html>
				<head>
					<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
				</head>
				<body>
					<form method="post" enctype="multipart/form-data" name="test" id="test">
						<input type="file" name="file" id="file"><input type="submit" value="upload" class="upload" >
					</form>
				</body>
			</html>
			
		<?php
		if($_FILES){
			
			foreach ($_FILES as $file => $array) {
				$newupload = $this->insert_attachment($file,$_GET['post_id']);
			}
			
				$imgsrc = wp_get_attachment_url($newupload);
				
				$ext = pathinfo($imgsrc);
				
				if($ext['extension'] == 'png' || $ext['extension'] == 'jpeg' || $ext['extension'] == 'jpg' || $ext['extension'] == 'gif'){
					$marker = 'img';
				}else{
					$marker = 'file';
				}
				
				echo '<script language="javascript">';
					echo '$(function() {';
						echo '$(\'#comment\', parent.document).val(';
							echo '$(\'#comment\', parent.document).val()+\'['.$marker.']\'+\''.$imgsrc.'\'+\'[/'.$marker.']\n\'';
						echo ');';
					echo '});';
				echo '</script>';
		}//if $_FILES
		
		die();		
	}
	
	
		
	function _activation() {
    	$this->_register_post_type();
    	$this->_register_project_type();
    	flush_rewrite_rules();
	}
	
	function _deactivation() {
    	flush_rewrite_rules();
	}


}//class
?>