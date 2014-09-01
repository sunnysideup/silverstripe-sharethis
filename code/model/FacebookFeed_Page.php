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
		'RSSURL' => 'Varchar(255)'
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
				"HowToFindRSS",
				"<p>
				The facebook RSS link format is like this https://www.facebook.com/feeds/page.php?format=rss20&id=XXX
				To find the id value, you can follow these steps :</p>
				<ol>
					<li>Open a new tab and open <a href=\"http://www.facebook.com\" target=\"_blank\">facebook</a></li>
					<li>Find your page (e.g. https://www.facebook.com/EOSAsia)</li>
					<li>Note the name (e.g. EOSAsia)</li>
					<li>Go to <a href=\"http://findmyfacebookid.com\" target=\"_blank\">http://findmyfacebookid.com</a></li>
					<li>Enter http://www.facebook.com/EOSAsia</li>
					<li>You'll get the answer (e.g. 357864420974239)</li>
					<li>Add this ID to the link - like this: https://www.facebook.com/feeds/page.php?format=rss20&id=357864420974239</li>
					<li>This is the link you need to add in the field above</li>
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

	public function Fetch($limit = 10){
		if($this->RSSURL) {
			$fb = new FacebookFeed_Item_Communicator();
			$fb->fetchFBFeed($this->RSSURL, $limit, $this->ID);
		}
	}

}

