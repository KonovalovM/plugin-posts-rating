<?php
/**
Plugin Name: Posts Rating Widget
Description: Widget for posts with adding rating
Version: 1.0
Author: Mykola Konovalov
 */

if (!defined('ABSPATH')) {
	exit;
}

// Include widget class
require_once plugin_dir_path(__FILE__) . 'class-posts-rating-widget.php';

function register_posts_rating_widget() {
	register_widget('Posts_Rating_Widget');
}
add_action('widgets_init', 'register_posts_rating_widget');
