<?php

namespace SunnySideUp\ShareThis;









use SilverStripe\Assets\Image;
use SilverStripe\Core\Config\Config;
use SunnySideUp\ShareThis\code\model\ShareThisDataObject;
use SilverStripe\Security\Permission;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\Forms\LiteralField;
use SunnySideUp\ShareThis\code\data\ShareThisOptions;
use SunnySideUp\ShareThis\code\extension\ShareThisSTE;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\PermissionProvider;



/**
 * @author nicolaas[at]sunnysideup.co.nz
 * @description: list of Share This Options that can be shown
 * @todo finish onAfterWrite and delete objects
 */
class ShareThisDataObject extends DataObject implements PermissionProvider
{
    private static $permission_framework = array(
        "SOCIAL_MEDIA" => array(
            'name' => "Social Media Management",
            'category' => "Social Media",
            'help' => 'Edit relationships, links and data of various social media platforms.',
            'sort' => 0
        )
    );

    private static $db = array(
        'Title' => 'Varchar(20)',
        'IncludeThisIcon' => 'Boolean',
        'IncludeThisIconInExtendedList' => 'Boolean',
        'Sort' => 'Int'
    );

    private static $has_one = array(
        'AlternativeIcon' => Image::class
    );

    private static $casting = array(
        'Icon' => 'HTMLText',
        'IncludeThisIconNice' => 'Varchar',
        'IncludeThisIconInExtendedListNice' => 'IncludeThisIconInExtendedList'
    );

    private static $field_labels = array(
        'Title' => 'Name',
        'IncludeThisIcon' => 'Include in main list',
        'IncludeThisIconNice' => 'Include in primary list',
        'IncludeThisIconInExtendedList' => 'Include in secondary list',
        'IncludeThisIconInExtendedListNice' => 'Include in secondary list',
        'Sort' => 'Sort Index (lower numbers shown first)',
        'AlternativeIcon' => 'Optional Alternative Icon (can be any size, a 32px by 32px square is recommended)'
    );

    private static $summary_fields = array(
        'Icon' => 'Icon',
        'Title' => 'Name',
        'IncludeThisIconNice' => 'IncludeThisIcon'
        //'IncludeThisIconInExtendedListNice' => 'IncludeThisIconInExtendedList'
    );

    private static $singular_name = 'Icon to share this page';

    private static $plural_name = 'Icons to share this page';

    private static $default_sort = 'IncludeThisIcon DESC, IncludeThisIconInExtendedList ASC, Sort ASC, Title ASC';

    public function providePermissions()
    {
        return Config::inst()->get(ShareThisDataObject::class, "permission_framework");
    }

    public function canView($member = null)
    {
        return Permission::checkMember($member, 'SOCIAL_MEDIA');
    }

    public function canCreate($member = null)
    {
        return Permission::checkMember($member, 'SOCIAL_MEDIA');
    }

    public function canEdit($member = null)
    {
        return Permission::checkMember($member, 'SOCIAL_MEDIA');
    }

    public function canDelete($member = null)
    {
        return Permission::checkMember($member, 'SOCIAL_MEDIA');
    }
    public function IncludeThisIconNice()
    {
        return $this->getIncludeThisIconNice();
    }
    public function getIncludeThisIconNice()
    {
        return $this->IncludeThisIcon ? "YES" : "NO" ;
    }

    public function IncludeThisIconInExtendedListNice()
    {
        return $this->getIncludeThisIconInExtendedListNice();
    }
    public function getIncludeThisIconInExtendedListNice()
    {
        return $this->IncludeThisIconInExtendedList ? "YES" : "NO" ;
    }

    public function Icon()
    {
        return $this->getIcon();
    }
    public function getIcon()
    {
        $icon = $this->AlternativeIcon();
        if ($icon->exists()) {
            return $icon->SetHeight(16);
        }
        $html = '<img src="' . SS_SHARETHIS_DIR . '/images/icons/' . strtolower($this->Title) . ".png\" alt=\"{$this->Title}\"/>";
        return DBField::create_field("HTMLText", $html);
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        if (class_exists("DataObjectSorterDOD")) {
            $fields->addFieldToTab("Root.Sort", new LiteralField("SortShortList", $this->dataObjectSorterPopupLink("IncludeThisIcon", 1, "<h3>Sort Main Icons</h3>")));
        }
        //$fields->replaceField('Title', new LiteralField('Title', "<p>{$this->Icon}<span>{$this->Title}</span></p>"));
        return $fields;
    }

    public function onAfterWrite()
    {
        parent::onAfterWrite();
        $objects = ShareThisDataObject::get()->filter('Title', $this->Title)->exclude('ID', $this->ID);
        //$objects->delete();
    }

    public function validate()
    {
        $result = parent::validate();
        $bookmarks = ShareThisOptions::get_page_specific_data("", "", "");
        if (!isset($bookmarks[$this->Title])) {
            $result->error(sprintf(
                _t(
                    'ShareThisDataObject.NON_EXISTING_TITLE',
                    'This social plaform "%s" does not exist.  Please change / delete the this entry.'
                ),
                $this->Title
            ));
        }

        return $result;
    }

    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();
        $actualArray = ShareThisOptions::get_general_data();
        Config::inst()->update(ShareThisSTE::class, "included_icons", array());
        Config::inst()->update(ShareThisSTE::class, "excluded_icons", array());
        ShareThisOptions::set_general_data(null);
        $fullArray = ShareThisOptions::get_general_data();
        foreach ($fullArray as $key) {
            $object = ShareThisDataObject::get()->filter('Title', $key);
            if (! $object->exists()) {
                $object = new ShareThisDataObject();
                $object->Title = $key;
                $style = 'excluded';
                $object->IncludeThisIcon = false;
                if (in_array($key, $actualArray)) {
                    $object->IncludeThisIcon = true;
                    $style = 'included';
                }
                $object->write();
                DB::alteration_message("Added Bookmark Icon for $key ($style)", 'created');
            }
        }
        $inc = Config::inst()->get(ShareThisSTE::class, "included_icons");
        foreach ($inc as $key) {
            $object = ShareThisDataObject::get()->filter(array('Title' => $key, 'IncludeThisIcon' => 0));
            if ($object->exists()) {
                $object = $object->first();
                $object->IncludeThisIcon = true;
                $object->write();
                DB::alteration_message("Updated inclusion for $key", 'created');
            }
        }
        $exc = Config::inst()->get(ShareThisSTE::class, "excluded_icons");
        foreach ($exc as $key) {
            $object = ShareThisDataObject::get()->filter(array('Title' => $key, 'IncludeThisIcon' => 1));
            if ($object->exists()) {
                $object = $object->first();
                $object->IncludeThisIcon = false;
                $object->write();
                DB::alteration_message("Updated inclusion for $key", 'created');
            }
        }
    }
}
