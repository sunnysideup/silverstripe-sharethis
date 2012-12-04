<?php

/**
 *
 *@author nicolaas[at]sunnysideup.co.nz
 *@description: list of Share This Options that can be shown
 *
 **/

class ShareThisDataObject extends DataObject {


	public static $db = array(
		'Title' => 'Varchar(20)',
		'IncludeThisIcon' => 'Boolean',
		'IncludeThisIconInExtendedList' => 'Boolean',
		'Sort' => 'Int'
	);

	public static $has_many = array();
	
	public static $has_one = array(
		"AlternativeIcon" => "Image"
	);

	public static $many_many = array();

	public static $belongs_many_many = array();

	public static $defaults = array();

	public static $casting = array(
		"Icon" => "HTMLText"
	);

	public static $searchable_fields = array();

	public static $field_labels = array(
		"Title" => "Name",
		"IncludeThisIcon" => "Include in short list",
		"IncludeThisIconInExtendedList" => "Include in long list",
		"Sort" => "Sort Index (lower numbers shown first)",
		"AlternativeIcon" => "Optional Alternative Icon (16 x 16 px)"
	);

	public static $default_sort = "IncludeThisIcon DESC, IncludeThisIconInExtendedList, Sort ASC, Title ASC";

	public function canView($member = false) {
		return Permission::check('CMS_ACCESS_CMSMain');
	}

	public function canDelete($member = false) {
		return $this->canView($member);
	}

	public function canEdit($member = false) {
		return $this->canView($member);
	}

	public static $summary_fields = array(
		"Icon" => "Icon",	
		"Title" => "Name",
		"IncludeThisIcon" => "IncludeThisIcon",
		"IncludeThisIconInExtendedList" => "IncludeThisIconInExtendedList"
	);

	public static $singular_name = "Icon to share this page";

	public static $plural_name = "Icons to share this page";

	public function getIcon() {
		if($this->AlternativeIcon() && $this->AlternativeIcon()->Exists())  {
			return $this->AlternativeIcon()->SetHeight(16);
		}
		return '<img src="/'.SS_SHARETHIS_DIR."/images/icons/".strtolower($this->Title).".png".'" alt="'.$this->Title.'" />';
	}

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->replaceField("Title", new LiteralField("Title", "<p>".$this->Icon."<span>".$this->Title."</span></p>"));
		return $fields;
	}

	function onAfterWrite() {
		parent::onAfterWrite();
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		if($obj = DataObject::get("ShareThisDataObject", "{$bt}Title{$bt} = '".$this->Title."' AND ID <> ".$this->ID)) {
			$obj->delete();
		}
	}

	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		if(1 == 1) {
			$actualArray = ShareThisOptions::get_general_data();
			ShareThis::set_share_this_icons_to_include(array());
			ShareThis::set_share_this_icons_to_exclude(array());
			ShareThisOptions::set_general_data(null);
			$fullArray = ShareThisOptions::get_general_data();
			foreach($fullArray as $key) {
				if(!DataObject::get("ShareThisDataObject", "Title = '".$key."'")) {
					$o = new ShareThisDataObject();
					$o->Title = $key;
					$style = "excluded";
					$o->IncludeThisIcon = 0;
					if(in_array($key, $actualArray)) {
						$o->IncludeThisIcon = 1;
						$style = "included";
					}
					$o->write();
					DB::alteration_message("Added Bookmark Icon for ".$key." (".$style.")", "created");
				}
			}
		}
		$inc = ShareThis::get_share_this_icons_to_include();
		$exc = ShareThis::get_share_this_icons_to_exclude();
		if(count($inc)) {
			foreach($inc as $key) {
				if($obj = DataObject::get("ShareThisDataObject", "Title = '".$key."' AND IncludeThisIcon = 0")) {
					$obj->IncludeThisIcon = 1;
					$obj->write();
					DB::alteration_message("updated inclusion for ".$key, "created");
				}
			}
		}
		if(count($exc)) {
			foreach($exc as $key) {
				if($obj = DataObject::get("ShareThisDataObject", "Title = '".$key."' AND IncludeThisIcon = 1")) {
					$obj->IncludeThisIcon = 0;
					$obj->write();
					DB::alteration_message("updated inclusion for ".$key, "created");
				}
			}
		}
	}

}
