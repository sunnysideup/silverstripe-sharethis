<?php
/**
 * FROM: http://www.acornartwork.com/blog/2010/04/19/tutorial-facebook-rss-feed-parser-in-pure-php/
 * EXAMPLE:
 *		//Run the function with the url and a number as arguments
 *		$fb = new TheFaceBook_communicator();
 *		$dos = $fb->fetchFBFeed('http://facebook.com/feeds/status.php?id=xxxxxx&viewer=xxxxxx&key=xxxxx&format=rss20', 3);
 *		//Print Facebook status updates
 *		echo '<ul class="fb-updates">';
 *			 foreach ($dos as $do) {
 *					echo '<li>';
 *					echo '<span class="update">' .$do->Description. '</span>';
 *					echo '<span class="date">' .$do->Date. '</span>';
 *					echo '<span class="link"><a href="' .$do->Link. '">more</a></span>';
 *					echo '</li>';
 *			 }
 *		echo '</ul>';
 *
 *  SEE README on getting facebook URL for RSS Feed.
 *
 *
 **/

class FacebookFeed_Item extends DataObject{

	private static $db = array(
		"KeepOnTop" => "Boolean",
		"Hide" => "Boolean",
		"UID" => "varchar(32)",
		"Title" => "varchar(255)",
		"Author" => "Varchar(244)",
		"Description" => "HTMLText",
		"Link" => "Varchar(244)",
		"Date" => "Date"
	);


	private static $summary_fields = array(
		"FacebookFeed_Page.Title" => "Feed",
		"Title" => "Title",
		"KeepOnTopNice" => "Keep on top",
		"HideNice" => "Hide",
	);


	private static $has_one = array(
		"FacebookFeed_Page" => "FacebookFeed_Page"
	);

	private static $indexes = array(
		"UID" => true
	);

	private static $casting = array(
		'DescriptionWithShortLinks' => 'HTMLText',
		'KeepOnTopNice' => 'Varchar',
		'HideNice' => 'Varchar'
	);

	private static $searchable_fields = array(
		'Title' => 'PartialMatchFilter',
		'Author' => 'PartialMatchFilter',
		'Description' => 'PartialMatchFilter',
		'Hide' => true,
		'KeepOnTop' => true
	);


	function canCreate($member = null) {
		return false;
	}

	function canView($member = null) {
		return Permission::checkMember($member, 'SOCIAL_MEDIA');
	}

	function canEdit($member = null) {
		return Permission::checkMember($member, 'SOCIAL_MEDIA');
	}

	function canDelete($member = null) {
		return false;
	}

	private static $singular_name = "Facebook Item";
		function i18n_singular_name() { return "Facebook Item";}

	private static $plural_name = "Facebook Items";
		function i18n_plural_name() { return "Facebook Items";}

	private static $default_sort = "\"Hide\" ASC, \"KeepOnTop\" DESC, \"Date\" DESC";

	function DescriptionWithShortLinks() {
		require_once(Director::baseFolder()."/".SS_SHARETHIS_DIR.'/code/api/thirdparty/simple_html_dom.php');
		$html = str_get_html($this->Description);

		foreach($html->find('text') as $element) {
			//what exactly does it do?
			if(! in_array($element->parent()->tag, array('a', 'img'))) {
				$element->innertext = preg_replace("#(www(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie", "'http://$1$4'", $element->innertext);
				$element->innertext = preg_replace("#((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie", "'<a href=\"$1\" target=\"_blank\">click here</a>$4'", $element->innertext);
			}
		}
		$dom = new DOMDocument();
		@$dom->loadHTML($html);
		if($dom) {
			$dom->preserveWhiteSpace = false;
			$images = $dom->getElementsByTagName('img');
			foreach ($images as $image) {
				$link = $dom->createElement('a');
				$link->setAttribute('href', $this->Link);
				$image->parentNode->replaceChild($link, $image);
				$link->appendChild($image);
			}
		}
		return $dom->saveHTML();
	}

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeByName("UID");
		return $fields;
	}

	function KeepOnTopNice(){
		return $this->dbObject('KeepOnTop')->Nice();
	}

	function HideNice(){
		return $this->dbObject('Hide')->Nice();
	}

}


