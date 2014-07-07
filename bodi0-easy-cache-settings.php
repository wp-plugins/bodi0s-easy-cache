<?php
defined( 'ABSPATH' ) or exit();
/*
Plugin`s caching variables settings
Author: Budiony Damyanov
Email: budiony@gmail.com
Version: 0.8
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
/*
Globals:
$cachefile: Our cache file name;
$webpage: Our web page content;
$excluded: Our array containing excluded pages (as IDs);
$requested_page: First part of cache file name;
$ignore_page: Marker, flag if the page should be excluded or not from caching;
*/
		
		
	global $cachefile, $webpage, $excluded, $page, $ignore_page, $s;
	//Marker
	$ignore_page = false; 
	//DISABLE CACHING FOR LOGGED-IN USERS!
	if (is_user_logged_in()) $ignore_page = true;
	
	//DISABLE CACHING FOR PASSWORD-PROTECTED POSTS/PAGES
	if (post_password_required()) $ignore_page = true;
	
	//Get the web server protocol (non-empty value if the script was queried through the HTTPS protocol)
	$server_protocol = (!empty($_SERVER['HTTPS'])) ? "https://" : "http://";
	// Directory to cache files in, unique name based on sha1 hash of home URL (keep outside web root)
	$cachedir = get_option('easy_cache_option_cache_folder'); 
  
	  
	// The Requested page ID
	$page_id = url_to_postid($server_protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);  
	// Parse the request into array
	$parsed_url = parse_url($server_protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	// Get paged variable (we expect to have paged and/or numpages for multipage posts/pages)
	if (isset($parsed_url['query'])) parse_str($parsed_url['query']);
	//Build the URL 
	if ($page_id>0 && !isset($paged) && !isset($numpages) && !isset($_REQUEST['page']) && 
	 (is_page() || is_single() || is_archive())) $requested_page = get_permalink($page_id);
	else $requested_page = $server_protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		
	//Cache file expire setting depending of is the request website search or not
	if ( isset($s) || is_search()) {
		$cachetime = 60 * (int)get_option('easy_cache_option_search_cache_timeout');  //5min by default
	}
	else {
		$cachetime = 60 * (int)get_option('easy_cache_option_cache_time');    //5min by default
	}
			
	//Cache file name is constructed as SHA1 hash of requested URL
	$cachefilename = sha1($requested_page); 
	// Cache file to either load or create
	$cachefile =  $cachedir.$cachefilename.'.cache';
	//File name for combined and minified CSS files
	$cssfile = '_css.min.css'; 

	// Excluded pages list
	$excluded = get_option('easy_cache_option_exclude_pages');
	//
	$excluded = (!is_array($excluded)) ? maybe_unserialize($excluded) : array();
	
	/*//Annonymous function callback to get link to the requested page (transform it as permalink from page ID), PHP 5.3 or higher
	$callback = function($value) {
		return get_permalink($value);
	};		

	//Modify excluded pages IDs (and convert them to array if they are not already an array)
	$excluded = (array_map($callback, (array)$excluded));*/
	//Annonymous function callback to get link to the requested page (transform it as permalink from page ID)
		//$callback = function($value) {
		//	return get_permalink($value);
		//};		
		
	if (!function_exists('easy_cache_convert_to_permalinks')) {
		function easy_cache_convert_to_permalinks($value) {
			return get_permalink($value);
		};
	}				
	
	//Modify excluded pages IDs (and convert them to array if they are not already an array)
	$excluded = (array_map("easy_cache_convert_to_permalinks", (array)$excluded));
	
	//Cached count of the list for faster looping
	$number_of_excluded = count($excluded);
	//Loop trough array of excluded pages if the page is not search page
	if (!is_search()) {
		for ($i = 0; $i < $number_of_excluded; $i++) {
				$ignore_page = (strpos($requested_page, $excluded[$i]) !== false) ? true : $ignore_page;
		}
	}

	if ( is_search() && get_option("easy_cache_option_exclude_search_queries")=='Yes') $ignore_page = true;
  //echo '2';var_dump($ignore_page);
		
	//Check to see if we use mobile theme of Jetpack
	if ( !function_exists("easy_cache_is_jetpack_mobile") ) {
		function easy_cache_is_jetpack_mobile() {
			//Are Jetpack Mobile functions available?
			if (!function_exists('jetpack_is_mobile')) return false;
			// Is Mobile theme showing?
			if (isset($_COOKIE['akm_mobile']) and $_COOKIE['akm_mobile']=='false') return false;
			return jetpack_is_mobile();
		}
	}
	
	//Do not cache page if it is WP user login page
	if (strstr($_SERVER['REQUEST_URI'],'wp-login.php')) $ignore_page = true;		
	//Do not cache page if it is Duplicate comment WP error
	if (strstr($_SERVER['REQUEST_URI'],'wp-comments-post.php')) $ignore_page = true;
	//Do not cache WP user signup page
	if (strstr($_SERVER['REQUEST_URI'],'wp-signup.php')) $ignore_page = true;
	// Do not cache Jetpack mobile pages (according to the settings option)
	if (get_option('easy_cache_option_skip_jetpack_mobile_caching')=='Yes' && easy_cache_is_jetpack_mobile()) $ignore_page = true;
	
?>