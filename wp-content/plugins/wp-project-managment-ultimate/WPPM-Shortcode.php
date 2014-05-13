<?php
function wppm_shortcode( $atts ){
		        if(is_user_logged_in() && post_type_exists('projects')){
            	$projects = get_posts(array('post_type' => 'projects'));
            	global $current_user;$current_user = wp_get_current_user();
            	if($projects){
            		
            		
            		
            		
	            	$active = array();
	            	$completed = array();
						foreach( $projects as $project ){
							$meta = get_post_meta($project->ID, '_wppm_project_meta', true);
							if(isset($meta['_wppm_project_meta_users']) && in_array($current_user->ID, $meta['_wppm_project_meta_users'])){
								
								if($meta['_wppm_project_meta_completed'] == 100){
									$completed[] = '<li><a href="'.get_permalink($project->ID).'">'.$project->post_title.'</a></li>';
								}else{
									$active[] = '<li><a href="'.get_permalink($project->ID).'">'.$project->post_title.' - '.$meta['_wppm_project_meta_completed'].'%</a></li>';
								}
								
								
								
							}//in array
						}//foreach
						
					if($active){
						echo '<p>'.__('Active Projects', WPPM_LANG).'</p>';
						echo '<ul class="wppm-projects-widget-list-active">';
						echo implode('',$active);
		            	echo '</ul>';
					}
					if($completed){
						echo '<p>.'.__('Completed Projects', WPPM_LANG).'</p>';
						echo '<ul class="wppm-projects-widget-list-completed">';
						echo implode('',$completed);
		            	echo '</ul>';
					}
	            	
	            	
	            	
	            	
	            	
            	}//if projects
            	else{
            	echo '<p>'.__('You have no active projects on ', WPPM_LANG).get_bloginfo('name').'</p>';
            	}
            }//if post type exists
            else{
            	echo '<p>'.__('You must login to view your projects on ', WPPM_LANG).get_bloginfo('name').'</p>';
            	wp_login_form();
            }
            
            
}//function
add_shortcode( 'wp-project-managment', 'wppm_shortcode' );
           
?>