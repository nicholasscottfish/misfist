=== Image Feed Widget ===
Contributors: yorik
Donate link: None! Keep your money for important things!
Tags: rss, feed, image, widget
Requires at least: 2.8.4
Tested up to: 3.2.1
Stable tag: trunk

A widget to display imges from RSS feeds such as twitter, flickr or youtube

== Description ==

This plugin allows to place widgets on your sidebars, that fetch the contents of one or more RSS feeds, combine them by date if there is more than one, and display the thumbnail images that are included in the feeds items. You can give the widget any number of feeds, and limit the quantity of images to be displayed.

You can use any kind of RSS feed, but they must contain thumbnails (the plugin will check for, in that order, media:thumbnail tags, or enclosure tags, or, if none of these are found, an img tag inside the feed item description). If you don't undestand a word of this, just make sure your feed carries thumbnails, otherwise all you'll see is a "No thumbnail found" text...

You can also use this plugin from your theme templates, to display images lists anywhere else on your blog and you can easily give them a fixed size or a maximum size with CSS styling.

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory or use the wordpress plugin installer
2. Activate the plugin through the 'Plugins' menu in WordPress
3. A new "Image Feed Widget" will be available.
4. Give a list of feeds to the widget, separated by commas, such as http://www.example1.com/rss,http://www.example2.com/rss2

== Frequently Asked Questions ==

= How do I define the size of the thumbnails? =

The thumbnail images can easily sized by placing something like this in your theme's css stylesheet:

.image-feed-small a img {
  max-height: 100px;
  max-width: 100px;
  }

The above css code will apply to all widgets. You can use "height" and "width" instead of "max-height" and "max-width" if you prefer.

= What about having several images per line? =

Easy too, the content of the widget is a list, so you just need to add this to your theme's css stylesheet:

image-feed-list {
  display: inline;
  }

you must of course specify a width that allows more than one image to fit in your sidebar...

= And how do I use the plugin in my theme? =

Anywhere in your theme templates, you can display a list of images coming from rss feeds. Just place the following code where you want the images to appear:

`<?php get_image_feed_list($feedslist, $maxfeeds, $divname, $printtext, $target, $useenclosures); ?>`

Where:
* $feedlist is a comma-separated list of rss feed urls (mandatory)
* $maxfeeds is the maximum number of images to display (optional, default = 90)
* $divname is a name suffix for the list class. "myList" will become "image-feed-myList" (optional)
* $printtext must be 1 if you want the image title to be printed below the image (optional)
* $target is "samewindow" or "newwindow", depending on where you want links to open (optional, default = samewindow)
* $useenclosures is "yes" or "no" (optional, default = yes). Use this if you don't want to use the <enclosure> tag in the feed and force the script to find an image link in the feed item description. Some feeds like flickr use an original-size image in enclosure and a thumbnail-size image in the description, don't ask me why...

Example:

`<?php get_image_feed_list("http://www.example1.com/rss,http://www.example2.com/rss2", 10, "myImagesList"); ?>`

== Screenshots ==

See the plugin in action on http://www.oteatromagico.mus.br/wordpress

== Changelog ==

= 0.1 =
* First version

= 0.2 =
* Added a selection box to choose between opening links in the same window or in a new window

= 0.3 =
* Added an option to disable the use of enclosure tags

= 0.4 = 
* Added an option to display image title or not

= 0.5 =
* Fixed a bug that prevented multiple choices boxes to appear in the plugin settings
