=== Get Latest Tweets ===
Author URI: http://paulschreiber.com/
Plugin URI: http://paulschreiber.com/blog/2011/02/11/how-to-display-tweets-on-a-wordpress-page/
Contributors: paulschreiber
Tags: get_latest_tweets, tweet, tweets, twitter, formatting, list, shortcode
Requires at least: 3.0.4
Tested up to: 3.4.2
Stable tag: 0.1.3

Adds a shortcode tag [get_latest_tweets username='somename'] to display an excerpt from your latest blog post.

== Description ==

This plugin adds the ability to put a shortcode tag in any static page or post and have it display the latest tweets for a particular twitter user.

It generates markup like this, which you can style as desired:

    <ul class='tweets'>
    <li>@<a class='atreply' href='http://twitter.com/jane'>jane</a> Please dance a jig. <span class='date'><a href='http://twitter.com/me/status/2345'>3 hours ago</a></span></li>
    <li>Anyone used TotalFinder? <a href='http://t.co/blah'>http://t.co/blah</a> Saw it on @<a class='atreply' href='http://twitter.com/bob'>bob</a>'s machine and am intrigued. <span class='date'><a href='http://twitter.com/me/status/1234'>6 hours ago</a></span></li>
    </ul>

== Installation ==

1. Upload the plugin to the `/wp-content/plugins/` directory and unzip it.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Create 'wp-content/cache/latest_tweets/' and make sure it is writable by the web server.
1. Place `[get_latest_tweets username="somename"]` in your pages. 

== Changelog ==

= 0.1.3 =
* Fix "Tested up to" and make sure version numbers sync up

= 0.1.2 =
* Updated for new twitter API (thanks, @byjuhohn)

= 0.1.1 =
* Fixed tweet permalinks (credit: Wouter den Boer)

= 0.1 =
* Initial release.
