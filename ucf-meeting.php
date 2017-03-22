<?php
/*
Plugin Name: UCF Meetings Custom Post Type
Description: Provides a custom post type for posting meetings.
Version: 1.0.0
Authors: Jim Barnes
License: GPL3
*/
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'UCF_MEETINGS__PLUGIN_URL', plugins_url( basename( dirname( __FILE__ ) ) ) );
define( 'UCF_MEETINGS__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'UCF_MEETINGS__STATIC_URL', UCF_DEGREE__PLUGIN_URL . '/static' );
define( 'UCF_MEETINGS__PLUGIN_FILE', __FILE__ );

include_once 'includes/ucf-meeting-posttype.php';

if ( ! function_exists( 'ucf_meetings_plugin_activation' ) ) {
	function ucf_meetings_plugin_activation() {
		flush_rewrite_rules();
		return;
	}

	register_activation_hook( UCF_MEETINGS__PLUGIN_FILE, 'ucf_meetings_plugin_activation' );
}

if ( ! function_exists( 'ucf_meetings_plugin_deactivation' ) ) {
	function ucf_meetings_plugin_deactivation() {
		flush_rewrite_rules();
		return;
	}

	register_deactivation_hook( UCF_MEETINGS__PLUGIN_FILE, 'ucf_meetings_plugin_deactivation' );
}

if ( ! function_exists( 'ucf_meetings_plugins_loaded' ) ) {
	function ucf_meetings_plugins_loaded() {
		add_action( 'init', array( 'UCF_Meeting_PostType', 'register' ), 10, 0 );
	}

	add_action( 'plugins_loaded', 'ucf_meetings_plugins_loaded', 10, 0 );
}

?>
