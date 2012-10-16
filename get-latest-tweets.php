<?php
/*
Plugin Name: Get Latest Tweets
Plugin URI: http://paulschreiber.com/blog/2011/02/11/how-to-display-tweets-on-a-wordpress-page/
Description: Adds a shortcode tag [get_latest_tweets] to display an recent tweets
Version: 0.1.3
Author: Paul Schreiber
Author URI: http://paulschreiber.com/
*/

/*  Copyright 2011-12 Paul Schreiber <paul at paulschreiber.com>

    Released under the GPL, version 2.

		formatting code adapted from Twitter http://twitter.com/javascripts/widgets/widget.js 
*/

$gltCacheLiveTime = 30; // 24 seconds == 150 (rate limit per hour) / 60 (minutes)
$gltCachePath = WP_CONTENT_DIR . '/cache/latest_tweets/';

function time_ago($then) {
	$diff = time() - strtotime($then);

	$second = 1;
	$minute = $second * 60;
	$hour = $minute * 60;
	$day = $hour * 24;
	$week = $day * 7;
	
	if (is_nan($diff) || $diff < 0) {
	  return ""; // return blank string if unknown
	}

	if ($diff < $second * 2) {
	  // within 2 seconds
	  return "right now";
	}

	if ($diff < $minute) {
	  return floor($diff / $second) . " seconds ago";
	}

	if ($diff < $minute * 2) {
	  return "about 1 minute ago";
	}

	if ($diff < $hour) {
	  return floor($diff / $minute) . " minutes ago";
	}

	if ($diff < $hour * 2) {
	  return "about 1 hour ago";
	}

	if ($diff < $day) {
	  return  floor($diff / $hour) . " hours ago";
	}

	if ($diff > $day && $diff < $day * 2) {
	  return "yesterday";
	}

	if ($diff < $day * 365) {
	  return floor($diff / $day) . " days ago";
	}

  return "over a year ago";
}

function get_json_from_twitter($username, $count) {
	$url = "http://api.twitter.com/1/statuses/user_timeline.json?screen_name=$username";
	$json = file_get_contents($url);
	return $json;
}

function cache_file_name($username) {
	return $GLOBALS["gltCachePath"] . "/$username.json";
}

function cache_json($username, $count) {
	$cacheDirectory = dirname($GLOBALS["gltCachePath"]);
	
	if (!file_exists($cacheDirectory)) {
		if (!mkdir($cacheDirectory)) {
			die("Could not create cache directory. Make sure " . dirname($cacheDirectory) . " is writable by the web server." );
		}
	}
	
	if (!file_exists($GLOBALS["gltCachePath"])) {
		if (!mkdir($GLOBALS["gltCachePath"])) {
			die("Could not create cache directory. Make sure " . dirname($GLOBALS["gltCachePath"]) . " is writable by the web server." );
		}
	}
	
	$json = get_json_from_twitter($username, $count);
	return file_put_contents(cache_file_name($username), $json);
}

function read_cached_json($username) {
	return file_get_contents(cache_file_name($username));
}

function get_json($username, $count) {
	$cacheFile = cache_file_name($username);
	$staleCache = true;
	clearstatcache();
	if ((file_exists($cacheFile) && filesize($cacheFile))) {
		$cacheInfo = stat($cacheFile);
		$modTime = $cacheInfo[9];
		
		if ( (time() - $modTime) < $GLOBALS["gltCacheLiveTime"] ) {
			$staleCache = false;
		}
	}
	
	if ($staleCache) {
		if (!cache_json($username, $count)) {
			die("Could not write to JSON cache. Make sure " . $GLOBALS["gltCachePath"] . " is writeable by the web server");
		}
	}

	return read_cached_json($username);
}

function format_tweet($tweet) {
	// add @reply links
	$tweet_text = preg_replace("/\B[@＠]([a-zA-Z0-9_]{1,20})/",
	          								"@<a class='atreply' href='http://twitter.com/$1'>$1</a>",
	        									$tweet);

	// make other links clickable
	$matches = array();
	$link_info = preg_match_all("/\b(((https*\:\/\/)|www\.)[^\"\']+?)(([!?,.\)]+)?(\s|$))/", $tweet_text, $matches, PREG_SET_ORDER);

	if ($link_info) {
		foreach ($matches as $match) {
			$http = preg_match("/w/", $match[2]) ? 'http://' : '';
			$tweet_text = str_replace($match[0],
						"<a href='" . $http . $match[1] . "'>" . $match[1] . "</a>" . $match[4],
						$tweet_text);
		}
	}
	
	return $tweet_text;
}


function get_latest_tweets_html($attributes) {
	$data = extract( shortcode_atts( array(
			'username' => null,
			'count' => 5,
		), $attributes ) );
		
	$count = intval($count);
	if ($count < 1 or $count > 100) {
		return "Numbers of tweets must be between 1 and 100.";
	}
	
	if (!$username) {
		return "Please specify a twitter username";
	}

	$json = get_json($username, $count);
	$tweetData = json_decode($json, true);
	
	$content = "<ul class='tweets'>\n";
	foreach ($tweetData as $index => $tweet) {
		if ($index == $count) break;
		$content .= "<li>" . format_tweet($tweet["text"]) . " <span class='date'><a href='http://twitter.com/" . $username . "/status/" . $tweet["id_str"] . "'>" . time_ago($tweet["created_at"]) . "</a></span></li>\n";
	}
	$content .= "</ul>\n";


	return $content;
}

add_shortcode('get_latest_tweets', 'get_latest_tweets_html');
?>
