<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

	<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>

	<style type="text/css" media="screen">
		@import url( <?php bloginfo('stylesheet_url'); ?> );
	</style>
	
	<!--[if gte IE 6]>
	<style type="text/css" media="screen">
		#sidebar {width: 201px;}
	</style>
	<![endif]-->
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom 1.0" href="<?php bloginfo('atom_url'); ?>" />

	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<?php wp_get_archives('type=monthly&format=link'); ?>
	<?php //comments_popup_script(); // off by default ?>
	<?php wp_head(); ?>
</head>

<body>
<div id="wrap">
<div id="header">
<div id="logolink">
	<h1><a href="<?php bloginfo('url'); ?>/"><?php bloginfo('name'); ?></a></h1>
	<p><small><a href="<?php bloginfo('url'); ?>/"><?php bloginfo('description'); ?></a></small></p>
	<div style="clear: right;"></div>
</div>
<div id="shadow">
</div>
<ul id="navigation">
	<li class="<?php if (!is_page()) echo "current_page_item" ?>"><a href="<?php bloginfo('url'); ?>">Blog</a></li>
	<?php wp_list_pages('title_li='); ?>
	<?php wp_register(); ?>
	<li><?php wp_loginout(); ?></li>
</ul>
</div>

<?php get_sidebar(); ?>

<div id="content">
<!-- end header -->