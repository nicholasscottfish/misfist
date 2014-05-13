<?php
class WPPM_Ultimate_Notifications extends WPPM_Ultimate{
	
	/**
	 * constructer
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * add the actions for the class functions
	 *
	 */
	function __construct(){
		
		add_filter('wp_mail', array(&$this, 'decode'), 10,1);//filter the content of email before sending
		
		add_filter('WPPM_before_meta_save', array(&$this,'_publish_hook'));//hook into the save post and use the custom hook setup with the metabox class
		
		add_action('comment_post', array(&$this,'_comment_hook'));//and on commenst
		
	}//function
	
	
	/**
	 * stops wp_mail from filtering out html entities and breaking urls
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 *
	 */
    function decode($array){
    	
		$array['subject'] = html_entity_decode($array['subject']);
		$array['message'] = html_entity_decode($array['message']);
		
		return $array;
	}
	
	
	/**
	 * send notification when project updated or published
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * checks for users - checks if project is completed then send the mail (completed | updated)
	 *
	 */
    function _publish_hook($meta){
    	
    	global $post;
    	
		if(isset($meta['_wppm_project_meta_users'])){

        	if(!isset($meta['_wppm_project_meta_completed_mailed']) && $meta['_wppm_project_meta_completed'] == 100){

        		foreach($meta['_wppm_project_meta_users'] as $user){
        			
        			$data = get_userdata($user);
					$this->_notice_mail($data->user_email, $data->display_name, $post->post_title, $post->ID, $post->post_modified, 'Completed');
					
        		}//foreach
        		
        		$meta['_wppm_project_meta_completed_mailed'] = 'Yes';//set the completed flag
        		
        	}//if hasnt been mailed for completed
        	
        	elseif($meta['_wppm_project_meta_completed'] != 100){
        		
        		foreach($meta['_wppm_project_meta_users'] as $user){
        			
        			$data = get_userdata($user);
					$this->_notice_mail($data->user_email, $data->display_name, $post->post_title, $post->ID, $post->post_modified, 'Updated');
					
        		}//foreach
        		
        	}//it isnt completed
        	
        	
        }//if no meta values or users assigned to meta


	return $meta;

    }//function
    
    
	/**
	 * send notification when project is commented on
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * changes the notification email to say comment
	 * v1.1 may include the comment text
	 *
	 */
    function _comment_hook($comment_id){
    	
	    global $post;
	    
	    $meta = get_post_meta($post->ID, '_wppm_project_meta', true);//get meta
	    
	    if($meta){
	    	
		    foreach($meta['_wppm_project_meta_users'] as $user){
		    	
		     	$data = get_userdata($user);
				$this->_notice_mail($data->user_email, $data->display_name, $post->post_title, $post->ID, date(DATE_RFC822), 'Updated (comment)');
				
		    }//foreach
	    
	    }//if
	    
	}//function
    
    
    
	/**
	 * this is the core of mail system, accepts vars for the subject heading (comment | updated | completed)
	 *
	 * @package WPPM
	 * @since 3.3
	 *
	 * need to make it more streamlined and also maybe add var fro adding comment text.
	 *
	 */
    function _notice_mail($to, $name, $post_title, $post_id, $post_modified, $status = 'Updated'){
    	
    	$headers = '';
    	
    	if(WPPM_Ultimate_Usage::option('project_notify_name') && WPPM_Ultimate_Usage::option('project_notify_email')){
    		
    		$headers = 'From: "'.WPPM_Ultimate_Usage::option('project_notify_name').'" <'.WPPM_Ultimate_Usage::option('project_notify_email').'@'.$this->get_part_url().'>';
    		
    	}//if
    	
    	$message = __("Hello ",WPPM_LANG).$name. ",\n\n";
		$message .= __("Your Project ",WPPM_LANG) . $post_title . __(' has just been '.$status.'!',WPPM_LANG) . "\n\n";
		$message .= __('To view the updated project click here: ',WPPM_LANG).get_permalink($post_id).' ' . "\n\n\n\n";
		$message .= __("Project modified: ",WPPM_LANG). $post_modified . "\n\n\n\n";
		
		$message = wordwrap($message, 70);
		
		$return = wp_mail($to, __('Project: ',WPPM_LANG).$post_title.__(' '.$status.' - ',WPPM_LANG).$post_modified, $message, $headers);
		
		return $return;
		
    }//function
    
	
	
}//class
?>