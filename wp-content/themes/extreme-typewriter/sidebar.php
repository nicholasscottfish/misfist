<!-- begin sidebar -->
<div id="sidebar">
<ul>
	<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar()) { ?>
	<?php wp_list_bookmarks('title_after=&title_before='); ?>
	<?php wp_list_categories('title_li=' . __('Categories:')); ?>
	<li id="search">
		<label for="s"><?php _e('Search:'); ?></label>
		<form id="searchform" method="get" action="<?php bloginfo('home'); ?>">
		<div>
		<input type="text" name="s" id="s" size="15" /><br />
		<input type="submit" value="<?php _e('Search'); ?>" />
		</div>
		</form>
	</li>
	<li id="archives"><?php _e('Archives:'); ?>
		<ul>
			<?php wp_get_archives('type=monthly'); ?>
		</ul>
	</li>
	<?php } ?>
	
</ul>
<p class="credit"><cite>Powered by <a href="http://wordpress.org/">WordPress</a></cite></p>
</div>
<!-- end sidebar -->
