<?php

class MyTwitterData extends DataObject {

	private static $username = "";

	private static $db = array(
		"Date" => "SS_Datetime",
		"TwitterID" => "Varchar(64)",
		"Title" => "HTMLText",
		"Hide" => "Boolean"
	);

	private static $indexes = array(
		"TwitterID" => true
	);

	private static $casting = array(
		"Link" => "Varchar"
	);

	private static $default_sort = "\"Date\" DESC";

	function forTemplate(){
		return $this->Title;
	}

	function Link(){
		return "https://twitter.com/".Config::inst()->get("MyTwitterData", "username")."/status/".$this->TwitterID;
	}


	function canView($member = null) {
		return Permission::checkMember($member, 'SOCIAL_MEDIA');
	}

	function canCreate($member = null) {
		return false;
	}

	function canEdit($member = null) {
		return Permission::checkMember($member, 'SOCIAL_MEDIA');
	}

	function canDelete($member = null) {
		return Permission::checkMember($member, 'SOCIAL_MEDIA');

}
