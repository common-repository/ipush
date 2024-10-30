<?php

/**
 * @package InstantPush
 * @version 1.0
 */

/*
Plugin Name: Ipush
Plugin URI: https://instantpu.sh
Description: Ipush Plugin manages and sends notification to your subscribers base.
Author: InstantPush
Version: 1.0
Author URI: https://instantpu.sh/
*/

include 'metabox.php';
include 'top_menu.php';
add_option('instantpush_options', array());

// Push notification consent script.
function instantpush() {
  $settings = get_option('instantpush_options');
  if (!isset($settings['adspace_id']) || empty(trim($settings['adspace_id']))) {
    return;
  }
  wp_enqueue_script("instantpush_consent","https://t.instantpu.sh/push.js?a={$settings['adspace_id']}&wordpress=true");
}

add_action( 'wp_enqueue_scripts', 'instantpush' );

?>
