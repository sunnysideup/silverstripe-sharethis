<?php

/**
 * Add a field to each SiteTree object and it's subclasses to enable Share icons.
 * @author nicolaas [at] sunnysideup.co.nz
 * @inspiration: Silverstripe Original Module - full credits to them.  We made our own to improve their module
 * @todo fix populateDefaults to make sure SiteConfig table is built first
 */
class ShareThisSTE extends SiteTreeExtension {

	/**
	* use BW icons
	* @var boolean
	*/
	static $use_bw_effect = false;

	/**
	* specify icons to be included, if left empty, this variable will be ignored
	* We have this variable so that you can setup a bunch of default icons
	* @var array
	*/

	protected static $included_icons = array();
		static function set_included_icons(array $array) {self::$included_icons = $array;}
		static function get_included_icons() {return self::$included_icons;}

	/**
	* specify icons to be excluded, if left empty, this variable will be ignored
	* We have this variable so that you can setup a bunch of default icons
	* @var array
	*/

	protected static $excluded_icons = array();
		static function set_excluded_icons(array $array) {self::$excluded_icons = $array;}
		static function get_excluded_icons() {return self::$excluded_icons;}

	/**
	 * standard SS method
	 * @var Array
	 **/
	static $db = array(
		'ShareIcons' => 'Boolean'
	);

	function updateCMSFields(FieldList $fields) {
		$config = $this->owner->getSiteConfig();
		if(! $config->AlwaysIncludeShareThisLinks) {
			$fields->addFieldToTab('Root.SocialMedia', new HeaderField('ShareThisHeader', 'Allow users to share this page'));
			$fields->addFieldToTab('Root.SocialMedia', new CheckboxField('ShareIcons', 'Show Share Icons on this page', $config->IncludeByDefault));
		}
		$fields->addFieldToTab('Root.SocialMedia', new LiteralField('LinkToSiteConfigSocialMedia', "<p>Note: make sure to review the social media settings in the <a href=\"{$config->CMSEditLink()}\">Site Config</a>.</p>"));
		$list = ShareThisOptions::get_all_options($this->owner->Title, $this->owner->Link(), $this->owner->MetaDescription);
		$fields->addFieldToTab('Root.SocialMedia', new HeaderField('ShareThisNow', 'Share this page on your favourite social media sites...'));
		$html = "<div><p>Click on any of the icons below to share the '<i>{$this->owner->Title}</i>' page. Any click will open a new tab/window where you will need to enter your login details.</p>";
		foreach($list as $key => $innerArray) {
			if(! isset($innerArray['click'])) {
				$html .= "<span><a href=\"{$innerArray['url']}\" target=\"_blank\" style=\"whitespace: nowrap; display: inline-block;\"><img src=\"" . SS_SHARETHIS_DIR . "/images/icons/$key.png\" alt=\"$key\"/>{$innerArray['title']}</a></span>&nbsp;&nbsp;";
			}
		}
		$html .= '</div>';
		$fields->addFieldToTab('Root.SocialMedia', new LiteralField('ShareNow', $html));
		return $fields;
	}

	function ShowShareIcons() {
		$config = $this->owner->getSiteConfig();
		if($config->AlwaysIncludeShareThisLinks) {
			return true;
		}
		return $this->owner->ShareIcons;
	}

	function ShareIcons() {
		$bookmarks = $this->makeBookmarks('IncludeThisIcon');
		return $this->makeShareIcons($bookmarks);
	}

	function ShareAllExpandedList() {
		Requirements::javascript(SS_SHARETHIS_DIR . '/javascript/ShareAllExpandedList.js');
		$bookmarks = $this->makeBookmarks('IncludeThisIconInExtendedList');
		return $this->makeShareIcons($bookmarks);
	}

	function IncludeShareAll() {
		$config = $this->owner->getSiteConfig();
		return $config->ShareThisAllInOne;
	}

	function ShareAll() {
		if($this->IncludeShareAll()) {
			return ShareThisOptions::get_share_all();
		}
	}

	/**
	 * eturns array
	 */
	protected function makeShareIcons($bookmarks) {
		$icons = array();
		if($bookmarks) {
			Requirements::themedCSS('SocialNetworking', "sharethis"); // ALSO  added in template
			Requirements::javascript(SS_SHARETHIS_DIR . '/javascript/shareThis.js');
			if(self::$use_bw_effect) {
				Requirements::customScript('sharethis.set_use_BW(true);', 'ShareThisBWEffect');
			}
			foreach($bookmarks as $key => $bookmark) {
				if(isset($bookmark['title']) && isset($bookmark['url'])) {
					$icon = array(
						'Title' => $bookmark['title'],
						'URL' => $bookmark['url'],
						'Key' => $key,
						'ImageSource' => "sharethis/images/icons/$key.png",
						'UseStandardImage' => true
					);
					if(isset($bookmark['click'])) {
						$icon['OnClick'] = $bookmark['click'];
					}
					if(isset($bookmark['icon'])) {
						$icon['ImageSource'] = $bookmark['icon'];
						$icon['UseStandardImage'] = false;
					}
					$icon['ImageSourceOver'] = str_replace(array('.png', '.gif', '.jpg'), array('_over.png', '_over.gif', '_over.jpg'), $icon['ImageSource']);
					$icons[] = new ArrayData($icon);
				}
				else {
					debug::show("Title of url not defined for $key");
				}
			}
		}
		return new ArrayList($icons);
	}

	protected function makeBookmarks($field) {
		$finalBookmarks = array();
		$bookmarks = ShareThisOptions::get_page_specific_data($this->owner->Title, $this->owner->Link(), $this->owner->MetaDescription);
		$objects = ShareThisDataObject::get()->filter($field, 1)->sort(array('Sort' => 'ASC', 'Title' => 'ASC'));
		if($objects->exists()) {
			foreach($objects as $obj) {
				if(isset($bookmarks[$obj->Title])) {
					$finalBookmarks[$obj->Title] = $bookmarks[$obj->Title];
					if($obj->AlternativeIconID && $obj->AlternativeIcon()->exists()) {
						$finalBookmarks[$obj->Title]['icon'] = $obj->AlternativeIcon()->Link();
					}
				}
			}
		}
		else {
			$finalBookmarks = $bookmarks;
		}
		return $finalBookmarks;
	}

	function populateDefaults() {
		//$config = $this->owner->getSiteConfig();
		//$this->owner->HasSocialNetworkingLinks = $config->IncludeByDefaultShareThisLinks;
	}
}
