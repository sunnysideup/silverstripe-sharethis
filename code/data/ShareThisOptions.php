<?php

/**
 * @author nicolaas [at] sunnysideup.co.nz
 *
 */
class ShareThisOptions extends Object {

	private static $page_specific_data;

	private static $general_data;

	private static $share_all_data;

	private static $non_encoded_page_url;
	private static $encoded_page_url;
	private static $encoded_page_title;
	private static $encoded_page_title_space_encoded;
	private static $encoded_description;
	private static $icon;

	public static function get_all_options($title, $link, $description) {
		self::set_variables($title, $link, $description);
		self::$page_specific_data = array(
"email" => array(
	"url" => "mailto:?".htmlentities("Subject=".self::$encoded_page_title."&Body=".self::$encoded_description."%0D%0A".self::$encoded_page_url),
	"faicon" => "fa-send",
	"title" => "Email"),
"print" => array(
	"url" => "#",
	"faicon" => "fa-print",
	"click" => "window.print(); return false;",
	"title" => "Print"),
"favourites" => array(
	"url" => "#",
	"faicon" => "fa-bookmark",
	"click" => "sharethis.bookmark('".self::$encoded_page_url."', '".self::$encoded_page_title."'); return false;",
	"title" => "Add to favourites (Internet Explorer Only)"),
//"foursquare" => array(
//	"url" => "http://foursquare.com/home?status=".htmlentities(urlencode("currently reading: ").self::$encoded_page_url),
//	"faicon" => "fa-foursquare-square",
//	"title" => "FourSquareIt"),
"delicious" => array(
	"url" => "http://del.icio.us/post?".htmlentities("url=".self::$encoded_page_url."&title=".self::$encoded_page_title),
	"faicon" => "fa-delicious",
	"title" => "Add to Delicious"),
"facebook" => array(
	"url" => "http://www.facebook.com/share.php?".htmlentities("u=".self::$encoded_page_url."&title=".self::$encoded_page_title),
	"faicon" => "fa-facebook-square",
	"title" => "Share on Facebook"),
"googleplus" => array(
	"url" =>  "https://plus.google.com/share?url=".self::$encoded_page_url,
	"faicon" => "fa-google-plus",
	"title" => "Google Plus One"),
"linkedin" => array(
	"url" =>  "http://www.linkedin.com/shareArticle?".htmlentities("mini=true&url=".self::$encoded_page_url."&title=".self::$encoded_page_title."&source=".Director::absoluteBaseURL()),
	"faicon" => "fa-linkedin-square",
	"title" => "Share on LinkedIn"),
"pinterest" => array(
	"url" => "http://pinterest.com/pin/create/bookmarklet/?".htmlentities("media=html&url=".self::$encoded_page_url."&is_video=false&description=".self::$encoded_page_title),
	"faicon" => "fa-pinterest",
	"title" => "Pinterest it"),
"reddit" => array(
	"url" => "http://reddit.com/submit?".htmlentities("url=".self::$encoded_page_url."&title=".self::$encoded_page_title),
	"faicon" => "fa-reddit",
	"title" => "Reddit"),
"stumbleupon" => array(
	"url" => "http://www.stumbleupon.com/submit?".htmlentities("url=".self::$non_encoded_page_url."&title=".self::$encoded_page_title),
	"faicon" => "fa-stumbleupon",
	"title" => "Stumble It"),
"twitter" => array(
	"url" => "http://twitter.com/home?status=".htmlentities(urlencode("currently reading: ").self::$encoded_page_url),
	"faicon" => "fa-twitter-square",
	"title" => "Tweet It"),
"thumblr" => array(
	"url" => "http://www.tumblr.com/share/link?url=".htmlentities(self::$encoded_page_url."&name=".self::$encoded_page_title),
	"faicon" => "fa-tumblr-square",
	"title" => "Thumblr")
);
		return self::$page_specific_data;
	}

	public static function get_page_specific_data($title, $link, $description = '') {
		$originalArray = self::$page_specific_data ? self::$page_specific_data : self::get_all_options($title, $link, $description);
		$finalArray = array();
		$inc = Config::inst()->get("ShareThisSTE", "included_icons");
		$exc = Config::inst()->get("ShareThisSTE", "excluded_icons");
		if(count($inc)) {
			$new_array_of_icons_to_include = array();
			foreach($inc as $key => $value) {
				$new_array_of_icons_to_include[$value] = $value;
				if(! isset($originalArray[$value])) {
					debug::show("Error in ShareIcons::set_icons_to_include, $key does not exist in bookmark list");
				}
			}
			foreach($originalArray as $key => $array) {
				if(! isset($new_array_of_icons_to_include[$key])) {
					unset($originalArray[$key]);
				}
			}
		}
		//which ones do we exclude
		if(count($exc)) {
			foreach($exc as $key) {
				if(! isset($originalArray[$key])) {
					debug::show("Error in ShareIcons::set_icons_to_exclude, $key does not exist in bookmark list");
				}
				else {
					unset($originalArray[$key]);
				}
			}
		}
		if(! $link) {
			self::$page_specific_data = null;
		}
		return $originalArray;
	}

	/*
		summary: (required) utf-8 string, defaults to document.title
		content: (optional) utf-8 string, defaults to null
		updated: (optional) ISO 8601 date, defaults to document.lastModified
		published: (optional) ISO 8601 date, defaults to null
		author: currently not implemented
		category: currently not implemented
	*/
	public static function get_share_all() {
		//self::set_variables($title, $link, $description);
		self::$share_all_data = '
<script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#&amp;type=website"></script>
<script type="text/javascript">
SHARETHIS.addEntry(
	{
		title:"' . urldecode(self::$encoded_page_title) . '",
		summary:"' . urldecode(self::$encoded_page_title) . '",
		url:"' . urldecode(self::$encoded_page_url) . '",
		icon:"' . urldecode(self::$icon) . '"
	},
	{button:true}
);
</script>';
		return self::$share_all_data;
	}

	public static function set_general_data() {
		self::$general_data = null;
	}
	public static function get_general_data() {
		if(! self::$general_data) {
			$array = self::get_page_specific_data('', '', '');
			$newArray = array();
			if(count($array)) {
				foreach($array as $key => $subArray) {
					$newArray[$key] = $key;
				}
			}
			self::$general_data = $newArray;
		}
		return self::$general_data;
	}

	private static function set_variables($title, $link, $description) {
		self::$icon = urlencode(Director::absoluteBaseURL() . 'favicon.ico');
		self::$non_encoded_page_url = Director::absoluteURL($link);
		self::$encoded_page_url = urlencode(self::$non_encoded_page_url);
		self::$encoded_page_title = urlencode($title);
		self::$encoded_page_title_space_encoded = str_replace('+', '%20', urlencode($title));
		if($description) {
			self::$encoded_description = urlencode($description);
		}
		else {
			self::$encoded_description = self::$encoded_page_title;
		}
	}

	private function facebookLike() {
		//see http://developers.facebook.com/docs/reference/plugins/like/
		return '<div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#appId=231498950207168&amp;xfbml=1"></script><fb:like href="www.test.com" send="false" width="450" show_faces="true" font="lucida grande"></fb:like>';
	}

}
