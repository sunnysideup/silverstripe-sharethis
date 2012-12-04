<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *
 **/


class ShareThisOptions extends Object {

	protected static $page_specific_data= null;

	protected static $general_data = null;
		static function set_general_data($v) {self::$general_data = $v;}

	protected static $share_all_data = null;

	protected static $non_encoded_page_url;
	protected static $encoded_page_url;
	protected static $encoded_page_title;
	protected static $encoded_page_title_space_encoded;
	protected static $encoded_description;
	protected static $icon;

	public static function get_all_options($title, $link, $description) {
		self::set_variables($title, $link, $description);
		self::$page_specific_data = array(

"email" => array(
	 "url" => "mailto:?".htmlentities("Subject=".self::$encoded_page_title."&Body=".self::$encoded_description."%0D%0A".self::$encoded_page_url),
	 "title" => "Email"),
"print" => array(
	 "url" => "#",
	 "click" => "window.print(); return false;",
	 "title" => "Print"),
"favourites" => array(
	 "url" => "#",
	 "click" => "sharethis.bookmark('".self::$encoded_page_url."', '".self::$encoded_page_title."'); return false;",
	 "title" => "Add to favourites (Internet Explorer Only)"),
"ask" => array(
	 "url" => "http://mystuff.ask.com/mysearch/BookmarkIt?".htmlentities("v=1.2&t=webpages&url=".self::$encoded_page_url."&title=".self::$encoded_page_title."&abstext=".self::$encoded_description),
	 "title" => "Share on Ask"),
"bebo" => array(
	 "url" => "http://www.bebo.com/c/share?".htmlentities("Url=".self::$encoded_page_url."&Title=".self::$encoded_page_title),
	 "title" => "Stumble It"),
"blinklist" => array(
	 "url" => "http://blinklist.com/blink?".htmlentities("u=".self::$encoded_page_url."&t=".self::$encoded_page_title."&d=".self::$encoded_description),
	 "title" => "Share on BlinkList"),
"blogmarks" => array(
	 "url" => "http://blogmarks.net/my/new.php?".htmlentities("mini=1&simple=1&url=".self::$encoded_page_url."&title=".self::$encoded_page_title),
	 "title" => "BlogMark It"),
"delicious" => array(
	 "url" => "http://delicious.com/save?".htmlentities("url=".self::$encoded_page_url."&title=".self::$encoded_page_title),
	 "title" => "Add to Delicious"),
"digg" => array(
	 "url" => "http://digg.com/submit?".htmlentities("url=".self::$non_encoded_page_url."&title=".self::$encoded_page_title),
	 "title" => "Digg this"),
"dzone" => array(
	 "url" => "http://www.dzone.com/links/add.html?".htmlentities("url=".self::$encoded_page_url."&title=".self::$encoded_page_title),
	 "title" => "Add to DZone"),
"facebook" => array(
	 "url" => "http://www.facebook.com/sharer.php?".htmlentities("u=".self::$encoded_page_url."&t=".self::$encoded_page_title),
	 "title" => "Share on Facebook"),
"fark" => array(
	 "url" => "http://cgi.fark.com/cgi/fark/submit.pl?".htmlentities("new_url=".self::$encoded_page_url),
	 "title" => "Fark It"),
"friendfeed" => array(
	 "url" => "http://friendfeed.com/share/bookmarklet/frame#".htmlentities("url=".self::$encoded_page_url."&title=".self::$encoded_page_title_space_encoded),
	 "title" => "Furl this"),
"furl" => array(
	 "url" => "http://www.furl.net/storeIt.jsp?".htmlentities("u=".self::$encoded_page_url."&t=".self::$encoded_page_title),
	 "title" => "Furl this"),
"google" => array(
	 "url" =>  "http://www.google.com/bookmarks/mark?".htmlentities("op=edit&output=popup&bkmk=".self::$encoded_page_url."&title=".self::$encoded_page_title."&annotation=".self::$encoded_description),
	 "title" => "Googlize this post"),
"kaboodle" => array(
	 "url" =>  "http://www.kaboodle.com/za/additem?".htmlentities("get=1&url=".self::$encoded_page_url."&title=".self::$encoded_page_title),
	 "title" => "Share on Kadooble"),
"linkedin" => array(
	 "url" =>  "http://www.linkedin.com/shareArticle?".htmlentities("mini=true&url=".self::$encoded_page_url."&title=".self::$encoded_page_title."&source=".Director::absoluteBaseURL()),
	 "title" => "Share on LinkedIn"),
"live" => array(
	 "url" => "https://favorites.live.com/quickadd.aspx?".htmlentities("url=".self::$encoded_page_url."&title=".self::$encoded_page_title_space_encoded),
	 "title" => "Add to Windows Live"),
"ma.gnolia" => array(
	 "url" => "http://ma.gnolia.com/bookmarklet/add?".htmlentities("url=".self::$encoded_page_url."&title=".self::$encoded_page_title),
	 "title" => "Add to ma.gnolia"),
"misterwong" => array(
	 "url" => "http://www.mister-wong.com/addurl/?".htmlentities("bm_url=".self::$encoded_page_url."&bm_description=".self::$encoded_page_title_space_encoded),
	 "title" => "Wong It"),
"myspace" => array(
	 "url" => "http://www.myspace.com/Modules/PostTo/Pages/?".htmlentities("u=".self::$encoded_page_url.'&t='.self::$encoded_page_title),
	 "title" => "Share on MySpace"),
"netvouz" => array(
	 "url" => "http://www.netvouz.com/action/submitBookmark?".htmlentities("url=".self::$encoded_page_url.'&title='.self::$encoded_page_title),
	 "title" => "Add to NetVouz"),
"newsvine" => array(
	 "url" => "http://www.newsvine.com/_tools/seed".htmlentities("&save?u=".self::$encoded_page_url."&h=".self::$encoded_page_title),
	 "title" => "Seed Newsvine"),
"reddit" => array(
	 "url" => "http://reddit.com/submit?".htmlentities("url=".self::$encoded_page_url."&title=".self::$encoded_page_title),
	 "title" => "Reddit"),
"simpy" => array(
	 "url" => "http://simpy.com/simpy/LinkAdd.do?".htmlentities("href=".self::$encoded_page_url."&title=".self::$encoded_page_title),
	 "title" => "Add to Simpy"),
"slashdot" => array(
	 "url" => "http://slashdot.org/bookmark.pl?".htmlentities("url=".self::$encoded_page_url."&title=".self::$encoded_page_title),
	 "title" => "Slashdot It"),
"spurl" => array(
	 "url" => "http://www.spurl.net/spurl.php?".htmlentities("url=".self::$encoded_page_url."&title=".self::$encoded_page_title),
	 "title" => "Spurl It"),
"squidoo" => array(
	 "url" => "http://www.squidoo.com/lensmaster/bookmark?".htmlentities(self::$encoded_page_url),
	 "title" => "Add to Squidoo"),
"stumbleupon" => array(
	 "url" => "http://www.stumbleupon.com/submit?".htmlentities("url=".self::$non_encoded_page_url."&title=".self::$encoded_page_title),
	 "title" => "Stumble It"),
"stylehive" => array(
	 "url" => "http://www.stylehive.com/savebookmark/index.htm?".htmlentities("url=".self::$encoded_page_url),
	 "title" => "Add to Stylehive"),
"technorati" => array(
	 "url" => "http://technorati.com/faves?".htmlentities("add=".self::$encoded_page_url),
	 "title" => "Add to Technorati"),
"twitter" => array(
		"url" => "http://twitter.com/home?status=".htmlentities(urlencode("currently reading: ").self::$encoded_page_url),
		"title" => "Tweet It"),
"yahoo" => array(
	 "url" =>  "http://bookmarks.yahoo.com/toolbar/savebm?u=".htmlentities("u=".self::$encoded_page_url."&t=".self::$encoded_page_title),
	 "title" => "Bookmark it on Yahoo"),
"socialmarker" => array(
	 "url" => "http://www.socialmarker.com/?".htmlentities("link=".self::$encoded_page_url."&title=".self::$encoded_page_title),
	 "title" => "Bookmark Elsewhere")


			);
		return self::$page_specific_data;
	}

