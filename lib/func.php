<?php
defined( 'ABSPATH' ) or exit();
/*
Plugin`s library of some handy functions
Author: bodi0
Email: budiony@gmail.com
Version: 0.7
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

//Calculate web server load, for statistical purposes, Windows and UNIX, @return string
if (!function_exists('easy_cache_get_server_load1')) {
function easy_cache_get_server_load1($windows = false) {
    $os=strtolower(PHP_OS);
    if(strpos($os, 'win') === false){
        if(file_exists('/proc/loadavg')) {
            $load = file_get_contents('/proc/loadavg');
            $load = explode(' ', $load, 1);
            $load = $load[0];
        }
				elseif(function_exists('shell_exec')) {
            $load = explode(' ','uptime');
            $load = $load[count($load)-1];
        }
				else {
            return false;
        }
        if(function_exists('shell_exec'))$cpu_count = shell_exec('cat /proc/cpuinfo | grep processor | wc -l');
        return array('Load'=>$load,'CPU count'=>$cpu_count);
    }
		elseif($windows){
        if(class_exists('COM')) {
            $wmi = new COM("Winmgmts://");
            //
						$server = $wmi->execquery("SELECT LoadPercentage FROM Win32_Processor");
            $load=0;
            $cpu_count=0;
             
             foreach($server as $cpu){
                 $cpu_count++;
                 $load += $cpu->loadpercentage;
             }
             
             $load = round($load/$cpu_count);
            
            return array('Load'=>$load,'CPU count'=>$cpu_count);
        }
        return false;
    }
    return false;
}
}

//Calculate web server load, for statistical purposes, UNIX only, @return string
if (!function_exists('easy_cache_get_server_load2') ) {
	function easy_cache_get_server_load2() {
		if(function_exists('sys_getloadavg')) {
		$load = sys_getloadavg();
			if (!empty($load))	return $load[0]." / ".$load[1]. " / ". $load[2];
			else return false;
		}
		else return false;
	}
}

//Calculate web server memory usage, @return float
if (!function_exists('easy_cache_get_memory_usage_stats')) {
function easy_cache_get_memory_usage_stats() {
	if (function_exists("memory_get_usage")) 	$mem = memory_get_usage (false);
	if (function_exists("memory_get_peak_usage")) $mem_peak = memory_get_peak_usage(false);
	//
	return round($mem/(1024*1024),2) ."MiB / ".round($mem_peak/(1024*1024),2) .'MiB';
	}
}
//List folders and sub-folders contents with given file type, @return array
if (!function_exists('easy_cache_list_folders')) {
	function easy_cache_list_folders ($dir = "/", $file_type="*.css") {
	//Get cache files created by plugin and calculate total size (filter [ and ], otherwise glob() will not function properly)
	$items = glob( preg_replace('/(\*|\?|\[)/', '[$1]', $dir ). $file_type);

	$count = count($items);
	//
    for ($i = 0; $i < $count; $i++) {
        if (is_dir($items[$i])) {
            $add = glob($items[$i] . $file_type);
            $items = array_merge($items, $add);
        }
    }
    return $items;
	}
}

//Function to convert hex value to string
if (!function_exists('easy_cache_hexstr')) {
	function easy_cache_hexstr($hexstr) {
	  $hexstr = str_replace(' ', '', $hexstr);
	  $hexstr = str_replace('\x', '', $hexstr);
	  $retstr = pack('H*', $hexstr);
	  return $retstr;
	}
}
//Function to convert string to hex value 
if (!function_exists('easy_cache_strhex')) {
	function easy_cache_strhex($string) {
			$hexstr = unpack('H*', $string);
		return array_shift($hexstr);
	}

}
//Function for combining and minifying resource files (like CSS), @return string
if (!function_exists("easy_cache_optimize_css_files")) {
	function easy_cache_optimize_css_files($buffer = "") {
 
	// Remove comments
	$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
	$colors = array('#000000','#111111','#222222','#333333','#444444','#555555','#666666','#777777','#888888','#999999',
	'#aaaaaa','#bbbbbb','#cccccc','#dddddd','#eeeeee','#ffffff');
	// Remove whitespace
	$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    ','  '), ' ', $buffer);
	 
	// Remove space after colons
	$buffer = str_replace(array(': ',' :',' : ',': '), ':', $buffer);
	$buffer = str_replace(array('{ ',' {',' { ','{  ','{ '), '{', $buffer);
	$buffer = str_replace(array('} ',' }',' } '), '}', $buffer);
	$buffer = str_replace(array('; ',' ;',' ; '), ';', $buffer);
	$buffer = str_replace(array(', ',' ,',' , '), ',', $buffer);
	$buffer = str_replace(array('> ',' >',' > '), '>', $buffer);
	//Optimize CSS units
	//$buffer = str_replace(array(' 0%',' 0in',' 0cm',' 0mm',' 0em',' 0ex',' 0pt',' 0pc',' 0px'), '0', $buffer);
	$buffer = str_replace(array(',0%',',0in',',0cm',',0mm',',0em',',0ex',',0pt',',0pc',',0px'), ',0', $buffer);
	$buffer = str_replace(array(':0%',':0in',':0cm',':0mm',':0em',':0ex',':0pt',':0pc',':0px'), ':0', $buffer);
	//Optimize CSS colors
	foreach ($colors as $color) {
		$buffer = str_ireplace($color, substr($color,0,4), $buffer);
	}
 	 
	// Write everything out
	return $buffer;
	}
}
//Minify HTML contents
if (!function_exists('easy_cache_html_compress')) {
	function easy_cache_html_compress($buffer) {
		// Remove extra tabs, spaces, newlines, etc.
		$buffer = preg_replace('~(\s)\1+~', '$1', $buffer);
		$buffer = str_replace(array("\t","\r","\t\r","\r\t"),"", $buffer);
		$buffer = str_replace(array(" />","/>\r","/>\r ","/>\n","/>\n ","/>\r\n","/>\r\n "),"/>", $buffer);
		$buffer = str_replace(array(" </","\r</"," \r</","\n</"," \n</","\r\n</"," \r\n</"),"</", $buffer);
		$buffer = str_replace(array(";\r",";\r\n",";\n","; "," ;"," ; "),";", $buffer);
		$buffer = str_replace(array("{ "," {"," { ","{  ","{ ", "{\r","{\n","{\r\n"), "{", $buffer);
		$buffer = str_replace(array("} "," }"," } ","}\r","}\n","}\r\n"), "}", $buffer);

		return $buffer;
	}
}




//Check for correct URL according to http://www.faqs.org/rfcs/rfc2396
if (!function_exists('easy_cache_is_url')) {
	function easy_cache_is_url($url) 
	{
		if (function_exists("filter_var") && filter_var($url, FILTER_VALIDATE_URL)) {
			return true;
		} else {
			return false;
		}
	} 
}

?>