<?php

/**
 * @author nicolaas[at]sunnysideup.co.nz
 * @description: list of Share This Options that can be shown
 * @todo finish onAfterWrite and delete objects
 */
class ShareThisDataObject extends DataObject {

	static $db = array(
		'Title' => 'Varchar(20)',
		'IncludeThisIcon' => 'Boolean',
		'IncludeThisIconInExtendedList' => 'Boolean',
		'Sort' => 'Int'
	);

	static $has_one = array(
		'AlternativeIcon' => 'Image'
	);

	static $casting = array(
		'Icon' => 'HTMLText'
	);

	static $field_labels = array(
		'Title' => 'Name',
		'IncludeThisIcon' => 'Include in short list',
		'IncludeThisIconInExtendedList' => 'Include in long list',
		'Sort' => 'Sort Index (lower numbers shown first)',
		'AlternativeIcon' => 'Optional Alternative Icon (16 x 16 px)'
	);

	static $summary_fields = array(
		'Icon' => 'Icon',
		'Title' => 'Name',
		'IncludeThisIcon' => 'IncludeThisIcon',
		'IncludeThisIconInExtendedList' => 'IncludeThisIconInExtendedList'
	);

	static $singular_name = 'Icon to share this page';

	static $plural_name = 'Icons to share this page';

	static $default_sort = 'IncludeThisIcon DESC, IncludeThisIconInExtendedList ASC, Sort ASC, Title ASC';

	function canView($member = null) {
		return Permission::check('CMS_ACCESS_CMSMain');
	}

	function canDelete($member = null) {
		return $this->canView($member);
	}

	function canEdit($member = null) {
		return $this->canView($member);
	}

	function getIcon() {
		$icon = $this->AlternativeIcon();
		if($icon->exists())  {
			return $icon->SetHeight(16);
		}
		return '<img src="' . SS_SHARETHIS_DIR . '/images/icons/' . strtolower($this->Title) . ".png\" alt=\"{$this->Title}\"/>";
	}

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->replaceField('Title', new LiteralField('Title', "<p>{$this->Icon}<span>{$this->Title}</span></p>"));
		return $fields;
	}

	function onAfterWrite() {
		parent::onAfterWrite();
		$objects = ShareThisDataObject::get()->filter('Title', $this->Title)->exclude('ID', $this->ID);
		//$objects->delete();
	}

	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$actualArray = ShareThisOptions::get_general_data();
		ShareThisSTE::set_included_icons(array());
		ShareThisSTE::set_excluded_icons(array());
		ShareThisOptions::$general_data = null;
		$fullArray = ShareThisOptions::get_general_data();
		foreach($fullArray as $key) {
			$object = ShareThisDataObject::get()->filter('Title', $key);
			if(! $object->exists()) {
				$object = new ShareThisDataObject();
				$object->Title = $key;
				$style = 'excluded';
				$object->IncludeThisIcon = false;
				if(in_array($key, $actualArray)) {
					$object->IncludeThisIcon = true;
					$style = 'included';
				}
				$object->write();
				DB::alteration_message("Added Bookmark Icon for $key ($style)", 'created');
			}
		}
		$inc = ShareThisSTE::get_included_icons();
		foreach($inc as $key) {
			$object = ShareThisDataObject::get()->filter(array('Title' => $key, 'IncludeThisIcon' => 0));
			if($object->exists()) {
				$object = $object->first();
				$object->IncludeThisIcon = true;
				$object->write();
				DB::alteration_message("Updated inclusion for $key", 'created');
			}
		}
		$exc = ShareThisSTE::get_excluded_icons();
		foreach($exc as $key) {
			$object = ShareThisDataObject::get()->filter(array('Title' => $key, 'IncludeThisIcon' => 1));
			if($object->exists()) {
				$object = $object->first();
				$object->IncludeThisIcon = false;
				$object->write();
				DB::alteration_message("Updated inclusion for $key", 'created');
			}
		}
	}
}
