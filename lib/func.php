<?php
defined( 'ABSPATH' ) or exit();
/*
Plugin`s library of some handy functions
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

//Calculate web server load, for statistical purposes, Windows and UNIX
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

//Calculate web server load, for statistical purposes, UNIX only
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

//Calculate web server memory usage
if (!function_exists('easy_cache_get_memory_usage_stats')) {
function easy_cache_get_memory_usage_stats() {
	if (function_exists("memory_get_usage")) 	$mem = memory_get_usage (false);
	if (function_exists("memory_get_peak_usage")) $mem_peak = memory_get_peak_usage(false);
	//
	return round($mem/(1024*1024),2) ."MiB / ".round($mem_peak/(1024*1024),2) .'MiB';
	}
}
?>