	static function get_page_specific_data($title, $link, $description = "") {
		if(!self::$page_specific_data) {
			$originalArray = self::get_all_options($title, $link, $description);
		}
		else {
			$originalArray = self::$page_specific_data;
		}
		$finalArray = array();
		$inc = ShareThis::get_share_this_icons_to_include();
		$exc = ShareThis::get_share_this_icons_to_exclude();
		if(count($inc)) {
			$new_array_of_icons_to_include = array();
			foreach($inc as $key => $value) {
				$new_array_of_icons_to_include[$value] = $value;
				if(!isset($originalArray[$value])) {
					debug::show("Error in ShareIcons::set_icons_to_include, $key does not exist in bookmark list");
				}
			}
			foreach($originalArray as $key => $array) {
				if(!isset($new_array_of_icons_to_include[$key])) {
					unset($originalArray[$key]);
				}
			}
		}
		//which ones do we exclude
		if(count($exc)) {
			foreach($exc as $key) {
				if(!isset($originalArray[$key])) {
					debug::show("Error in ShareIcons::set_icons_to_exclude, $key does not exist in bookmark list");
				}
				else {
					unset($originalArray[$key]);
				}
			}
		}
		if(!$link) {
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
		title:"'.urldecode(self::$encoded_page_title).'",
		summary:"'.urldecode(self::$encoded_page_title).'",
		url:"'.urldecode(self::$encoded_page_url).'",
		icon:"'.urldecode(self::$icon).'"
	},
	{button:true}
);
</script>';
		return self::$share_all_data;
	}

	public static function get_general_data(){
		if(!self::$general_data) {
			$array = self::get_page_specific_data("", "", "");
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
		self::$icon = urlencode(Director::absoluteBaseURL().'favicon.ico');
		self::$non_encoded_page_url = Director::absoluteURL($link);
		self::$encoded_page_url = urlencode(self::$non_encoded_page_url);
		self::$encoded_page_title = urlencode($title);
		self::$encoded_page_title_space_encoded = str_replace("+", "%20",urlencode($title));
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
