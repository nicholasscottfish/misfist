<?php
/*
Plugin Name: Image Feed Widget
Plugin URI: http://yorik.uncreated.net
Description: A widget to display imges from RSS feeds such as twitter, flickr or youtube
Version: 0.5
Author: Yorik van Havre
Author URI: http://yorik.uncreated.net
*/

/*  Copyright 2009 Yorik van Havre  (email : yorik at uncreated dot net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function get_image_feed_list($feedslist, $maxfeeds=90, $divname='standard', $printtext=NULL, $target='samewindow', $useenclosures='yes') {

                // This is the main function of the plugin. It is used by the widget and can also be called from anywhere in your theme. See the readme file for example.

                $divname = 'image-feed-'.$divname;

		// Get RSS Feed(s)
		include_once(ABSPATH . WPINC . '/feed.php');

                // Get a SimplePie feed object from the specified feed source
                $feedsarray = split(',',$feedslist);
                $rss = fetch_feed($feedsarray);

                // Figure out how many total items there are. 
                $maxitems = $rss->get_item_quantity((int)$maxfeeds);

                // Build an array of all the items, starting with element 0 (first element).
                $rss_items = $rss->get_items(0,$maxitems);

                ?>

                <ul class="image-feed-list"><?php
		// Loop through each feed item and display each item as a hyperlink.
		  foreach ( $rss_items as $item ) : ?>
		    <li>
                      <div class="<?php echo $divname; ?>">
                          <a href="<?php echo $item->get_permalink(); ?>"
		            <?php if ($target == 'newwindow') { echo 'target="_BLANK" '; }; ?>
		            title="<?php echo $item->get_title().' - Postada em '.$item->get_date('d M Y, H:i'); ?>">
                            <?php if ($thumb = $item->get_item_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'thumbnail') ) {
                                $thumb = $thumb[0]['attribs']['']['url'];
	                        echo '<img src="'.$thumb.'"'; 
                                echo ' alt="'.$item->get_title().'"/>';
                             } else if ( $useenclosures == 'yes' && $enclosure = $item->get_enclosure() ) {
                                $enclosure = $item->get_enclosures();
	                        echo '<img src="'.$enclosure[0]->get_link().'"'; 
                                echo ' alt="'.$item->get_title().'"/>';
                            }  else {
                                preg_match_all('/<img[^>]+>/i',$item->get_content(), $images);
                                if ($images) {
                                  echo $images[0][0];
                                } else {
                                  echo "thumbnail not available";
                                }
                            } 
                            if ($printtext) {
                              if ($printtext != 'no') {
                                echo "<div class='imgtitle'>".$item->get_title()."</div>";
                              }
                            }?>
                          </a>
                      </div>
		    </li>
		  <?php endforeach; ?>
		</ul>

                <div style="clear:both;"></div>

                <?php
}

class Image_Feed_Widget extends WP_Widget {
  function Image_Feed_Widget() {
    $widget_ops = array('classname' => 'image_feed_widget', 'description' => 'A widget to display images from RSS feeds such as twitter, flickr or youtube' );
    $this->WP_Widget('image_feed_widget', 'Image Feed Widget', $widget_ops);
  }

  function widget($args, $instance) {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;

    $title = empty($instance['title']) ? '&nbsp;' : apply_filters('widget_title', $instance['title']);
    $feeds_list = empty($instance['feeds_list']) ? '&nbsp;' : $instance['feeds_list'];
    $maxnumber = empty($instance['maxnumber']) ? '&nbsp;' : $instance['maxnumber'];
    $target = empty($instance['target']) ? '&nbsp;' : $instance['target'];
    $displaytitle = empty($instance['displaytitle']) ? '&nbsp' : $instance['displaytitle'];
    $useenclosures = empty($instance['useenclosures']) ? '&nbsp;' : $instance['useenclosures'];
 
    if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };

    if ( empty( $target ) ) { $target = 'samewindow'; };

    if ( empty( $displaytitle ) ) { $displaytitle = 'no'; };

    if ( empty( $useenclosures ) ) { $useenclosures = 'yes'; };

    if ( !empty( $feeds_list ) ) {

      get_image_feed_list($feeds_list, $maxnumber, 'small', $displaytitle, $target, $useenclosures); ?>

                <div style="clear:both;"></div>

                <?php }

    echo $after_widget;
  }
 
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['feeds_list'] = strip_tags($new_instance['feeds_list']);
    $instance['maxnumber'] = strip_tags($new_instance['maxnumber']);
    $instance['target'] = strip_tags($new_instance['target']);
    $instance['displaytitle'] = strip_tags($new_instance['displaytitle']);
    $instance['useenclosures'] = strip_tags($new_instance['useenclosures']);
 
    return $instance;
  }
 
  function form($instance) {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'feeds_list' => '', 'maxnumber' => '', 'target' => '', 'displaytitle' => '', 'useenclosures' => '') );
    $title = strip_tags($instance['title']);
    $feeds_list = strip_tags($instance['feeds_list']);
    $maxnumber = strip_tags($instance['maxnumber']);
    $target = strip_tags($instance['target']);
    $displaytitle = strip_tags($instance['displaytitle']);
    $useenclosures = strip_tags($instance['useenclosures']);
?>
      <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
								    
      <p><label for="<?php echo $this->get_field_id('feeds_list__title'); ?>">RSS feeds (comma-separated): <input class="widefat" id="<?php echo $this->get_field_id('feeds_list'); ?>" name="<?php echo $this->get_field_name('feeds_list'); ?>" type="text" value="<?php echo attribute_escape($feeds_list); ?>" /></label></p>
		     
      <p><label for="<?php echo $this->get_field_id('maxnumber'); ?>">Max number of images to display: <input class="widefat" id="<?php echo $this->get_field_id('maxnumber'); ?>" name="<?php echo $this->get_field_name('maxnumber'); ?>" type="text" value="<?php echo attribute_escape($maxnumber); ?>" /></label></p>

      <p><label for="<?php echo $this->get_field_id('target'); ?>">Where to open the links: <select id="<?php echo $this->get_field_id('target'); ?>" name="<?php echo $this->get_field_name('target'); ?>">
        <?php 
  	  echo '<option ';
          if ( $instance['target'] == 'samewindow' ) { echo 'selected '; }
          echo 'value="samewindow">';
	  echo 'Same Window</option>';
  	  echo '<option ';
          if ( $instance['target'] == 'newwindow' ) { echo 'selected '; }
          echo 'value="newwindow">';
	  echo 'New Window</option>'; ?>
      </select></label></p>

      <p><label for="<?php echo $this->get_field_id('displaytitle'); ?>">Display title below images? <select id="<?php echo $this->get_field_id('displaytitle'); ?>" name="<?php echo $this->get_field_name('displaytitle'); ?>">
        <?php 
  	  echo '<option ';
          if ( $instance['displaytitle'] == 'yes' ) { echo 'selected '; }
          echo 'value="yes">';
	  echo 'Yes</option>';
  	  echo '<option ';
          if ( $instance['displaytitle'] == 'no' ) { echo 'selected '; }
          echo 'value="no">';
	  echo 'No</option>'; ?>
      </select></label></p>

      <p><label for="<?php echo $this->get_field_id('useenclosures'); ?>">Use RSS enclosures? (leave yes if unsure) <select id="<?php echo $this->get_field_id('useenclosures'); ?>" name="<?php echo $this->get_field_name('useenclosures'); ?>">
        <?php 
  	  echo '<option ';
          if ( $instance['useenclosures'] == 'yes' ) { echo 'selected '; }
          echo 'value="yes">';
	  echo 'Yes</option>';
  	  echo '<option ';
          if ( $instance['useenclosures'] == 'no' ) { echo 'selected '; }
          echo 'value="no">';
	  echo 'No</option>'; ?>
      </select></label></p>

<?php
																			}
}

// register_widget('Image_Feed_Widget');
add_action( 'widgets_init', create_function('', 'return register_widget("Image_Feed_Widget");') );

?>