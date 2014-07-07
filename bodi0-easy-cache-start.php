<?php
defined( 'ABSPATH' ) or exit();
/*
Plugin`s reading cache file
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
  $cachefile_created = ((file_exists($cachefile)) and ($ignore_page === false)) ? filemtime($cachefile) : 0;
    clearstatcache();

    // Show file from cache if still valid
    if (time() - $cachetime < $cachefile_created) {

        //ob_start('ob_gzhandler');
        echo file_get_contents($cachefile);
        //ob_end_flush();
        exit();

    }
    // If we're still here, we need to generate a cache file
		ob_start();
?>