<?php
defined( 'ABSPATH' ) or exit();
/*
Plugin Name: bodi0`s Easy cache
Plugin URI: http://wordpress.org/plugins/bodi0s-easy-cache/
Description: Caches the pages/posts in your blog for improved performance.
Version: 0.8
Text Domain: bodi0-easy-cache
Domain Path: /languages
Author: Budiony Damyanov
Author URI: mailto:budiony@gmail.com
Email: budiony@gmail.com
License: GPL2

		Copyright 2014  bodi0  (email : budiony@gmail.com)
		
		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License, version 2, as 
		published by the Free Software Foundation.
		
		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.
		
		You should have received a copy of the GNU General Public License
		along with this program; if not, write to the Free Software
		Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

global $wpdb, $nonce, $cachefile, $webpage, $plugin_options, $exclude_search_queries;
/*Plugin file name*/
$plugin = plugin_basename( __FILE__ );
/*Security check*/
$nonce = isset($_REQUEST['_wpnonce']) ? $_REQUEST['_wpnonce'] : '';
/*Get default uploads directory*/
$upload_dir = wp_upload_dir();
/*Set the default plugin options*/
$plugin_options = array(
'easy_cache_option_cache_time'=>'30',
'easy_cache_option_cache_folder'=>untrailingslashit($upload_dir['basedir']).DIRECTORY_SEPARATOR."cached".DIRECTORY_SEPARATOR,
'easy_cache_option_exclude_search_queries'=>'No',
'easy_cache_option_exclude_pages'=>'',
'easy_cache_option_enable_caching'=>'No',
'easy_cache_option_minify_cache_file'=>'Yes', 
'easy_cache_option_auto_rebuild_cache_file'=>'Yes',
'easy_cache_option_minified_css_files'=>'',
'easy_cache_option_skip_jetpack_mobile_caching'=>'Yes',
'easy_cache_option_search_cache_timeout'=>'15'


);

/*Actions*/
add_action('init', 'easy_cache_plugin_internationalization');
/*Admin menu page*/
add_action('admin_menu', 'easy_cache_admin_actions');
/*Attach cache function to send_headers http://codex.wordpress.org/Plugin_API/Action_Reference/send_headers */
add_action('send_headers', 'easy_cache_start');
add_action('wp_print_footer_scripts','easy_cache_end', PHP_INT_MAX); //Inserted in footer with low priority

/*Settings link*/
add_filter('plugin_action_links_'.$plugin, 'easy_cache_plugin_add_settings_link');
/*Actions when post/page is updated or deleted*/
add_action('edit_post', 'easy_cache_post_page_edited', PHP_INT_MAX);
add_action('delete_post', 'easy_cache_post_page_deleted', PHP_INT_MAX);
/*Actions when comment is inserted, deleted or status is changed*/
add_action('edit_comment', 'easy_cache_post_page_edited', PHP_INT_MAX);
add_action('comment_post', 'easy_cache_post_page_edited', PHP_INT_MAX);
add_action('delete_comment', 'easy_cache_update_delete_comment',PHP_INT_MAX);
add_action('wp_set_comment_status', 'easy_cache_update_delete_comment', PHP_INT_MAX);

/*Action executed when plugin is uninstalled*/
register_uninstall_hook(__FILE__, 'easy_cache_uninstall' );
/*Action executed when plugin is deactivated*/
register_deactivation_hook(__FILE__, 'easy_cache_deactivate');
/*Action executed when plugin is activated*/
register_activation_hook(__FILE__, 'easy_cache_install');


// Install plugin
if (!function_exists('easy_cache_install')) {
	function easy_cache_install() {
		
		global $wpdb, $plugin_options;
		if (version_compare(PHP_VERSION, '5.2.4', '<'))	{
			_easy_cache_trigger_error('PHP version below 5.2.4 is not supported. Even though version PHP 5.2.4 was released in August 2007 ('.(date('Y') - 2007).' years ago), it appears, that there are still lazy and careless hosting service providers. Please yell to those "system administrators" and upgrade to PHP 5.2.4 or newer.', E_USER_ERROR);
			die();
		}
		// Important: Check if current user can install plugins
		if ( !current_user_can( 'activate_plugins' ) )  return;
		$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
		check_admin_referer( "activate-plugin_{$plugin}" );
		//Setup plugin options
		foreach ($plugin_options as $key=>$value) {
			add_option( $key, $value ); 
		}
		if(!empty($wpdb->last_error)) wp_die($wpdb->print_error());

	}
}

//Deactivate plugin
if (!function_exists('easy_cache_deactivate')) {
	function easy_cache_deactivate() {
		global $wpdb;

		// Important: Check if current user can deactivate plugins
		if ( !current_user_can( 'activate_plugins' ) )  return;
		$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
		check_admin_referer( "deactivate-plugin_{$plugin}" );	
		if(!empty($wpdb->last_error)) wp_die($wpdb->print_error());
	} 
}

