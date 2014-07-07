<?php
defined( 'ABSPATH' ) or exit();
/*
Plugin`s Administration panel
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

// Important: Check if current user is logged
if ( !is_user_logged_in( ) )  die();

require("lib/func.php");

//Security check
$nonce = isset($_REQUEST['_wpnonce']) ? $_REQUEST['_wpnonce'] : '';
//Constant 


global $wpdb, $post, $upload_dir, $cssfile;
$cssfile = '_css.min.css';
/****************************************************************************************************************************/

//Update settings 

/****************************************************************************************************************************/
if (
isset($_POST['save-settings']) && $_POST['save-settings'] == 'save' &&
isset($_POST['easy-cache-enable-cache']) &&
isset($_POST['cache-folder']) && 
isset($_POST['cache-time']) && 
isset($_POST['easy-cache-exclude-search']) &&
isset($_POST['easy-cache-minify-cache-file']) &&
isset($_POST['easy-cache-auto-rebuild-cache-file']) &&
( wp_verify_nonce( $nonce, 'easy-cache-nonce' )) ) {
//Clean unnecessary slashes and add final slash, no sanitization is needed, because update_option() function takes care of it
$cache_folder = $_POST['cache-folder'];
//Get default uploads directory
$upload_dir = wp_upload_dir();
if (empty($cache_folder) || $cache_folder == '\\' || $cache_folder == '/') $cache_folder = untrailingslashit($upload_dir['basedir']).DIRECTORY_SEPARATOR."cached".DIRECTORY_SEPARATOR;
//
$cache_folder = 	preg_replace("~\\\\+([\"\'\\x00\\\\])~","\\",$cache_folder);
$cache_folder = rtrim(preg_replace("~\/\/+~","/",$cache_folder),'\\/'). DIRECTORY_SEPARATOR;


//Sanitize exclude search queries select
$exclude_search_queries = (!in_array($_POST['easy-cache-exclude-search'], array("Yes","No"))) ? 'No' : $_POST['easy-cache-exclude-search'];
//Sanitize enable caching
$enable_caching = (!in_array($_POST['easy-cache-enable-cache'], array("Yes","No"))) ? 'Yes' : $_POST['easy-cache-enable-cache'];
$minify_cache = (!in_array($_POST['easy-cache-minify-cache-file'], array("Yes","No"))) ? 'Yes' : $_POST['easy-cache-minify-cache-file'];
$auto_rebuild_cache = (!in_array($_POST['easy-cache-auto-rebuild-cache-file'], array("Yes","No"))) ? 'Yes' : $_POST['easy-cache-auto-rebuild-cache-file'];
$skip_jetpack_caching = (!isset($_POST['easy-cache-skip-jetpack-mobile-cache']) ? 'Yes' :  
!in_array($_POST['easy-cache-skip-jetpack-mobile-cache'], array("Yes","No"))) ? 'Yes': $_POST['easy-cache-skip-jetpack-mobile-cache'];

//Search queries cache timeout

//Array of excluded pages/posts
$exclude_pages = (!empty($_POST['pages'])) ? maybe_serialize($_POST['pages']): array();
//Array of minified CSS files
$minified_css_files = (!empty($_POST['easy-cache-minify-css-files'])) ? explode("\n",$_POST['easy-cache-minify-css-files']) : array();
//Remove duplicates
$minified_css_files = array_unique( array_map("trim", $minified_css_files) );
//Initialize var
$hex_minified_css_files = '';
//Convert CSS URL to hex string before inserting in options table, only valid URL are accepted
foreach ($minified_css_files as $minified_css_file) {
	if( easy_cache_is_url(trim($minified_css_file)) ) {
		$hex_minified_css_files .= easy_cache_strhex(trim($minified_css_file))."~~~";
	}
}

	//Update the options
	update_option('easy_cache_option_enable_caching', $enable_caching);
	update_option('easy_cache_option_cache_folder', $cache_folder);
	update_option('easy_cache_option_cache_time',((int)$_POST['cache-time'] < 1) ? 1 : (int)$_POST['cache-time']);
	update_option('easy_cache_option_exclude_search_queries', $exclude_search_queries);
	update_option('easy_cache_option_exclude_pages', $exclude_pages);
	update_option('easy_cache_option_minify_cache_file',$minify_cache);
	update_option('easy_cache_option_auto_rebuild_cache_file',$auto_rebuild_cache);
	update_option("easy_cache_option_minified_css_files", $hex_minified_css_files);
	update_option("easy_cache_option_skip_jetpack_mobile_caching", $skip_jetpack_caching);
	update_option("easy_cache_option_search_cache_timeout",((int)$_POST['easy-cache-search-cache-timeout'] < 1) ? 1 : (int)$_POST['easy-cache-search-cache-timeout']);

//Response handling
?>

<div id="message" class="updated">
  <p>
    <?php _e("The settings", "bodi0-easy-cache"); ?>
    <strong><?php _e("were saved", "bodi0-easy-cache"); ?></strong>.</p>
</div>
<?php 
if (!file_exists($cache_folder)) {
//Attempt to create the unique cache folder
$cache_folder_created =	@mkdir($cache_folder, 0755);
	if (!$cache_folder_created) {
?>
<div id="message" class="updated error">
  <p>
    <?php _e("Warning: Selected cache folder path cannot be created, it is not writable, choose another one, otherwise your blog will not function properly", "bodi0-easy-cache"); ?>.</p>
</div>
<?php
	}
}


}	 

/*****************************************************************************************************************************/

//Delete specific cache file

/****************************************************************************************************************************/

if (isset($_GET['delete-specific-cache']) && strlen($_GET['delete-specific-cache'])==40 
&& ( wp_verify_nonce( $nonce, 'easy-cache-nonce' )) ) {
//Clean unnecessary slashes
$cache_folder = 	get_option('easy_cache_option_cache_folder');
$result = false;
	if ( file_exists( untrailingslashit($cache_folder) .DIRECTORY_SEPARATOR. $_GET['delete-specific-cache'].".cache" ) ) {
		unlink( untrailingslashit($cache_folder) .DIRECTORY_SEPARATOR. $_GET['delete-specific-cache'].".cache" );
		$result = true;
	}
			
	if ($result) {
	//Response handling
		?>
<div id="message" class="updated">
  <p>
    <?php _e("Cached file associated with this page/post ", "bodi0-easy-cache"); ?>
    <strong><?php _e(" was deleted", "bodi0-easy-cache"); ?></strong>.</p>
</div>
<?php 
	}
	//No cache file was found or cannot be deleted
	else { ?>
<div id="message" class="updated error">
  <p>
    <?php _e("No associated cached file was found or cached file cannot be deleted", "bodi0-easy-cache"); ?>.</p>
</div>
<?php		
		}
	}	 


