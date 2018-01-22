<?php
/*
Plugin Name: WP micro.blog Tweaks
Description: Tweaks for micro.blog
Version: 1.1
Author: Jonathan Whiteland
Author URI: http://whiteland.net/jonathan
*/


// Hide Title from "Add new/Edit Post" screen
// ----------------------------------------------------------------------------------------------------
// See: http://wordpress.stackexchange.com/questions/110427/remove-post-title-input-from-edit-page

function updt_hide_post_title() {
	remove_post_type_support('post', 'title');
}

add_action('admin_init', 'updt_hide_post_title');

// ...and set it to a timestamp
// See: https://wordpress.org/support/topic/generating-and-setting-a-custom-post-title/

function updt_set_post_title() {
	return date("Y-m-d H:i:s");
}

add_filter('title_save_pre','updt_set_post_title');


// Use custom RSS feed that doesn't have titles
// ----------------------------------------------------------------------------------------------------
// See http://wordpress.stackexchange.com/questions/47726/remove-or-edit-dccreator-in-feeds

remove_all_actions( 'do_feed_rss2' );

function updt_rss_feed_without_titles() {
	$wp_path = explode('wp-content',__FILE__);
    	load_template( $wp_path[0] . 'wp-content/feeds/feed-rss2.php');
}

add_action('do_feed_rss2', 'updt_rss_feed_without_titles');

//
// Create a date for posts without titles
  function filter_title_save_pre( $title ) { 
        if ( $title == "" ) {
                return date( 'd/m/Y, H:i' ); 
                    } else {
                            return $title;
                                }
  } 
          
  add_filter( 'title_save_pre', 'filter_title_save_pre', 10, 1 ); 

//
// Remove titles from RSS feed  
  function remove_status_title_rss ( $title) {
    $post_format=get_post_format();
    if ( $post_format =="status") {
  $title="";

  }
  return $title;
  }
  add_filter( 'title_title_rss', 'remove_status_title_rss');

//
// Allow gifs to be uploaded without processing
  function disable_upload_sizes( $sizes, $metadata ) {

      // Get filetype data.
      $filetype = wp_check_filetype($metadata['file']);

      // Check if is gif. 
      if($filetype['type'] == 'image/gif') {
          // Unset sizes if file is gif.
          $sizes = array();
      }

      // Return sizes you want to create from image (None if image is gif.)
      return $sizes;
  }   
  add_filter('intermediate_image_sizes_advanced', 'disable_upload_sizes', 10, 2); 

// Unspam webmentions  

  function unspam_webmentions($approved, $commentdata) {
    return $commentdata['comment_type'] == 'webmention' ? 1 : $approved;
  }

  add_filter('pre_comment_approved', 'unspam_webmentions', '99', 2); 