//Uninstall plugin
if (!function_exists('easy_cache_uninstall')) {
	function easy_cache_uninstall() {
		global $wpdb, $plugin_options;
		// Important: Check if current user can uninstall plugins
		if ( !current_user_can( 'delete_plugins' ) ) return;
		check_admin_referer( 'bulk-plugins' );  
		//Delete the plugin options 
		foreach ($plugin_options as $key=>$value) {
			delete_option( $key, $value ); 
		}
		if(!empty($wpdb->last_error)) wp_die($wpdb->print_error());
	}
}


//Admin panel functions
if (!function_exists('easy_cache_menu')) {
	function easy_cache_menu() {
		//Important: Check if current user is logged
		if ( !is_user_logged_in() ) die();
		include_once ("bodi0-easy-cache-admin.php");
	}
}

//Register admin menu
if (!function_exists('easy_cache_admin_actions')) {
	function easy_cache_admin_actions() {
		add_options_page(__("Easy cache","bodi0-easy-cache"), __("Easy cache","bodi0-easy-cache"), 'manage_options', 'bodi0-easy-cache', 'easy_cache_menu');
	}
}
//Translations
if (!function_exists('easy_cache_plugin_internationalization')) {
	function easy_cache_plugin_internationalization() {
	  load_plugin_textdomain( 'bodi0-easy-cache', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
}
//Settings link
if (!function_exists('easy_cache_plugin_add_settings_link')) {
	function easy_cache_plugin_add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page='.basename( __FILE__ ).'">'.__("Administration","bodi0-easy-cache").'</a>';
		array_push( $links, $settings_link );
		return $links;
	}
}

//The cache routine
//Initial
if (!function_exists('easy_cache_start')) {
	function easy_cache_start() {
		
		if (get_option('easy_cache_option_enable_caching') =='Yes')
		//Use caching mechanism only if it is enabled
		include_once (dirname( __FILE__ ). DIRECTORY_SEPARATOR. 'bodi0-easy-cache-start.php');
		else;
		}	
}
//Final
if (!function_exists('easy_cache_end')) {
	function easy_cache_end() {
		if (get_option('easy_cache_option_enable_caching') =='Yes') {
			//Use caching mechanism only if it is enabled
			include_once (dirname( __FILE__ ). DIRECTORY_SEPARATOR. 'bodi0-easy-cache-end.php');
		}
		else;
	}	
}

//Actions when post/page is deleted permanently
if (!function_exists('easy_cache_post_page_deleted')) {
	function easy_cache_post_page_deleted() {
		
		//Delete cache file if post/page is deleted permanently
		easy_cache_delete_cached_file();
		
	}
}
//Actions when post/page is updated or when comment is posted
if (!function_exists('easy_cache_post_page_edited')) {
	function easy_cache_post_page_edited() {
		
	//Delete cache file depending on option setting
	if (get_option('easy_cache_option_auto_rebuild_cache_file') =='Yes')
		easy_cache_delete_cached_file();
	}
}

//Custom function for permanent cache file deletion
if (!function_exists('easy_cache_delete_cached_file')) {
function easy_cache_delete_cached_file($post_id = '') {		
//
$result = false;
$page = '';
if (empty($post_id)) $page = sha1(get_permalink());
else $page = sha1(get_permalink($post_id));

//Cached file
$cached_file = get_option('easy_cache_option_cache_folder') . DIRECTORY_SEPARATOR . $page  .'.cache';

//Delete it if File exists
	if (file_exists($cached_file)) $result =	unlink($cached_file);
		//
	}
}

//Custom function for associated cached file deletion on comment delete
if (!function_exists('easy_cache_update_delete_comment')) {
	function easy_cache_update_delete_comment($comment){
			if ( !is_object( $comment ) ){
					$comment = get_comment( $comment );
			}
			//Get the associated page/post ID
			$post_id = $comment->comment_post_ID;
			//Pass the page/post ID to the function
			if (get_option('easy_cache_option_auto_rebuild_cache_file') =='Yes') easy_cache_delete_cached_file($post_id);
	}
}

//Custom error handling
if (!function_exists('_easy_cache_trigger_error')) {
function _easy_cache_trigger_error($message, $errno) {
	if(isset($_GET['action']) && $_GET['action'] == 'error_scrape') {
		echo '<strong style="font-family:\'Open Sans\', Arial, Helvetica, sans-serif">' . $message . '</strong>';
		exit;
	} else {
		trigger_error($message, $errno);
	}
}

}

///EOF

?>