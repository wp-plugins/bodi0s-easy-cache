=== bodi0`s Easy cache ===
Contributors:
Donate link:
Tags: advanced cache, benchmark, benchmarking, cache, caching, cash, debug, debugging, execution, generation, highly extensible, includes extensive documentation, loading, options panel included, performance, easy cache, easycache, speed, super cache, w3c validated code, websharks framework, wp-cache
Requires at least: 3.2.0
Tested up to: 3.8.1
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Speed up your blog with Easy Cache, bullet-proof and easy-to-use website caching mechanism for WordPress that just works.

== Description ==
Easy cache takes a real-time snapshot of every Page, Post, Category, Link, etc. These snapshots are then stored (cached) into folder of your choice, so they can be referenced and served to the visitor later, in order to save database processing time that has been slowing your website down.

The Easy cache plugin uses configuration options that you select from the Administration panel. Search for `Easy cache` in your `Settings` page. 
Easy cache excludes administrative or WP system login pages from caching (i.e. it works only on public part of your blog). Only **NOT**  logged-in users (which are the most) can benefit from caching mechanism when they visit the public part of your website.

There is an automatic cache expiration system, which runs through WordPress® behind-the-scene, according to your `Cached file expires after` setting, also various cache and server load statistics are available for precise tracking of what is going on.

The Easy cache plugin has been tested with various permalink settings, various pagination plugins like `WP Pagenavi` and `WPML WordPress Multilingual Plugin`, other multi-language and custom pagination plugins however should also work, if you experience troubles, post your issue on the plugin's forum.

**Supported languages**
- English

- French

- Bulgarian


== Installation ==
WordPress® can only handle one cache plugin being activated at a time. Please remove any existing cache plugins that you've tried in the past. 
In other words, if you've installed W3 Total Cache, WP Super Cache, DB Cache Reloaded, 
or any other caching plugin, uninstall them all before installing Easy cache. One way to check, is to make sure this file: `wp-content/advanced-cache.php` and/or `wp-content/object-cache.php` are **NOT** present; and if they are, delete these files **BEFORE** installing Easy cache. 

Those files will only be present if you have a caching plugin already installed. If you don't see them, you're ready to install Easy cache.

To verify that Easy cache is working, navigate your site like a regular visitor would. Right-click on any page, choose `View Page Source`, then scroll to the bottom of the document. At the very bottom you'll find comments that show Easy cache statistical information. You should also notice that page-to-page navigation is faster, compared to what you experienced prior to installing Easy cache.

1. Upload unzipped archive of the plugin to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to `Settings -› Easy cache`, select to enable caching and/or tweak other parameters and save the settings.

== Frequently Asked Questions ==


**Why WordPress® pages need to be cached?**

Briefly explanation, according `Wikipedia`, in computer science, a `cache` (pronounced /kash/) is a collection of data, which duplicates original values, stored elsewhere or computed earlier, where the original data is expensive to fetch (owing to longer access time) or to compute, compared to the cost of reading the cache. In other words, a cache is a temporary storage area, where frequently accessed data can be stored for rapid access. Once the data is stored in the cache, it can be used in the future by accessing the cached copy rather than re-fetching or recomputing the original data.

WordPress® is a database-driven publishing platform. That means you have all these great tools on the back-end of your site to work with, but it also means that every time a Post/Page/Category is accessed on your site, dozens of connections to the database have to be made and literally thousands of PHP routines run in harmony behind-the-scenes to make everything dance. The problem is, for every request, that a browser sends to your site, all of these routines and connections have to be made (yes, every single time). This can be very system resource consuming, which actually leads to slowdowns of your website. 

In most cases, the big part of the content on your site remains unchanged for at least a couple of minutes, or maybe an hour at a time. 
If you've been using WordPress® for period of time, you've probably noticed that (on average) your site does not 
load as fast as it was by initial installation. All of the above is the reason for this.


**Where are the cache files stored on my server?**

The cache files are stored in a sub-folder of path your choice, by default this is sub-folder, named `< cached >` inside the default WordPress 'uploads' folder. This folder needs to remain writable and accessible (i.e. with permissions set to 0755 or higher). 
Inside the `< cached >` folder Easy cache stores all snapshots as files with names `<SHA1>.cache` (file name is constructed as an `SHA1` hash of the requested URL).
You can remove all cached files from the Administration panel in order to revoke caching mechanism to rebuild the cache upon next post/page access again.


See also: `Settings -› Easy cache -› Cache folder path` for further details.


**Is there a need to modify my current theme in order caching to work correctly?**

Maybe. Make sure the call to `wp_footer();` function is at the very bottom in your theme's `footer.php` file, right before closing `< /body>` tag.  Otherwise contents after `wp_footer();` may be not included in generated cache file (i.e. these contents will be most probably scripts or style-sheets, so it is important to check this out).



**Will comments or any other dynamic parts of my blog update immediately?**

It depends on your configuration of Easy cache. There is an automatic cache expiration system, which runs through WordPress® behind-the-scene, according to your Cached file expires setting (see: `Settings -› Easy cache -› Cached file expires after` and `Settings -› Easy cache -› Rebuild cached file on page/post/comment update`). The default value of 5 minutes is suitable for most cases. If you don't update your site too often, you could set this to 120 minutes or longer. The longer the cache expiration time is, the greater your performance gain. Alternatively, the shorter the expiration time, the fresher everything will remain on your site, but at cost of slowdowns.


**Can I exclude given pages or posts from being cached?**

Yes, navigate to `Settings -› Easy cache -› Exclude pages/posts from caching` and select desired items. You can also include/exclude all search queries in your blog from being cached, it will be useful if you have huge amount of traffic, generated by searches to disable search queries exclusion

See also: `Settings -› Easy cache -› Exclude search queries from caching:` for further details.


**How to verify that Easy cache is working?** 

First of all, make sure you are **NOT** logged-in. Then navigate to your site like a normal visitor would. Right-click on any page (choose View Page Source), then scroll to the very bottom of the document. At the bottom, you'll find comments that show Easy cache statistics and information. You should also notice that page-to-page navigation is very fast, compared to what you experienced prior to installing Easy cache.


**Is there any further optimization of saved cache file for speed improvement?**

Yes, cached file can be minified (cleaned) and then saved to disk, in typical scenario this process will reduce cache file size between 6 and 12%, which also means, that your visitors will open pages 6 to 12% faster (with given equal conditions). 
Remark: minifying is applied to generated HTML file only, no other JavaScript or CSS minification is done.

See `Settings -› Easy cache -› Minify saved cache file` for details.


**What is the purpose of Statistics section?**

It gives you technical information about the caching mechanism and server load. This is useful if you want to track more precisely what's going on your server and on your website.


**Why should I donate to this plugin?**

Well, the main purpose of caching mechanism is to save your visitors time and bandwidth, which transfers of saving your money. By reducing the load times you actually economize your resources and provide your visitors better experience, so I won't mind if you buy me a beer if you are happy about that. Check the 'Donate' button for more info.

== Screenshots ==
None

== Changelog ==

= 0.1 =
Initial release.