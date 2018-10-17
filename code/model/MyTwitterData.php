<?php

namespace SunnySideUp\ShareThis;

use SilverStripe\Core\Config\Config;
use SilverStripe\Security\Permission;
use SilverStripe\ORM\DataObject;

/**
 * MyTwitterData
 */
class MyTwitterData extends DataObject
{
    private static $username = "";

    private static $db = array(
        "Date" => "SS_Datetime",
        "TwitterID" => "Varchar(64)",
        "Title" => "HTMLText",
        "Hide" => "Boolean"
    );

    private static $summary_fields = array(
        "Date" => "Date",
        "Title" => "Title",
        "HideNice" => "Hide"
    );

    private static $indexes = array(
        "TwitterID" => true
    );

    private static $casting = array(
        "Link" => "Varchar",
        "HideNice" => "Varchar"
    );

    private static $default_sort = "\"Date\" DESC";

    public function forTemplate()
    {
        return $this->Title;
    }

    public function Link()
    {
        return "https://twitter.com/".Config::inst()->get(MyTwitterData::class, "username")."/status/".$this->TwitterID;
    }


    public function canView($member = null)
    {
        return Permission::checkMember($member, 'SOCIAL_MEDIA');
    }

    public function canCreate($member = null, $context = [])
    {
        return false;
    }

    public function canEdit($member = null)
    {
        return Permission::checkMember($member, 'SOCIAL_MEDIA');
    }

    public function canDelete($member = null)
    {
        return false;
    }

    public function HideNice()
    {
        return $this->dbObject('Hide')->Nice();
    }
}
