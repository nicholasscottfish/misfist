<?php
/****************/
/*   WP query   */
$query = array(
	'posts_per_page' => get_theme_option('blog_posts'),
	'orderby' => get_theme_option('blog_orderby'),
	'order' => get_theme_option('blog_order'),
	'paged' => ( get_query_var('paged') ? get_query_var('paged') : true )
);
?>

<div id="blog_section" class="section" data-section="<?php echo sanitize_title( get_theme_option('blog_label') ); ?>">

	<div id="blog_section_botm_bg">
	
		<div class="section_inn">
			
			<?php if ( get_theme_option('top') ) : ?>
			
			<a class="go_top" href="#home_section"></a>
			
			<?php endif; ?>
			
			<?php if ( get_theme_option('blog_label') || get_theme_option('blog_intro') ) : ?>
			
			<div id="blog_section_desc">
			
				<div id="blog_section_desc_left">
				
					<h3 class="section_label"><?php echo retro_filter( get_theme_option('blog_label') ); ?></h3>
										
				</div><!-- end div #blog_section_desc_left -->
				
				<div id="blog_section_desc_right">
				
					<p><?php echo get_theme_option('blog_intro'); ?></p>
					
					<div class="clr"></div>
					
				</div><!-- end div #blog_section_desc_right -->
				
				<div class="clr"></div>
				
			</div>
			<!-- end div #blog_section_desc -->
			
			<?php endif; ?>
			
			<div id="blog_section_content">
			
				<div id="blog_section_content_left">
				
					<div id="blog_section_listing">
					
						<ul>
						
							<?php query_posts( $query ); if ( have_posts() ) :  ?>
							
							<?php while ( have_posts() ) : the_post(); ?>
														
							<?php get_template_part('loop'); ?>
								
							<?php endwhile; ?>
							
							<?php if ( get_theme_option('blog_paged') ) : ?>
							
							<div class="clr"></div>
							
							<!-- Previous posts link -->
							<div class="pager fr">
								<?php previous_posts_link( __('Newer posts &rarr;', 'haku') ); ?>
							</div>
							
							<!-- Next posts link -->
							<div class="pager fl">
								<?php next_posts_link( __('&larr; Older posts', 'haku') ); ?>
							</div>
							
							<?php endif; ?>
							
							<?php else : ?>
																						
							<?php get_template_part('not-found'); ?>
				
							<?php endif; ?>
							
						</ul>
						
						<div class="clr"></div>
						
					</div><!-- end div #blog_section_listing -->
					
				</div><!-- end div #blog_section_content_left -->
				
				<?php get_sidebar(); ?>
								
			</div><!-- end div #blog_section_content -->
			
			<div class="clr"></div>
			
		</div><!-- end div .section_inn -->
		
		<div class="clr"></div>
		
	</div><!-- end div #blog_section_botm_bg -->
	
	<div class="clr"></div>
	
</div><!-- end div #blog_section -->

<?php wp_reset_query(); ?>