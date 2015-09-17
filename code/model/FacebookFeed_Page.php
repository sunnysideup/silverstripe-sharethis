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



class FacebookFeed_Page extends DataObject  {

	private static $db = array(
		"Title" => "Varchar(244)",
		'FacebookPageID' => 'Varchar(40)'
	);

	private static $has_many = array(
		'Items' => 'FacebookFeed_Item'
	);

	private static $many_many = array(
		'Pages' => 'SiteTree'
	);

	function canCreate($member = null) {
		return Permission::checkMember($member, 'SOCIAL_MEDIA');
	}

	function canView($member = null) {
		return Permission::checkMember($member, 'SOCIAL_MEDIA');
	}

	function canEdit($member = null) {
		return Permission::checkMember($member, 'SOCIAL_MEDIA');
	}

	function canDelete($member = null) {
		return Permission::checkMember($member, 'SOCIAL_MEDIA');
	}

	public function getCMSFields(){
		$fields = parent::getCMSFields();
		$fields->addFieldToTab(
			"Root.Main",
			new LiteralField(
				"HowToFindPageID",
				"<p>
				To find the Facebook Page ID value, you can follow these steps :</p>
				<ol>
					<li>Open a new tab and open <a href=\"http://www.facebook.com\" target=\"_blank\">facebook</a></li>
					<li>Find your page (e.g. https://www.facebook.com/EOSAsia)</li>
					<li>Note the name (e.g. EOSAsia)</li>
					<li>Go to <a href=\"http://findmyfacebookid.com\" target=\"_blank\">http://findmyfacebookid.com</a></li>
					<li>Enter http://www.facebook.com/EOSAsia</li>
					<li>You'll get the answer (e.g. 357864420974239)</li>
				</ol>"
			)
		);
		$fields->addFieldToTab(
			"Root.Pages",
			new TreeMultiSelectField("Pages", "Show on", "SiteTree")
		);
		$pages = $this->Pages();
		if($pages && $pages->count()) {
			$links = array();
			foreach($pages as $page) {
				$links[] = "<li><a href=\"".$page->Link("updatefb")."\">".$page->Title."</a></li>";
			}
			if(count($links)) {
				$fields->addFieldToTab(
					"Root.Pages",
					new LiteralField(
						"LinksToCheck",
						"<p>
							Choose the links below to view your facebook feed:
						<ol>
							".implode("", $links)."
						</ol>"
					)
				);
			}
		}
		return $fields;
	}

	/**
	 *
	 * @param SiteTree | Int $page - page or page id
	 * @param Int $limit
	 *
	 */
	public static function all_for_one_page($page, $limit = 10){
		if($page instanceOf SiteTree) {
			$pageID = $page->ID;
		}
		else {
			$pageID = $page;
		}
		$feedIDs = array();
		$sql = "
			SELECT \"FacebookFeed_Page_Pages\".\"FacebookFeed_PageID\"
			FROM \"FacebookFeed_Page_Pages\"
			WHERE \"FacebookFeed_Page_Pages\".\"SiteTreeID\" = $pageID";
		$rows = DB::query($sql);
		if($rows) {
			foreach($rows as $row) {
				$feedIDs[$row["FacebookFeed_PageID"]] = $row["FacebookFeed_PageID"];
			}
		}
		if(count($feedIDs)) {
			return FacebookFeed_Item::get()->filter(
				array(
					"FacebookFeed_PageID" => $feedIDs,
					"Hide" => 0
				)
			)
			->limit($limit);
		}
	}

	public function ShowableItems($limit = 10){
		return $this->getComponents('Items', 'Hide = 0', null, '', $limit);
	}

	public function Fetch($verbose = false){
		$count = 0;
		if($this->FacebookPageID) {
			$items = SilverstripeFacebookConnector::get_feed($this->FacebookPageID);
			if($items) {
				foreach($items as $item) {
					if(!FacebookFeed_Item::get()->filter(array("UID" => $item["id"]))->first()) {
						$count++;
						$message = isset($item["message"]) ? $item["message"] : "";
						//Converts UTF-8 into ISO-8859-1 to solve special symbols issues
						$message = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $message);
						$message = $this->stripUnsafe($message);
						//Get status update time
						$pubDate = strtotime(isset($item["created_time"]) ? $item["created_time"] : "today");
						$convertedDate = gmdate($timeFormat = 'Y-m-d', $pubDate);  //Customize this to your liking
						//Get link to update
						//Store values in array
						$obj = new FacebookFeed_Item();
						$obj->UID = $item["id"];
						$obj->Title = (string) (isset($item["name"]) ? $item["name"] : "");
						$obj->Date = $convertedDate;
						$obj->Author = (string) (isset($item["from"]["name"]) ? $item["from"]["name"] : "");
						$obj->Link = (string) (isset($item["link"]) ? $item["link"] : "");
						$obj->PictureLink = (string) (isset($item["full_picture"]) ? $item["full_picture"] : "");
						$obj->Description = $message;
						$obj->FacebookFeed_PageID = $this->ID;
						$obj->write();
					}
				}
			}
			else {
				if($verbose) {
					DB::alteration_message("ERROR: no data returned", "deleted");
				}
			}
			if($count == 0 && $verbose) {
				DB::alteration_message("Nothing to add.");
			}
		}
		else {
			if($verbose) {
				DB::alteration_message("ERROR: no Facebook Page ID provided", "deleted");
			}
		}
		if($count && $verbose) {
			DB::alteration_message("Added $count items", "created");
		}
	}

	function stripUnsafe($string) {
    // Unsafe HTML tags that members may abuse
		$unsafe=array(
			'/onmouseover="(.*?)"/is',
			'/onclick="(.*?)"/is',
			'/style="(.*?)"/is',
			'/target="(.*?)"/is',
			'/onunload="(.*?)"/is',
			'/rel="(.*?)"/is',
			'/<a(.*?)>/is',
			'/<\/a>/is'
		);
		$string= preg_replace($unsafe, " ", $string);
		return $string;
	}


}

