<?php
defined( 'ABSPATH' ) or exit();
/*
Plugin`s caching buffer flush (write cache file)
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
//Settings
require('bodi0-easy-cache-settings.php');
//
		//Minify function
		function easy_cache_html_compress($buffer) {
			// Remove extra tabs, spaces, newlines, etc.
			$buffer = preg_replace('~(\s)\1+~', '$1', $buffer);
			$buffer = str_replace(array("\t","\r"),"", $buffer);
			return $buffer;
	 }
		
		$webpage = ob_get_contents();

		// Write file as cache if it is not excluded only
    if ($ignore_page === false) {
		
		//Check to see if cache file should be minified
		if (get_option('easy_cache_option_minify_cache_file') == 'Yes') $webpage = easy_cache_html_compress($webpage);

    // Now the script has run, generate a new cache file
    $fp = fopen($cachefile, 'w'); 
    
    // Save the contents of output buffer to the file
	  fwrite($fp, $webpage . '<!-- cached on: '
			.gmdate('D, Y-m-d H:i:s'). ' GMT'.', file: '
			.basename($cachefile).', size: '
			.strlen($webpage).' bytes, URL: '
			.$page .'	--></body></html>');
    fclose($fp); 
		
		}
		// Flush buffer
    ob_end_flush(); 
?>