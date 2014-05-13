<?php
get_header();

	if ( have_posts() ) :
	
		while ( have_posts() ) : the_post();
		$meta = get_post_meta(get_the_ID(), '_wppm_project_meta', true);
		global $WPPM;
		global $current_user;$current_user = wp_get_current_user();
		if(in_array($current_user->ID,$meta['_wppm_project_meta_users'])){
			
			echo '<div id="project-wrapper-'.get_the_ID().'" class="project-wrapper">';
			
			echo '<h2 id="project-title-header">'.$WPPM[4]->option('project_table_title', __('Project: ', WPPM_LANG)).get_the_title().'</h2>';
			
			echo '<table id="project-details">';
				
				echo '<tr>';
					echo '<td class="term">';
					echo $WPPM[4]->option('project_table_users', __('Users:', WPPM_LANG));
					echo '</td>';
					echo '<td><ul>';
					foreach($meta['_wppm_project_meta_users'] as $user){$data = get_userdata($user);echo '<li><a href="mailto:'.$data->user_email.'">'.$data->display_name.'</a></li>';}
					echo '</ul></td>';
				echo '</tr>';
				
				echo '<tr>';
					echo '<td class="term">';
					echo $WPPM[4]->option('project_table_start_date', __('Start Date:', WPPM_LANG));
					echo '</td>';
					echo '<td>'.$meta['_wppm_project_meta_start_date'].'</td>';
				echo '</tr>';
				
				echo '<tr>';
					echo '<td class="term">';
					echo $WPPM[4]->option('project_table_end_date', __('End Date:', WPPM_LANG));
					echo '</td>';
					echo '<td>'.$meta['_wppm_project_meta_end_date'].'</td>';
				echo '</tr>';
				
				echo '<tr>';
					echo '<td class="term">';
					echo $WPPM[4]->option('project_table_completed', __('Completed:', WPPM_LANG));
					echo '</td>';
					echo '<td>'.$meta['_wppm_project_meta_completed'].'%</td>';
				echo '</tr>';
				
				echo '<tr>';
					echo '<td class="term">';
					echo $WPPM[4]->option('project_table_paid', __('Paid:', WPPM_LANG));
					echo '</td>';
					if($meta['_wppm_project_meta_paid'] == 'Yes' || $meta['_wppm_project_meta_paid'] == 'No' ){
						echo '<td>'.$meta['_wppm_project_meta_paid'].'</td>';
					}elseif($meta['_wppm_project_meta_paid'] == 'Part' && isset($meta['_wppm_project_meta_paid_amount']) ){
						echo '<td>'.$meta['_wppm_project_meta_paid_amount'].'%</td>';	
					}else{
						echo '<td>'.__('N/A', WPPM_LANG).'</td>';	
					}
				echo '</tr>';
			
			echo '</table>';
			
			
			echo '<div id="project-description">';
				echo '<h3 id="project-details-header">'.$WPPM[4]->option('project_description_title', __('Project Details:', WPPM_LANG)).'</h3>';
				the_content();
			echo '</div><!--project-description-->';
			
			
			$attachments = get_children( 
											array(
											'post_parent' => get_the_ID(),
											'post_type' => 'attachment',
											'orderby' => 'menu_order ASC, ID',
											'order' => 'DESC'
												)
											);
											
			if($attachments){								
			
				echo '<div id="project-attachments-wrapper">';
				echo '<h4 id="project-attachments-header">'.$WPPM[4]->option('project_attachments_title', __('Project Attachments:', WPPM_LANG)).'</h4>';
												
					echo '<ul id="project-attachments">';
						foreach($attachments as $attachment){
							echo '<li><a href="'.$attachment->guid.'" target="_blank">'.$attachment->post_title.'</a></li>';
						}//foreach
					echo '</ul>';
				echo '</div><!--project-attachments-wrapper-->';
			
			}//attachments
			
			echo '</div><!--project-wrapper-->';
		
			comments_template('', true);
		
		}//if user is allowed
		else{
			echo '<div id="project-wrapper-'.get_the_ID().'" class="project-wrapper">';
				echo '<p class="project-not-allowed">'.__('Sorry you cannot view this item', WPPM_LANG).'</p>';
				echo '<a href="'.wp_login_url( get_permalink() ).'" title="Login">'.__('Please Login', WPPM_LANG).'</a>';
			echo '</div><!--project-wrapper-->';
		}
		
		endwhile; endif;

get_footer();
?>