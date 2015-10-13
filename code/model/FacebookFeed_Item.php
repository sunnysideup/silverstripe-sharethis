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
		"Title" => "varchar(255)",
		"KeepOnTop" => "Boolean",
		"Hide" => "Boolean",
		"UID" => "varchar(32)",
		"Author" => "Varchar(244)",
		"Description" => "HTMLText",
		"DescriptionWithShortLink" => "HTMLText",
		"Link" => "Varchar(244)",
		"PictureLink" => "Text"
	);


	private static $summary_fields = array(
		"Created.Nice" => "Created",
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
		'KeepOnTopNice' => 'Varchar',
		'HideNice' => 'Varchar',
		'FacebookPostLink' => 'Varchar'
	);

	private static $searchable_fields = array(
		'Title' => 'PartialMatchFilter',
		'Author' => 'PartialMatchFilter',
		'Description' => 'PartialMatchFilter',
		'Hide' => true,
		'KeepOnTop' => true
	);

	private static $default_sort = "\"Created\" DESC";

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

	public function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->DescriptionWithShortLink = $this->Description;
		//$this->DescriptionWithShortLink = $this->createDescriptionWithShortLinks();
	}

	protected function createDescriptionWithShortLinks() {
		require_once(Director::baseFolder()."/".SS_SHARETHIS_DIR.'/code/api/thirdparty/simple_html_dom.php');
		$html = str_get_html($this->Description);
		if($html) {
			foreach($html->find('text') as $element) {
				//what exactly does it do?
				if(! in_array($element->parent()->tag, array('a', 'img'))) {
					$element->innertext = $this->replaceLinksWithProperOnes($element->innertext);
				}
			}
		}
		else {
			$this->Hide = true;
			$this->write();
		}
	}

	protected function replaceLinksWithProperOnes($text) {
		$rexProtocol = '(https?://)?';
		$rexDomain   = '((?:[-a-zA-Z0-9]{1,63}\.)+[-a-zA-Z0-9]{2,63}|(?:[0-9]{1,3}\.){3}[0-9]{1,3})';
		$rexPort     = '(:[0-9]{1,5})?';
		$rexPath     = '(/[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]*?)?';
		$rexQuery    = '(\?[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
		$rexFragment = '(#[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
		$outcome  = "";
		$validTlds = array_fill_keys(explode(" ", ".aero .asia .biz .cat .com .coop .edu .gov .info .int .jobs .mil .mobi .museum .name .net .org .pro .tel .travel .ac .ad .ae .af .ag .ai .al .am .an .ao .aq .ar .as .at .au .aw .ax .az .ba .bb .bd .be .bf .bg .bh .bi .bj .bm .bn .bo .br .bs .bt .bv .bw .by .bz .ca .cc .cd .cf .cg .ch .ci .ck .cl .cm .cn .co .cr .cu .cv .cx .cy .cz .de .dj .dk .dm .do .dz .ec .ee .eg .er .es .et .eu .fi .fj .fk .fm .fo .fr .ga .gb .gd .ge .gf .gg .gh .gi .gl .gm .gn .gp .gq .gr .gs .gt .gu .gw .gy .hk .hm .hn .hr .ht .hu .id .ie .il .im .in .io .iq .ir .is .it .je .jm .jo .jp .ke .kg .kh .ki .km .kn .kp .kr .kw .ky .kz .la .lb .lc .li .lk .lr .ls .lt .lu .lv .ly .ma .mc .md .me .mg .mh .mk .ml .mm .mn .mo .mp .mq .mr .ms .mt .mu .mv .mw .mx .my .mz .na .nc .ne .nf .ng .ni .nl .no .np .nr .nu .nz .om .pa .pe .pf .pg .ph .pk .pl .pm .pn .pr .ps .pt .pw .py .qa .re .ro .rs .ru .rw .sa .sb .sc .sd .se .sg .sh .si .sj .sk .sl .sm .sn .so .sr .st .su .sv .sy .sz .tc .td .tf .tg .th .tj .tk .tl .tm .tn .to .tp .tr .tt .tv .tw .tz .ua .ug .uk .us .uy .uz .va .vc .ve .vg .vi .vn .vu .wf .ws .ye .yt .yu .za .zm .zw .xn--0zwm56d .xn--11b5bs3a9aj6g .xn--80akhbyknj4f .xn--9t4b11yi5a .xn--deba0ad .xn--g6w251d .xn--hgbk6aj7f53bba .xn--hlcj6aya9esc7a .xn--jxalpdlp .xn--kgbechtv .xn--zckzah .arpa"), true);

		$position = 0;
		while (preg_match("{\\b$rexProtocol$rexDomain$rexPort$rexPath$rexQuery$rexFragment(?=[?.!,;:\"]?(\s|$))}", $text, $match, PREG_OFFSET_CAPTURE, $position))
		{
				list($url, $urlPosition) = $match[0];

				// Print the text leading up to the URL.
				$outcome .= (htmlspecialchars(substr($text, $position, $urlPosition - $position)));

				$domain = $match[2][0];
				$port   = $match[3][0];
				$path   = $match[4][0];

				// Check if the TLD is valid - or that $domain is an IP address.
				$tld = strtolower(strrchr($domain, '.'));
				if (preg_match('{\.[0-9]{1,3}}', $tld) || isset($validTlds[$tld]))
				{
						// Prepend http:// if no protocol specified
						$completeUrl = $match[1][0] ? $url : "http://$url";

						// Print the hyperlink.
						$outcome .= sprintf('<a href="%s">%s</a>', htmlspecialchars($completeUrl), htmlspecialchars("$domain$port$path"));
				}
				else
				{
						// Not a valid URL.
						$outcome .= (htmlspecialchars($url));
				}

				// Continue text parsing from after the URL.
				$position = $urlPosition + strlen($url);
		}

		// Print the remainder of the text.
		$outcome .= ((substr($text, $position)));
		return $outcome;
	}

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeByName("UID");
		$fields->removeByName("PictureLink");
		if($this->PictureLink) {
			$fields->addFieldToTab("Root.Main", new LiteralField("PictureLinkIMG", "<img src=\"".$this->PictureLink."\" alt=\"\" />"), "Author");
		}
		if($this->Link) {
			$fields->addFieldToTab("Root.Main", new LiteralField("LinkLink", "<h2><a href=\"".$this->Link."\" >go to link: ".substr($this->Link,0, 45)."...</a></h2>"), "Author");
			$fields->addFieldToTab("Root.Main", new LiteralField("LinkLink", "<h2><a href=\"".$this->Link."\" >go to link: ".substr($this->Link,0, 45)."...</a></h2>"), "Author");
			$fields->addFieldToTab("Root.RawData", new TextField("Link", "Link"));
		}
		if($this->Description) {
			$fields->addFieldToTab("Root.RawData", new HtmlEditorField("Description"));
			$fields->addFieldToTab("Root.Main", new HtmlEditorField("DescriptionWithShortLink", "Edited Link"));
		}
		return $fields;
	}

	function KeepOnTopNice(){
		return $this->dbObject('KeepOnTop')->Nice();
	}

	function HideNice(){
		return $this->dbObject('Hide')->Nice();
	}
	
	/** 
	 * @return string
	 */
	function getFacebookPostLink(){
		return "https://facebook.com/" . $this->UID;
	}

}


