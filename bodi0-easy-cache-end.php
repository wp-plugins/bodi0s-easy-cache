<?php
defined( 'ABSPATH' ) or exit();
/*
Plugin`s caching buffer flush (write cache file)
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
//Settings
require('bodi0-easy-cache-settings.php');
require('lib/func.php');
//
		$webpage = ob_get_contents();

	// Write file as cache if it is not excluded only
    if ($ignore_page === false) {
		
		//Check to see if cache file should be minified
			if (get_option('easy_cache_option_minify_cache_file') == 'Yes') { 
				$webpage = easy_cache_html_compress($webpage);
			}
			$minified_css_files = array();
			$new_minified_css_files = array();
			
			//Decode the values from hex to string...and convert it to array()
			$minified_css_files =  explode("~~~",get_option("easy_cache_option_minified_css_files")); 
			//
			foreach($minified_css_files as $value) {
				$new_minified_css_files[] =  trim(easy_cache_hexstr($value));
	
			}
			//Filter empty values
			$new_minified_css_files = array_filter($new_minified_css_files);
			
			//Check to see if CSS files needs to be minified and combined
			if (!empty($new_minified_css_files)) {		 
				//The regex patterns array
				$pattern = array();
			
				//Remove the stylesheets links first...
				foreach($new_minified_css_files as $nmcf) {
					//Escape the URL and create array of link patterns to match against the <link>
					$url = preg_quote($nmcf);
					$pattern[] = '~<link([^>]*?)href[\s*]?=[\s*]?[\'\"\\\]*'.$url.'([^>]*?)>~isu';
				}
				//Remove the <link> tag
					$webpage = preg_replace($pattern, "", $webpage);	
					
				//Insert the new link to combined and minified CSS file
				$combined_css_link = '<link href="'.get_stylesheet_directory_uri(). "/" . $cssfile.'" type="text/css" rel="stylesheet"/>';
				//
				$webpage_modified = str_replace("</head>",$combined_css_link."</head>", $webpage);
				
		}
		//No minification and combination of CSS
		else {
			$webpage_modified = $webpage;
		}	

	  // Now the script has run, generate a new cache file with EXCLUSIVE LOCK
    $addendum = '<!-- Easy cached on: '
			.gmdate('D, Y-m-d H:i:s'). ' GMT'.', file: '
			.basename($cachefile).', size: ';
		$addendum	.= (strlen($webpage_modified) ) + 
		strlen( $addendum ) + 
		(31) + 
		(strlen(strlen($webpage_modified))) +
		 strlen($requested_page).' bytes, URL: ' .$requested_page.' --></body></html>';
			
		//Write the cache file
		file_put_contents($cachefile, $webpage_modified. $addendum, LOCK_EX); 
        	
	}
	// Flush buffer
    ob_end_flush(); 
?>