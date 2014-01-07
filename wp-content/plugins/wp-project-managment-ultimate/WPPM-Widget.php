<?php
/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'wppm_load_widgets' );

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function wppm_load_widgets() {
	register_widget( 'WPPM_Widget' );
}

/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class WPPM_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function WPPM_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'wppm-widget', 'description' => __('Widget displays a users related projects when they are logged in.', WPPM_LANG) );

		/* Widget control settings. */
		$control_ops = array('id_base' => 'wppm-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'wppm-widget', __('WP Project Managment Widget', WPPM_LANG), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );
		
		if(!is_user_logged_in() && $instance['showwidget'] == 1){return;}
		
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		//widget code
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
           
            //widget code end

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['showwidget'] = $new_instance['showwidget'];

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Example', WPPM_LANG), 'showwidget' => 0);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', WPPM_LANG); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" class="widefat" value="<?php echo $instance['title']; ?>" />
		</p>
		
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['showwidget'], 1 ); ?> value="1" id="<?php echo $this->get_field_id( 'showwidget' ); ?>" name="<?php echo $this->get_field_name( 'showwidget' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'showwidget' ); ?>"><?php _e('Hide Widget to logged out users', WPPM_LANG); ?></label>
		</p>
	<?php
	}
}

?>