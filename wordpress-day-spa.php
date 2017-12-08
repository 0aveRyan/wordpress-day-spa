<?php
/**
 * Plugin Name: WordPress Day SPA
 * Plugin URI:  https://github.com/0aveRyan/wordpress-day-spa
 * Description: Example plugin for implementing Vue.js 2.0+ Single Page Applications in the WordPress Admin
 * Version:     1.0.0
 * Author:      David Ryan <dryanpress@chat.wordpress.org>
 * Author URI:  https://github.com/0aveRyan/wordpress-day-spa/contributors
 * Text Domain: wordpress-day-spa
 * License:     GNU General Public License v2+
 */

//avoid direct calls to this file, because now WP core and framework has been used
if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

// load file directly so we don't rely on composer
if ( ! class_exists( 'WPAZ_Plugin_Base\\V_2_5\\Abstract_Plugin' ) ) {
	include_once 'vendor/wordpress-phoenix/abstract-plugin-base/src/abstract-plugin.php';
}
// run plugin
include_once 'app/class-plugin.php';
WordPress_Day_Spa\Plugin::run( __FILE__ );