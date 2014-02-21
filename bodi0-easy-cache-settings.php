<?php
defined( 'ABSPATH' ) or exit();
/*
Plugin`s caching variables settings
Author: bodi0
Email: budiony@gmail.com
Version: 0.1
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
$page: First part of cache file name;
$ignore_page: Marker, flag if the page should be excluded or not from caching;
$front_page: 
$multipage: WordPress global variable, that denote whether or not a post is paginated or not (returns zero if not);
$paged: WordPress search global variable, that denote whether or not a post is paginated or not (returns zero if not);
*/
		global $cachefile, $webpage, $excluded, $page, $ignore_page, $front_page, $multipage, $paged;
		//Marker
    $ignore_page = false; 
		//DISABLE CACHING FOR LOGGED-IN USERS!
		if (is_user_logged_in()) $ignore_page = true;
		
		// Directory to cache files in, unique name based on sha1 hash of home URL (keep outside web root)
    $cachedir = get_option('easy_cache_option_cache_folder'); 
    
		$cachetime = 60 * (float)get_option('easy_cache_option_cache_time');    //5min by default
    $page = ''; // Requested page
		if(is_home() || is_front_page() || is_search() || is_404() || is_archive() || is_feed() || $multipage> 0 || $paged> 0)   
			$page = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		else $page = get_permalink();
		
		$cachefilename = sha1($page); //Cache file name is constructed as SHA1 hash of requested URL
    $cachefile =  $cachedir.$cachefilename.'.cache';// Cache file to either load or create
		// Excluded pages list
    $excluded = get_option('easy_cache_option_exclude_pages');
		//
		$excluded = (!is_array($excluded)) ? @unserialize($excluded) : array();
		
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
					$ignore_page = (strpos($page, $excluded[$i]) !== false) ? true : $ignore_page;
			}
		}
		if (is_search() && get_option("easy_cache_option_exclude_search_queries")=='Yes') $ignore_page = true;

		//Do not cache page if it is WP user login page
		if (strstr($_SERVER['REQUEST_URI'],'wp-login.php')) $ignore_page = true;		
		//Do not cache page if it is Duplicate comment WP error
		if (strstr($_SERVER['REQUEST_URI'],'wp-comments-post.php')) $ignore_page = true;
		//Do not cache WP user signup page
		if (strstr($_SERVER['REQUEST_URI'],'wp-signup.php')) $ignore_page = true;
		?>