/*****************************************************************************************************************************/

//Delete all cached files

/****************************************************************************************************************************/

if (isset($_GET['delete-cache']) && $_GET['delete-cache']=='reset' 
&& ( wp_verify_nonce( $nonce, 'easy-cache-nonce' )) ) {
//Clean unnecessary slashes
$cache_folder = 	get_option('easy_cache_option_cache_folder');
$result = false;
//Get cache files created by plugin and calculate total size (filter [ and ], otherwise glob() will not function properly)
$cache_folder_items = glob( preg_replace('/(\*|\?|\[)/', '[$1]', untrailingslashit($cache_folder)). DIRECTORY_SEPARATOR .'[a-f0-9]*.cache');
	if (!empty($cache_folder_items)) {
		foreach ($cache_folder_items as $cache_file) {
			unlink($cache_file);
		}
	$result = true;
	}
	if ($result) {
	//Response handling
		?>
<div id="message" class="updated">
  <p>
    <?php _e("All cached files ", "bodi0-easy-cache"); ?>
    <strong><?php _e(" were deleted", "bodi0-easy-cache"); ?></strong>.</p>
</div>
<?php 
	}
	//No cache files found or cannot be deleted
	else { ?>
<div id="message" class="updated error">
  <p>
    <?php _e("No cache files were found or cache files cannot be deleted", "bodi0-easy-cache"); ?>.</p>
</div>
<?php		
		}
	}	 

/*****************************************************************************************************************************/

//Delete minified and combined CSS file

/****************************************************************************************************************************/
if (isset($_GET['delete-css']) && $_GET['delete-css']=='reset' 
&& ( wp_verify_nonce( $nonce, 'easy-cache-nonce' )) ) {
	$result = false;
	//Clean unnecessary slashes
	if (file_exists(get_stylesheet_directory(). DIRECTORY_SEPARATOR .$cssfile)) {
		$result =	unlink(get_stylesheet_directory(). DIRECTORY_SEPARATOR .$cssfile);
	}
	if ($result) {
	//Response handling
		?>
<div id="message" class="updated">
  <p>
<?php _e("Minified and combined CSS file ", "bodi0-easy-cache"); ?>
    <strong><?php _e(" was deleted", "bodi0-easy-cache"); ?></strong>.<br />
<?php		_e("If you want to re-create it, fill the corresponding textarea with URL of CSS files and save settings.", "bodi0-easy-cache"); ?><br />
<strong><?php _e("Important","bodi0-easy-cache"); ?>:</strong>
<?php _e("Keep in mind, that generated cached files *may* include the currently deleted CSS file, you have to re-build those files or re-create the CSS file.","bodi0-easy-cache"); ?>
</div>
<?php 
	}
	//CSS file cannot be found/or deleted
	else { ?>
		<div id="message" class="updated error">
  <p><?php _e("Minified and combined CSS file ", "bodi0-easy-cache"); ?> <code><?php echo $cssfile ?></code>
  <?php _e(" cannot be found or cannot be deleted.","bodi0-easy-cache"); ?></p>
</div>
<?php		}

}



/*****************************************************************************************************************************/

//Restore default settings of the plugin

/******************************************************************************************************************************/

