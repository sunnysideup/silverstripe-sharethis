<?php

/**
 *
 *@author nicolaas[at]sunnysideup.co.nz
 *@description: creates a list of places where people can follow you (e.g. twitter, your blog, etc...)
 *
 */
class SocialNetworkingLinksDataObject extends DataObject {

	private static $db = array(
		'URL' => 'Varchar(255)',
		'Title' => 'Varchar(255)',
		'Sort' => 'Int'
	);

	private static $casting = array(
		'Code' => 'Varchar(255)',
		'Link' => 'Varchar(255)',
		'IconHTML' => 'HTMLText'
	);

	private static $has_one = array(
		'Icon' => 'Image',
		'InternalLink' => 'Page'
	);

	private static $searchable_fields = array(
		'Title' => 'PartialMatchFilter'
	);

	private static $field_labels = array(
		'InternalLink' => 'Internal Link',
		'URL' => 'OR External Link (e.g. http://twitter.com/myname/) - will override internal link',
		'Title' => 'Title',
		'Sort' => 'Sort Index (lower numbers shown first)',
		'IconID' => 'Icon (preferably something like 32pixels by 32pixels)'
	);

	private static $summary_fields = array(
		'Title' => 'Title',
		'IconHTML' => 'HTMLText'
	);

	private static $default_sort = 'Sort ASC, Title ASC';

	private static $singular_name = 'Join Us link';

	private static $plural_name = 'Join Us links';

	function canView($member = null) {
		return Permission::checkMember($member, 'SOCIAL_MEDIA');
	}

	function canCreate($member = null) {
		return Permission::checkMember($member, 'SOCIAL_MEDIA');
	}

	function canEdit($member = null) {
		return Permission::checkMember($member, 'SOCIAL_MEDIA');
	}

	function canDelete($member = null) {
		return Permission::checkMember($member, 'SOCIAL_MEDIA');
	}

	/**
	 * @return String - returns the title with all non-alphanumeric + spaces removed.
	 */
	function Code() {
		return strtolower(preg_replace("/[^a-zA-Z0-9]/", '', $this->Title));
	}

	function IconHTML() {return $this->getIconHTML();}
	function getIconHTML() {
		$icon = $this->Icon();
		if($icon && $icon->exists()) {
			$html = $icon->SetHeight(32);
		}
		else {
			$html = DBField::create_field("HTMLText",'<img src="/' . SS_SHARETHIS_DIR . "/images/icons/{$this->Code}.png\" alt=\"{$this->Code}\"/>");
		}
		return  $html;
	}

	function Link() {
		if($this->URL) {
			return $this->URL;
		}
		elseif($this->InternalLinkID) {
			$page = SiteTree::get()->byID($this->InternalLinkID);
			if($page->exists()) {
				return $page->Link();
			}
		}
	}

	function getCMSFields() {
		$fields = parent::getCMSFields();
		if($this->ID) {
			$fields->addFieldToTab('Root.Main', new LiteralField('Code', "<p>Code: {$this->Code()}</p>"));
			$fields->addFieldToTab('Root.Main', new LiteralField('Link', "<p>Link: <a href=\"{$this->Link()}\">{$this->Link()}</a></p>"));
			$fields->addFieldToTab('Root.Main', new LiteralField('Link', "<p>{$this->IconHTML()}</p>"));
		}
		$fields->removeFieldFromTab('Root.Main', 'InternalLinkID');
		$fields->addFieldToTab('Root.Main', new TreeDropdownField('InternalLinkID', 'Internal Link', 'SiteTree'), 'URL');
		return $fields;
	}
}
