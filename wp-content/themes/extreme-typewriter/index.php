<?php 
get_header();
?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<div class="post" id="post-<?php the_ID(); ?>">
	<?php if (!is_page()){ ?>
	<div class="date">
		<p><?php the_time("<b>d</b></p><p>m/y"); ?></p>
	</div>
	<?php } ?>
	<div class="contentinner">
		<h2 class="storytitle">
			<a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a>
			<?php edit_post_link(__('Edit This'), "<small>", "</small>"); ?>
		</h2>
		<?php if (!is_page()){ ?>
			<div class="meta"><?php the_time() ?> <?php _e("by") ?> <?php the_author() ?>. <?php _e("Filed under:"); ?> <?php the_category(',') ?></div>
			<div class="meta tags"><?php the_tags(__('Tags: ')." "); ?></div>
		<?php } ?>
		<div class="storycontent">
			<?php the_content(__('(more...)')); ?>
		</div>
	
		<div class="feedback">
			<?php if (is_single()) wp_link_pages(); ?>
			<?php comments_popup_link(__('Comments (0)'), __('Comments (1)'), __('Comments (%)')); ?>
		</div>
	</div>
</div>

<?php comments_template(); // Get wp-comments.php template ?>

<?php 
endwhile;
endif; 
?>

<?php posts_nav_link(' &#8212; ', __('&laquo; Newer Posts'), __('Older Posts &raquo;')); ?>

<?php get_footer(); ?>