if(isset($_GET['restore']) && $_GET['restore']=='defaults' && ( wp_verify_nonce( $nonce, 'easy-cache-nonce' ))) {
	update_option("easy_cache_option_cache_time","30");
	update_option("easy_cache_option_cache_folder",untrailingslashit($upload_dir['basedir']).DIRECTORY_SEPARATOR."cached".DIRECTORY_SEPARATOR);
	update_option("easy_cache_option_exclude_search_queries","No");
	update_option("easy_cache_option_exclude_pages","");
	update_option("easy_cache_option_enable_caching","No");
	update_option("easy_cache_option_minify_cache_file","Yes");
	update_option("easy_cache_option_auto_rebuild_cache_file","Yes");
	update_option("easy_cache_option_minified_css_files","");
	update_option("easy_cache_option_skip_jetpack_mobile_caching","Yes");
	update_option("easy_cache_option_search_cache_timeout","15");
	//Response handling
?>
<div id="message" class="updated">
  <p>
    <?php _e("The default settings ", "bodi0-easy-cache"); ?>
    <strong><?php _e(" were restored", "bodi0-easy-cache"); ?></strong>.</p>
</div>

<?php 
}
/*****************************************************************************************************************************/
?>
<style type="text/css">
a {
	text-decoration: none !important
}
.wrap form {
	display: inline-block !important
}
.small{font-size: 0.7em; color:gray}
.delete:hover, .cancel:hover, .trash:hover, .delete, .trash, .cancel {color:#c00 !important;}
ul.children li {margin-left:1em;}
textarea{font-family:"Courier New", Courier, monospace;font-size:12px;}
.logo {background-image: url(data:image/gif;base64,R0lGODlhKAAoAPf/AEiYxuPz+7bW6KPL4oC42D2Sw3ay1USWxSSEuzK6/tTn8jaOwRZ8t1ujzLnX6Qms/p7J4RyAuYS62ev4/3+31yyJvuXw9wJxsXCv0wBvr77p/2Wpz02byCKDu0KVxDqRwl3I/iqIvRt/uAt2swl1s/3+/vD3+gVzsu/2+vH3+w54tARysRV8t2GmzgCt/xJ6thB5tejy+Pj7/fv9/vb6/KjO5Ofy+NHl8ZjF32iq0B+BurvZ6qTM4+Lv9iaFvEuaxwd0srzZ6r7b66nP5FKeySeGvMXe7erz+Mjg7iCCuhN7tuPv9unz+JrG4C+Kv8nh7hl+uPT5/Nnq88/k8LPU5xR7tuz0+Q94tff7/dvr9PP4+12kzTKMv5LC3VSfytzr9Iy/2w13tI2/3BF6tTSNwFagyzCLv9Dl8FGdyfX5/On0+uv0+azQ5brY6nq01u71+mus0cTe7WCmzl6lzfr8/aXN4zWOwJHB3ZPC3mKnzh6BuZnG36bN4yK0/lOfymOozzONwM3j8Hm01d/t9arP5cvt/67S5nTQ/iOEu8Hc7KvQ5X3R/G+u0s7j70CSwuP2/0eYxiW1/gKu/xJ2sQBurYa72oq9297s9fn9/zePwcfg7rDa8Fify/n8/RF4sl6r1Zze/yu7/zGOwoPW/4jX/43a/6XT7Y7A3GTL/tny/+bx9/b8//H3+q7k/3Kw0zORxXex0svi773a69rq9Bh9t+n3/sjf7SiGvHnS/n/U/3ez1R+3/yyJvZnN6uz5//f6/FfG/g53s5bE3ji8/qPK4ajW78Hf8IO52bnY6QZzsuHu9gBsqzSKvL/h8yCBubLm//z9/uDx+UCUxFrK/0GWxkOWxVPF/j/B/2yqz6HK4g+y/9/x+xKv/gas/63R5t3s9AOq/v3+/2qr0QBtrPX6/ABtr0jB/pDa/5PZ/PP7/7HT58zn9e/6//L5/V/J/hd6tJvQ7J7R7QGp/gCq//X7/om92ou+29np8tbo8mmr0VHH/93x+3221wCp/gBwsP///yH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS4zLWMwMTEgNjYuMTQ1NjYxLCAyMDEyLzAyLzA2LTE0OjU2OjI3ICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M2IChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDowRDk3MEI3MUE0NDkxMUUzODk4RERCREU4OEI2REQ1MCIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDowRDk3MEI3MkE0NDkxMUUzODk4RERCREU4OEI2REQ1MCI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjBEOTcwQjZGQTQ0OTExRTM4OThEREJERTg4QjZERDUwIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjBEOTcwQjcwQTQ0OTExRTM4OThEREJERTg4QjZERDUwIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+Af/+/fz7+vn49/b19PPy8fDv7u3s6+rp6Ofm5eTj4uHg397d3Nva2djX1tXU09LR0M/OzczLysnIx8bFxMPCwcC/vr28u7q5uLe2tbSzsrGwr66trKuqqainpqWko6KhoJ+enZybmpmYl5aVlJOSkZCPjo2Mi4qJiIeGhYSDgoGAf359fHt6eXh3dnV0c3JxcG9ubWxramloZ2ZlZGNiYWBfXl1cW1pZWFdWVVRTUlFQT05NTEtKSUhHRkVEQ0JBQD8+PTw7Ojk4NzY1NDMyMTAvLi0sKyopKCcmJSQjIiEgHx4dHBsaGRgXFhUUExIREA8ODQwLCgkIBwYFBAMCAQAAIfkEAQAA/wAsAAAAACgAKAAACP8A/wkcSJBgDEXiPkA54e9KBS94zhScSJEitDZ/OvjbyLGjigM4mFQcSdBQAY5k4AijYgTJjgEEAFTZSIvCEZIUv5XZ2IHCmRIjbdSBtNEHG5wEqUTwx8AeioJYTKCIApTgDmkbXWFB2gSIPw5fBqZBputAERYvdGTaAqHHwBlivKIxQbLJRjdVTYgJ0bGvvxctpgwUsvSHlooCvNYbKICv38cjBL0ROCWJvxYUL0Hx52YggQt+T6iAMcJvBmwDbXnyh2ein68zBDLqq0ROjRsxrGTZwc/JRkqvAtDbJtDUMhX4CKrzx2KQQAIdT2BwTpEGD2eiav3TF8qXwE+UiAz/pJPJHxiBAlZwjCAAqQU1q4D16wdCYLRJF+IIDOIvSYp/KdzCkQiBIDXQOfPN14pAsPjTgEB/+EOAQKdwdIIDBg40wTAJJoDJP/eQEMYSTOhwggL/pFEERxhkSFAhDyT4jEAA+JMNG/4AAlQbHCnhlosDgZDgNAJ14U8e+fgDh0AGcDQHkARpIM98u7TzzxP+mPGBPzgI5AFHQwDZiDECpdPHfJKs808MY7CwGRX/yLCiPyfc4OIejqgxkDnzubDJP534sIJ6RvxjAgsbqWCDgSls4A+GA7nTZzwCccEREv+g8MJGMFiBVBy+5VAQKn3CIxAZDfmzwz9R6LARCVmM/zRLIJaQ4I8eaxRkTZ/F/FNCBf4Ay4OvC3AkC0UmcNJXmASFk0CfzWQqggpESCjQHBxNOJEu5bQggR7++DFRKjH2o00A/0ixAiJGAiAQBBxVIENBNHTggUBD+DNsQaQkeI1AhHx1QxhV3NQDDBzxUVAUwdz7Tw3+1FAQO2fOV4pAOfhTyT8H+KPwP3JwhMBNBGXcwjEsMLBEQYckyM0j/7CyVCz/4ODPAQLdYOtGP/w3EAoNkEACL0EUBAo4CeIiEDH+2BEbEwxcIIRAgnDkRK4FxdDDLxONkmAk3nXCjD9NDESBPwXERsNJgCwKZC7zdKOBQL2M44PP/1ih0XnpbqNhAZQCLYKOQPu8408dBXnjDwlTA04ROdRkIN5EGPgTgWCOE0THFhn4oApFWKDRH6aZ/0MDtlVoMpIJP/y1L+BZVOOPEkWTpEUeG7UQlosy4ECLPz4UauAdCI+hC4o4vTFAef6UsbKLCnixUTIA3PGEDfP6ioIUNeTgqj8hDJB5Ig1cwdELPnBhRwUigLZRJnvgnbkyA/xhBgPqJYoAB5U8YWBAADs=); margin-right:5px;}
</style>
<div class="wrap">
  <h2>
  <div style="display:inline-block;width:40px;height:40px;vertical-align:middle" class="logo"></div>
    
	<?php _e("Easy cache [Administration]","bodi0-easy-cache"); ?>
  </h2>
  <p>
<strong><?php _e("Speed up your website by setting the parameters for the caching mechanism.", "bodi0-easy-cache") ?></strong>
    <br />
    &raquo; <?php _e("The plug-in creates in selected cache folder cache file for every requested page, according to the caching parameters.", "bodi0-easy-cache"); ?>
    <br />
		&raquo; <?php _e("Make sure that all links, everywhere in your blog are absolute, not relative (including URL for backgrounds), according RFC 2396.","bodi0-easy-cache"); ?>
    <br />

    &raquo; <?php _e("Search queries or individual pages and posts can be excluded from caching.", "bodi0-easy-cache"); ?><br />
		&raquo; <?php _e("The cached files are automatically  updated/deleted when pages/posts are updated (including when a comment is added or updated, which causes the comment count for the post to update) or deleted permanently.","bodi0-easy-cache"); ?><br />
<div style="background-color:#ccc;width:100%;height:1px"></div>
<br />

    <strong><?php _e("Important","bodi0-easy-cache"); ?>:</strong> <?php _e("Make sure the call to <code>wp_footer();</code> function is at the very bottom in your theme`s <code>footer.php</code> file, right before closing <code>< /body></code> tag. Otherwise contents after <code>wp_footer();</code> may be not included in generated cache file.","bodi0-easy-cache"); ?><br />
<strong><?php _e("Important","bodi0-easy-cache"); ?>:</strong> <?php _e("Remember to log out or use different browser if you want to test if caching mechanism is working, only not logged-in users can benefit from caching.","bodi0-easy-cache"); ?>
  </p>
  
  
  <?php if (get_option('easy_cache_option_enable_caching') !='Yes') { ?>
<div id="message" class="updated error">
  <p>
    <?php _e("Warning: Caching is disabled, your blog will display non-cached pages to users", "bodi0-easy-cache"); ?>.</p>
</div>	<?php		}?>
  <form name="form-cache" id="form-cache" action="?page=<?php echo $_GET['page']; ?>" method="post">
  <input type="hidden" name="save-settings" value="save"/>
    <table class="widefat">
      <thead>
      <tr><th colspan="2"><?php _e("Parameters","bodi0-easy-cache"); ?></th></tr>
      </thead>
      <tbody>
      <tr class="alternate">
        <th style="vertical-align:top"><?php _e("Enable caching","bodi0-easy-cache"); ?>:
        <div class="small"><?php _e("Select to enable or disable caching mechanism globally in your website.","bodi0-easy-cache"); ?></div>
        </th>
        <td style="vertical-align:top"><select name="easy-cache-enable-cache" id="easy-cache-enable-cache">
            <?php 
						$easy_cache_option_enable_caching_yes = '';
						$easy_cache_option_enable_caching_no = '';
		
				if (get_option('easy_cache_option_enable_caching') == 'Yes') $easy_cache_option_enable_caching_yes = ' selected="selected" ';
				else $easy_cache_option_enable_caching_no = ' selected="selected" ';
				 ?>
            <option value="Yes" <?php echo $easy_cache_option_enable_caching_yes;?>>
            <?php _e("Yes", "bodi0-easy-cache"); ?>
            </option>
            <option value="No" <?php echo $easy_cache_option_enable_caching_no;?>>
            <?php _e("No", "bodi0-easy-cache"); ?>
            </option>
          </select></td>
      </tr>
      <tr>
        <th style="vertical-align:top"><?php _e("Cache folder path", "bodi0-easy-cache"); ?>:
          <div class="small">
          <?php _e("Make sure that folder path exists and is writable, i.e. the permissions are 755 or higher.","bodi0-easy-cache");?>
					<br/>
					<?php _e("The default value is sub-folder, named < cached > inside the default WordPress 'uploads' folder. Leave this field empty for restoring the default path.", "bodi0-easy-cache"); ?>
          </div>
        </th>
        <td style="vertical-align:top"><input name="cache-folder" id="cache-folder" type="text" style="width:400px" 
        value="<?php echo get_option('easy_cache_option_cache_folder') ?>"/></td>
      </tr>
      <tr class="alternate">
        <th style="vertical-align:top"><?php _e("Cached file expires after", "bodi0-easy-cache"); ?>:
          <div class="small">
            <?php _e("Value in minutes, integers only, greater than zero","bodi0-easy-cache");?>,<br/> 
            <?php _e("1 hour=60, 1 day=1440, 1 week=10080.", "bodi0-easy-cache")?>
          </div>
        </th>
        <td style="vertical-align:top"><input name="cache-time" id="cache-time" type="text" value="<?php echo get_option('easy_cache_option_cache_time');?>" style="width:80px"/></td>
      </tr>
      <tr>
        <th style="vertical-align:top"><?php _e("Exclude search queries from caching", "bodi0-easy-cache"); ?>:
          <div class="small">
            <?php _e("Select to exclude or not from caching all search results in your web site. It will be useful to set this option to 'No' if you have huge amount of traffic, generated by searches in order to speed-up page/post display.", "bodi0-easy-cache"); ?>
          </div>
        </th>
        <td style="vertical-align:top"><select name="easy-cache-exclude-search" id="easy-cache-exclude-search"
        onchange="javascript: if(jQuery(this).val()=='Yes') jQuery('#easy-cache-search-cache-timeout').attr('readonly','readonly'); else jQuery('#easy-cache-search-cache-timeout').removeAttr('readonly');" 
        >
            <?php 
						$exlude_search_selected_yes = '';
						$exlude_search_selected_no = '';
				if (get_option('easy_cache_option_exclude_search_queries') == 'Yes') $exlude_search_selected_yes = ' selected="selected" ';
				else $exlude_search_selected_no = ' selected="selected" ';
				 ?>
            <option value="Yes" <?php echo $exlude_search_selected_yes;?>>
            <?php _e("Yes", "bodi0-easy-cache"); ?>
            </option>
            <option value="No" <?php echo $exlude_search_selected_no;?>>
            <?php _e("No", "bodi0-easy-cache"); ?>
            </option>
          </select>
          </td>
      </tr>
      <tr class="alternate">
      <th style="vertical-align:top"><?php _e("Search queries cache expire after","bodi0-easy-cache"); ?>: 
      <div class="small"><?php _e("Choose search results cache timeout (if search queries are not excluded from caching), in minutes, integers only, select value for the timeout lower than global cache expire time if you prefer to have fresher information in searches","bodi0-easy-cache"); ?>,<br />
            <?php _e("1 hour=60, 1 day=1440, 1 week=10080.", "bodi0-easy-cache")?>

</div>
      </th>
      <td style="vertical-align:top"><input type="text" name="easy-cache-search-cache-timeout"  id="easy-cache-search-cache-timeout" 
          value="<?php echo get_option('easy_cache_option_search_cache_timeout');?>" style="width:80px" 
					<?php echo ( get_option('easy_cache_option_exclude_search_queries') == 'No' ) ? '' : ' readonly="readonly"'; ?>/></td>
      </tr>
      <tr>
      	<th style="vertical-align:top"><?php _e("Minify saved cache file","bodi0-easy-cache"); ?>:<br />
<div class="small"><?php _e("Select to optimize saved cache file on disk for further performance improvement by striping extra spaces, new lines, tabs, etc., on average minification reduces the cache file size within 6 to 12%, depending on how formatted is the HTML content of non-cached file.","bodi0-easy-cache"); ?></div></th>
        
        <td style="vertical-align:top">
        <select name="easy-cache-minify-cache-file" id="easy-cache-minify-cache-file">
            <?php 
						$exlude_minify_selected_yes = '';
						$exlude_minify_selected_no = '';
				if (get_option('easy_cache_option_minify_cache_file') == 'Yes') $exlude_minify_selected_yes = ' selected="selected" ';
				else $exlude_minify_selected_no = ' selected="selected" ';
				 ?>
            <option value="Yes" <?php echo $exlude_minify_selected_yes;?>>
            <?php _e("Yes", "bodi0-easy-cache"); ?>
            </option>
            <option value="No" <?php echo $exlude_minify_selected_no;?>>
            <?php _e("No", "bodi0-easy-cache"); ?>
            </option>
          </select>
        
        </td>
      </tr>
			<tr class="alternate">
      	<th style="vertical-align:top"><?php _e("Rebuild cached file on page/post/comment update","bodi0-easy-cache"); ?>:<br />
<div class="small"><?php _e("Select to automatically recreate cached file on disk when post or page has been modified (including when a comment is added or updated, which causes the comment count for the page/post to update), useful if you do not want your website visitors to wait for cache to expire to get the latest page/post/comments changes.","bodi0-easy-cache"); ?></div></th>
        
        <td style="vertical-align:top">
        <select name="easy-cache-auto-rebuild-cache-file" id="easy-cache-auto-rebuild-cache-file">
            <?php 
						$auto_rebuild_selected_yes = '';
						$auto_rebuild_selected_no = '';
				if (get_option('easy_cache_option_auto_rebuild_cache_file') == 'Yes') $auto_rebuild_selected_yes = ' selected="selected" ';
				else $auto_rebuild_selected_no = ' selected="selected" ';
				 ?>
            <option value="Yes" <?php echo $auto_rebuild_selected_yes;?>>
            <?php _e("Yes", "bodi0-easy-cache"); ?>
            </option>
            <option value="No" <?php echo $auto_rebuild_selected_no;?>>
            <?php _e("No", "bodi0-easy-cache"); ?>
            </option>
          </select>
        
        </td>
      </tr>
      <tr>
        <th style="vertical-align:top"><?php _e("Exclude pages/posts from caching", "bodi0-easy-cache"); ?>:
          <div class="small">
            <?php _e("Select pages or posts from published ones, which you don`t want to be cached, sorted by title, private pages or posts are also in this list. Excluding posts/pages is useful when you have registration form or any other specific (custom) search form implemented on these posts/pages.", "bodi0-easy-cache"); ?><br />
<?php _e("You can delete associated with given post/page cached file by clicking on the red X","bodi0-easy-cache"); ?>.
          </div>
        </th>
        <td style="vertical-align:top">
        <strong><?php _e("Pages","bodi0-easy-cache"); ?></strong>
        <div style="height: auto;max-height: 400px;min-height: 40px;overflow: scroll;width: 400px;">
        <?php 
				//Pages arguments
				$args_pages = array(
				'sort_order' => 'ASC',
				'sort_column' => 'post_title',
				'hierarchical' => 0,
				'exclude' => '',
				'include' => '',
				'meta_key' => '',
				'meta_value' => '',
				'authors' => '',
				'child_of' => 0,
				'parent' => -1,
				'exclude_tree' => '',
				'number' => '',
				'offset' => 0,
				'post_type' => 'page',
				'post_status' => array('publish', 'private', 'future')
			); 
			
			
 				//Get pages
				$pages = get_pages( $args_pages );
				
				//Get excluded pages from options (if any) and covert them to array
				$excluded = get_option('easy_cache_option_exclude_pages');
				$excluded = (!is_array($excluded)) ? maybe_unserialize($excluded) : '';
				$checked = '';
				foreach ( $pages as $page ) {
					//If page is excluded then check it...
					if (in_array($page->ID,(array)$excluded)) { $checked = ' checked="checked" ';}
					//Show link to delete cached file only if it exists
					if ( file_exists( untrailingslashit( get_option("easy_cache_option_cache_folder") ) .DIRECTORY_SEPARATOR. sha1(get_permalink($page->ID)).".cache" ) ) {
					echo '( <a href="?page='. $_GET['page'] .'&delete-specific-cache='.sha1(get_permalink($page->ID)).
					'&amp;_wpnonce='. wp_create_nonce( 'easy-cache-nonce' ) 
					.'" title="'.__("Delete associated cached file","bodi0-easy-cache").'"
					class="delete"
					>X</a> ) ';}
					
					echo '<input type="checkbox" name="pages[]" id="page-'.$page->ID.'" value="' .  $page->ID  . '" '.$checked.'/>'
					.'<a href="'.get_permalink($page->ID).'" target="_blank">'.$page->post_title.'</a>'.' <em class="small">('.$page->post_date.', '.$page->post_status.', '. get_the_author_meta( 'user_nicename', $page->post_author).')</em><br/>';
					
					$checked='';
				}

				?>
        </div>
          
       <br /><br />   
       <strong><?php _e("Posts","bodi0-easy-cache"); ?></strong>
        <div style="height: auto;max-height: 400px;min-height: 40px;overflow: scroll;width: 400px;">
					<?php
					$args_posts = array(
					'posts_per_page'   => -1,
					'offset'           => 0,
					'category'         => '',
					'orderby'          => 'post_title',
					'order'            => 'ASC',
					'include'          => '',
					'exclude'          => '',
					'meta_key'         => '',
					'meta_value'       => '',
					'post_type'        => 'post',
					'post_mime_type'   => '',
					'post_parent'      => '',
					'post_status'      => array('publish','private','future'),
					'suppress_filters' => true );
				//Get posts
				$posts = get_posts( $args_posts );
								
				//Get excluded posts from options (if any) and covert them to array
				$checked = '';
				foreach ( $posts as $post ) {
					//If page is excluded then check it...
					if (in_array($post->ID,(array)$excluded)) { $checked = ' checked="checked" ';}
					//Show link to delete cached file only if it exists
					if ( file_exists( untrailingslashit( get_option("easy_cache_option_cache_folder") ) .DIRECTORY_SEPARATOR. sha1(get_permalink($post->ID)).".cache" ) ) {
					echo '( <a href="?page='. $_GET['page'] .'&delete-specific-cache='.sha1(get_permalink($post->ID)).
					'&amp;_wpnonce='. wp_create_nonce( 'easy-cache-nonce' ) 
					.'" title="'.__("Delete associated cached file","bodi0-easy-cache").'"
					class="delete"
					>X</a> ) ';}
					echo '<input type="checkbox" name="pages[]" id="page-'.$post->ID.'" value="' .  $post->ID  . '" '.$checked.'/>'
					.'<a href="'.get_permalink($post->ID).'" target="_blank">'.$post->post_title.'</a>'.' <em class="small">('.$post->post_date.', '.$post->post_status.',	'. get_the_author_meta( 'user_nicename', $post->post_author) . ')</em><br/>';

					$checked='';
				}

				?>
          </div>
          </td>
      </tr>
      <tr class="alternate">
           
      
      <th style="vertical-align:top"><?php _e("Minify and combine CSS files","bodi0-easy-cache"); ?>:
      <div class="small"><?php _e("Insert absolute URL (valid URL according RFC 2396) of CSS files in sequence of their appearance in non-cached page for minification and combination. This process will reduce the number and size of HTTP requests to your server. The CSS files will be merged as single cached CSS resource file, named<code>_css.min.css</code> and saved in your current theme's folder.","bodi0-easy-cache"); 			
			?>
			<br />
	<?php	_e("This file will be included in every cached file, old links will be removed.","bodi0-easy-cache"); ?><br />
<strong><?php _e("Important","bodi0-easy-cache");?>:</strong><br />
<?php _e("Make sure that any URL inside the original CSS code is abosulte, not relative (otherwise you will have missing backgrounds).  Also make sure you type the URL of files you want to combine and minify exactly as it is in your original page / post (for example some stylesheets links may have dynamic content attached to them, like: <u>http://www.example.com/color.php?ver=1.2</u>), otherwise minification and combination will not work correctly. If you modify the original CSS files, remember to save settings here in order to re-generate cached CSS resource file.","bodi0-easy-cache"); ?><br />

</div>
      </th>
     <?php
			/* Display CSS minification and combination option only if 'allow_url_fopen' is true */
			if (ini_get("allow_url_fopen")) {
			?>
     
      <td style="vertical-align:top">
      <strong><?php _e("URL of CSS files, one per line","bodi0-easy-cache"); ?>, <?php _e("duplicates will be removed","bodi0-easy-cache"); ?></strong>
      <textarea name="easy-cache-minify-css-files" id="easy-cache-minify-css-files" style="width:400px;height:300px;" ><?php 
			
			//Initialize the arrays and the buffer
			$minified_css_files = array();
			$new_minified_css_files = array();
			$buffer = '';
			$merged_css_files_list='';
			//Decode the values from hex to string...and convert it to array()
			$minified_css_files =  explode("~~~",get_option("easy_cache_option_minified_css_files")); 
			$minified_css_files_list = '';
			foreach($minified_css_files as $value) {
				$minified_css_files_list .= easy_cache_hexstr($value)."\n";
				$new_minified_css_files[] =  trim(easy_cache_hexstr($value));
				
			}
				//Remove last new line from results
				echo rtrim($minified_css_files_list,"\n");
			//Check to see if CSS files needs to be minified and combined and generate the file only is user save the settings
			if (!empty($new_minified_css_files) && isset($_POST['save-settings']) && $_POST['save-settings'] == 'save') {		 
				foreach( $new_minified_css_files as $new_minified_css_file)  {
					if (trim($new_minified_css_file) != '' && easy_cache_is_url($new_minified_css_file)) 
					$buffer .= @file_get_contents($new_minified_css_file);
					$merged_css_files_list .= "\n".$new_minified_css_file;
				}
			//Write new CSS minified and combined file if contents not empty 
			if (trim($buffer) != '') @file_put_contents(get_stylesheet_directory() .DIRECTORY_SEPARATOR. $cssfile, 
			"/*Merged CSS files on ".gmdate('D, Y-m-d H:i:s')." GMT*/"."\n/*".$merged_css_files_list."*/"
			.easy_cache_optimize_css_files($buffer));
		
				}
		//No minification and combination of CSS
		else {
			//
		}	
				
				
				
				?></textarea>
      </td>
     
     <?php
			}
			/* The 'allow_url_fopen' is set to false, so no CSS minification and combination is possible */
			else { ?>
			<td style="vertical-align:top"><?php _e("The <code>allow_url_fopen</code> variable is set to <code>Off</code> in your <code>php.ini</code> configuration, no CSS minification and combination is possible. Contact your hosting company or web server administrator for details how this can be changed.","bodi0-easy-cache"); ?></td>	
		 <?php }
		 //
		 ?>
     
      </tr>
      
      <tr>
	<th style="vertical-align:top"><?php _e("Do not cache mobile pages/posts","bodi0-easy-cache")?>
	<div class="small"><?php _e("Select to skip caching of pages/posts, generated by Jetpack by WordPress.com mobile theme, highly recommended to set this to 'Yes', otherwise your website visitors may see a mix of the mobile pages/posts and desktop pages/posts. Skipping the caching mechanism for mobile version of your website is necessary, because the Jetpack module by WordPress.com provides his own caching procedure.","bodi0-easy-cache");?></div>
	</th>
	<td style="vertical-align:top">
	<?php if ( !function_exists( 'jetpack_is_mobile' ) ) {
		echo "<strong>"; 
		_e("Jetpack module by WordPress.com is Not Available","bodi0-easy-cache");  
		echo "</strong>"; 
		}
		 //Jetpack is installed and available
		  else { ?>
		<select name="easy-cache-skip-jetpack-mobile-cache" id="easy-cache-skip-jetpack-mobile-cache">
            <?php 
						$easy_cache_option_skip_jetpack_cache_yes = '';
						$easy_cache_option_skip_jetpack_cache_no = '';
		
				if (get_option('easy_cache_option_skip_jetpack_mobile_caching') == 'Yes') $easy_cache_option_skip_jetpack_cache_yes = ' selected="selected" ';
				else $easy_cache_option_skip_jetpack_cache_no = ' selected="selected" ';
				 ?>
            <option value="Yes" <?php echo $easy_cache_option_skip_jetpack_cache_yes;?>>
            <?php _e("Yes", "bodi0-easy-cache"); ?>
            </option>
            <option value="No" <?php echo $easy_cache_option_skip_jetpack_cache_no;?>>
            <?php _e("No", "bodi0-easy-cache"); ?>
            </option>
          </select>	
			
			
			
	<?php	}
	?></td>
	</tr>
      
      
      <tr class="alternate">
        <td colspan="2" style="vertical-align:top"><p>
            <input type="submit" name="submit" class="button-primary submit" value="<?php _e("Save settings","bodi0-easy-cache"); ?>"/>
            &nbsp;<a accesskey="c" href="javascript:void(0)" onclick="document.getElementById('form-cache').reset();" class="button-secondary cancel"><?php _e("Reset", "bodi0-easy-cache"); ?></a> 
            &nbsp;<a accesskey="d" href="?page=<?php echo $_GET['page']; ?>&amp;restore=defaults&amp;_wpnonce=<?php echo wp_create_nonce( 'easy-cache-nonce' ) ?>" class="button-secondary cancel"><?php _e("Restore default settings", "bodi0-easy-cache"); ?></a> 
          </p></td>
      </tr></tbody>
    </table>
    <?php 
//Nonce field
wp_nonce_field( 'easy-cache-nonce' );
?>
  </form>
  
<?php
//Calculate some statistical things
	$i=0;
	$divider = 1;
	$time_divider_i = 60;
	$time_divider_x = 60;
	$time_divider_v = 60;
	$size_measure = '';
	$time_measure = '';
	
	$total_cache_size = array();
	$total_cache_time = array();
	$total_cache_file_total = 0;
	$total_cache_age_total = 0;
	
	$min_cache_file_size = 0;
	$max_cache_file_size = 0;
	$average_cache_size = 0;
	
	$min_cache_time_display = '';
	$max_cache_time_display = '';
	$avg_cache_time_display = '';
	
	$avg_cache_age = 0;
	$min_cache_age = 0;
	$max_cache_age = 0;
	
	$cache_folder = 	get_option('easy_cache_option_cache_folder');
	//Get cache files created by plugin and calculate total size (filter [ and ], otherwise glob() will not function properly)
		$cache_folder_items = easy_cache_list_folders(untrailingslashit($cache_folder). DIRECTORY_SEPARATOR, '[a-f0-9]*.cache');
		if (!empty($cache_folder_items)) {
			
		foreach ($cache_folder_items as $cache_file) {
			//Stats
			$i++;
			$total_cache_size[] = filesize($cache_file);
			$total_cache_file_total = array_sum($total_cache_size);
			//
			$average_cache_size = round($total_cache_file_total / $i, 2);
			$min_cache_file_size = min($total_cache_size);
			$max_cache_file_size = max($total_cache_size);
			//
			$total_cache_time[] = filemtime($cache_file);
			$total_cache_age_total = array_sum($total_cache_time);
			$avg_cache_age = round($total_cache_age_total / $i);
			//Reverse order for times
			$min_cache_age = max($total_cache_time);
			$max_cache_age = min($total_cache_time);
		}

			//Format units
			if ($total_cache_file_total<1024) { 
				$size_measure =  __("bytes","bodi0-easy-cache"); $divider = 1; 
			}
			if ($total_cache_file_total<1024*1024 && $total_cache_file_total >1024) { 
				$size_measure =  __("KiB","bodi0-easy-cache"); $divider = 1024; 
			}
			if ($total_cache_file_total<1024*1024*1024 && $total_cache_file_total >1024*1024) { 
				$size_measure =  __("MiB","bodi0-easy-cache"); $divider = 1024*1024; 
			}
			if ($total_cache_file_total<1024*1024*1024*1024 && $total_cache_file_total >1024*1024*1024) { 
				$size_measure =  __("GiB","bodi0-easy-cache"); $divider = 1024*1024*1024;
			}
			//Format dates (min)
			if (time() - $min_cache_age < 60) { 
				$min_cache_time_display =  __("sec","bodi0-easy-cache"); $time_divider_i = 1;
			}
			if (time() - $min_cache_age < 3600 && time() - $min_cache_age > 60) { 
				$min_cache_time_display =  __("min","bodi0-easy-cache"); $time_divider_i = 60;
			}
			if (time() - $min_cache_age < 86400 && time() - $min_cache_age > 3600) { 
				$min_cache_time_display =  __("h","bodi0-easy-cache"); $time_divider_i = 3600;
			}
			if (time() - $min_cache_age < 604800 && time() - $min_cache_age >86400) { 
				$min_cache_time_display = __("d","bodi0-easy-cache");  $time_divider_i = 86400;
			}
			if (time() - $min_cache_age > 604800) {
				$min_cache_time_display = __("w","bodi0-easy-cache"); $time_divider_i = 604800;
			}
			
			//Format dates (max)
			if (time() - $max_cache_age < 60) { 
				$max_cache_time_display =  __("sec","bodi0-easy-cache"); $time_divider_x = 1;
			}
			if (time() - $max_cache_age < 3600 && time() - $max_cache_age > 60) { 
				$max_cache_time_display =  __("min","bodi0-easy-cache"); $time_divider_x = 60;
			}
			if (time() - $max_cache_age < 86400 && time() - $max_cache_age > 3600) { 
				$max_cache_time_display =  __("h","bodi0-easy-cache"); $time_divider_x = 3600;
			}
			if (time() - $max_cache_age < 604800 && time() - $max_cache_age >86400) { 
				$max_cache_time_display = __("d","bodi0-easy-cache");  $time_divider_x = 86400;
			}
			if (time() - $max_cache_age > 604800) {
				$max_cache_time_display = __("w","bodi0-easy-cache"); $time_divider_x = 604800;
			}
			
			//Format dates (avg)
			if (time() - $avg_cache_age < 60) { 
				$avg_cache_time_display =  __("sec","bodi0-easy-cache"); $time_divider_v = 1;
			}
			if (time() - $avg_cache_age < 3600 && time() - $avg_cache_age > 60) { 
				$avg_cache_time_display =  __("min","bodi0-easy-cache"); $time_divider_v = 60;
			}
			if (time() - $avg_cache_age < 86400 && time() - $avg_cache_age > 3600) { 
				$avg_cache_time_display =  __("h","bodi0-easy-cache"); $time_divider_v = 3600;
			}
			if (time() - $avg_cache_age < 604800 && time() - $avg_cache_age >86400) { 
				$avg_cache_time_display = __("d","bodi0-easy-cache");  $time_divider_v = 86400;
			}
			if (time() - $avg_cache_age > 604800) {
				$avg_cache_time_display = __("w","bodi0-easy-cache"); $time_divider_v = 604800;
			}
		
			
	} 
?>  
<p> <a href="?page=<?php echo $_GET['page']?>&amp;_wpnonce=<?php echo wp_create_nonce( 'easy-cache-nonce' ) ?>" class="alignright" >
    <?php _e("Refresh statistics", "bodi0-easy-cache"); ?>
    </a> &nbsp; </p>  
  <table class="widefat">
    <thead>
    <tr><th colspan="2"><?php _e("Statistics","bodi0-easy-cache"); ?></th></tr>
    </thead>
    <tbody>
    <tr class="alternate">
      <th style="vertical-align:top"> <?php _e("Average Web Server load","bodi0-easy-cache"); ?>:
      <div class="small"><?php _e("These values represents the current system software, average system load in the last 1, 5 and 15 minutes, also memory usage (current and peak).","bodi0-easy-cache");?><br />
<?php _e("Values above 80 means that your web server is overloaded.","bodi0-easy-cache"); ?></div>
      </th>
      <td>
			<strong><?php 
				echo 'PHP: '.phpversion(). '<br/>
				Apache: ';
				echo $_SERVER['SERVER_SOFTWARE'] .'<br/>';
				
				$load_data = array();
				$load_data = easy_cache_get_server_load2(); 
			 	if (!empty($load_data)) {
					echo $load_data;
				}
				else  _e("N/A","bodi0-easy-cache"); 
			?></strong>
      <br />
      <strong>
			<?php 
			echo easy_cache_get_memory_usage_stats();
			?>      
      </strong>
			</td>
    </tr>
    <tr>
      <th style="vertical-align:top"><?php _e("Number of cache files found", "bodi0-easy-cache"); ?><br />
			<?php _e("(including cached searches and custom pages)","bodi0-easy-cache"); ?>:
      <div class="small"><?php _e("Files are inside cached folder","bodi0-easy-cache"); ?><br />
      <code><?php echo get_option("easy_cache_option_cache_folder"); ?></code>
      </div></th>
      <td><strong><?php echo $i ?></strong></td>
    </tr>
    <tr class="alternate">
    <th style="vertical-align:top"><?php _e("Minified and combined CSS file","bodi0-easy-cache"); ?>:
    <div class="small"><?php _e("Details about size of minified and combined CSS file, in folder: ","bodi0-easy-cache"); ?>
    <br />
    <?php echo "<code>".get_stylesheet_directory()."</code>"; ?>
    </div>
    </th>
    <td><strong>
    <?php 
		//Flag
		$css_file_exists = 0;
			//
			if (file_exists(get_stylesheet_directory()."/".$cssfile)) {
				echo round( filesize(get_stylesheet_directory()."/".$cssfile )/ 1024,2); 	
				_e("KiB","bodi0-easy-cache");  _e(" created on ","bodi0-easy-cache"); echo gmdate("Y-m-d H:i:s",filemtime(get_stylesheet_directory()."/".$cssfile)) . ' GMT';
				$css_file_exists = 1;
			}
				else _e("Not Available","bodi0-easy-cache");
		?></strong>
    </td>
    </tr>
    <tr>
      <th style="vertical-align:top"><?php _e("Cache files age (min/max/avg)","bodi0-easy-cache"); ?>:
      <div class="small"><?php _e("Displays freshness of the cached files.","bodi0-easy-cache"); ?></div>
      </th>
      <td><strong><?php 
			if (!empty($cache_folder_items)) {
				echo round((time()-$min_cache_age)/$time_divider_i).' '.$min_cache_time_display;
				echo ' / '. round((time()-$max_cache_age)/$time_divider_x).' '.$max_cache_time_display;
				echo ' / '. round((time()-$avg_cache_age)/$time_divider_v).' '.$avg_cache_time_display; 
			}
			?></strong></td>
    </tr>
    	
	<tr class="alternate">
      <th style="vertical-align:top"><?php _e("Cache files size (min/max/avg)","bodi0-easy-cache"); ?>:
      <div class="small"><?php _e("Displays smallest, largest and averaged size of saved cached files.","bodi0-easy-cache"); ?></div>
      </th>
      <td><strong><?php 
			echo round($min_cache_file_size/1024,2);_e("KiB","bodi0-easy-cache"); 
			echo ' / '.round($max_cache_file_size/1024,2);_e("KiB","bodi0-easy-cache");
			echo ' / '. round($average_cache_size/1024,2); _e("KiB","bodi0-easy-cache");?></strong></td>
    </tr>
    <tr>
    <th style="vertical-align:top">
    <?php _e("Total space occupied by cache files","bodi0-easy-cache"); ?>:
        </th>
        <td>
        <strong>
        <?php
				
				echo (!empty($total_cache_file_total)) ? round($total_cache_file_total / $divider,2) : 0; 
				echo $size_measure;
				 
?></strong>
        </td></tr>
    <tr>
      <td colspan="2">
   &nbsp;<a accesskey="c" href="?page=<?php echo $_GET['page']; ?>&amp;delete-cache=reset&amp;_wpnonce=<?php echo wp_create_nonce( 'easy-cache-nonce' ) ?>" class="button-secondary cancel"><?php _e("Delete all cached files", "bodi0-easy-cache"); ?></a> 

<?php if ($css_file_exists == 1) { ?>
   &nbsp;<a accesskey="s" href="?page=<?php echo $_GET['page']; ?>&amp;delete-css=reset&amp;_wpnonce=<?php echo wp_create_nonce( 'easy-cache-nonce' ) ?>" class="button-secondary cancel"><?php _e("Delete combined and minified CSS file", "bodi0-easy-cache"); ?></a> 

<?php   } ?>

        </td>
    </tr>
    </tbody>
  </table>
</div>
<br />

  <?php _e("If you find this plugin useful, I wont mind if you buy me a beer", "bodi0-easy-cache"); ?>
  :
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" style="display:inline-block !important">
  <input type="hidden" name="cmd" value="_s-xclick"/>
  <input type="hidden" name="hosted_button_id" value="LKG7EXVNPJ7EN"/>
  <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!"  style="vertical-align: middle !important; border:0"/>
</form>
