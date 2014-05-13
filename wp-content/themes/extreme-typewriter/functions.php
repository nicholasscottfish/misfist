<?php
if ( function_exists('register_sidebar') )
	register_sidebar(array(
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ));
function extremetypewriter_widget_search() {
	?>
	<li id="search">
		<h3><?php _e('Search'); ?></h3>
	   	<form id="searchform" method="get" action="<?php bloginfo('home'); ?>">
		<div>
			<input type="text" name="s" id="s" value="<?php _e('Search'); ?>" onclick="if (this.value == '<?php _e('Search'); ?>') this.value='';" size="15" />
		</div>
		</form>
	</li>
	<?php
}
register_sidebar_widget(__('Search'), 'extremetypewriter_widget_search');

function hide_widgets() { 
	unregister_sidebar_widget("Search");
}  
add_action('widgets_init','hide_widgets');

?>
