<?php

namespace SunnysideUp\ShareThis;

use SilverStripe\Assets\Image;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;

/**
 * @author nicolaas[at]sunnysideup.co.nz
 * @description: list of Share This Options that can be shown
 * @todo finish onAfterWrite and delete objects
 */
// class ShareThisDataObject extends DataObject implements PermissionProvider
class ShareThisDataObject extends DataObject
{
    /**
     * @var string
     */
    private static $table_name = 'ShareThisDataObject';

    /**
     * @var array
     */
    private static $permission_framework = [
        'SOCIAL_MEDIA' => [
            'name' => 'Social Media Management',
            'category' => 'Social Media',
            'help' => 'Edit relationships, links and data of various social media platforms.',
            'sort' => 0,
        ],
    ];

    /**
     * @var array
     */
    private static $db = [
        'Title' => 'Varchar(20)',
        'IncludeThisIcon' => 'Boolean',
        'IncludeThisIconInExtendedList' => 'Boolean',
        'Sort' => 'Int',
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'AlternativeIcon' => Image::class,
    ];

    /**
     * @var array
     */
    private static $casting = [
        'Icon' => 'HTMLText',
        'IncludeThisIconNice' => 'Varchar',
        'IncludeThisIconInExtendedListNice' => 'IncludeThisIconInExtendedList',
    ];

    /**
     * @var array
     */
    private static $field_labels = [
        'Title' => 'Name',
        'IncludeThisIcon' => 'Include in main list',
        'IncludeThisIconNice' => 'Include in primary list',
        'IncludeThisIconInExtendedList' => 'Include in secondary list',
        'IncludeThisIconInExtendedListNice' => 'Include in secondary list',
        'Sort' => 'Sort Index (lower numbers shown first)',
        'AlternativeIcon' => 'Optional Alternative Icon (can be any size, a 32px by 32px square is recommended)',
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'Title' => 'Name',
    ];

    /**
     * @var string
     */
    private static $singular_name = 'Icon to share this page';

    /**
     * @var string
     */
    private static $plural_name = 'Icons to share this page';

    /**
     * @var string
     */
    private static $default_sort = 'IncludeThisIcon DESC, IncludeThisIconInExtendedList ASC, Sort ASC, Title ASC';

    /**
     * @return string
     */
    public function providePermissions()
    {
        return Config::inst()->get(ShareThisDataObject::class, 'permission_framework');
    }

    /**
     * @return boolean
     */
    public function canView($member = null)
    {
        return Permission::checkMember($member, 'SOCIAL_MEDIA');
    }

    /**
     * @return boolean
     */
    public function canCreate($member = null, $context = [])
    {
        return Permission::checkMember($member, 'SOCIAL_MEDIA');
    }

    /**
     * @return boolean
     */
    public function canEdit($member = null)
    {
        return Permission::checkMember($member, 'SOCIAL_MEDIA');
    }

    /**
     * @return boolean
     */
    public function canDelete($member = null)
    {
        return Permission::checkMember($member, 'SOCIAL_MEDIA');
    }

    /**
     * @return string
     */
    public function IncludeThisIconNice()
    {
        return $this->getIncludeThisIconNice();
    }

    /**
     * @return string
     */
    public function getIncludeThisIconNice()
    {
        return $this->IncludeThisIcon ? 'Yes' : 'No';
    }

    /**
     * @return string
     */
    public function IncludeThisIconInExtendedListNice()
    {
        return $this->getIncludeThisIconInExtendedListNice();
    }

    /**
     * @return string
     */
    public function getIncludeThisIconInExtendedListNice()
    {
        return $this->IncludeThisIconInExtendedList ? 'Yes' : 'No';
    }

    /**
     * Icon
     */
    public function Icon()
    {
        return $this->getIcon();
    }

    /**
     * Get the icon
     *
     * @return  DBField [<description>]
     */
    public function getIcon()
    {
        $icon = $this->AlternativeIcon();
        if ($icon->exists()) {
            return $icon->ScaleHeight(16);
        }

        $html = '<img src="' . SS_SHARETHIS_DIR . '/images/icons/' . strtolower($this->Title) . ".png\" alt=\"{$this->Title}\"/>";

        return DBField::create_field('HTMLText', $html);
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        if (class_exists('DataObjectSorterDOD')) {
            $fields->addFieldToTab('Root.Sort', LiteralField::create('SortShortList', $this->dataObjectSorterPopupLink('IncludeThisIcon', 1, '<h3>Sort Main Icons</h3>')));
        }

        return $fields;
    }

    /**
     * @return ValidationResult
     */
    public function validate()
    {
        $result = parent::validate();
        $bookmarks = ShareThisOptions::get_page_specific_data('', '', '');
        if (! isset($bookmarks[$this->Title])) {
            $result->addError(sprintf(
                _t(
                    'ShareThisDataObject.NON_EXISTING_TITLE',
                    'This social plaform "%s" does not exist.  Please change / delete the this entry.'
                ),
                $this->Title
            ));
        }

        return $result;
    }

    /**
     * Setting default records
     */
    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();
        $actualArray = ShareThisOptions::get_general_data();
        Config::inst()->update(ShareThisSTE::class, 'included_icons', []);
        Config::inst()->update(ShareThisSTE::class, 'excluded_icons', []);
        ShareThisOptions::set_general_data(null);
        $fullArray = ShareThisOptions::get_general_data();

        foreach ($fullArray as $key) {
            $object = ShareThisDataObject::get()->filter('Title', $key);

            if (! $object->exists()) {
                $object = new ShareThisDataObject();
                $object->Title = $key;
                $style = 'excluded';
                $object->IncludeThisIcon = false;

                if (in_array($key, $actualArray, true)) {
                    $object->IncludeThisIcon = true;
                    $style = 'included';
                }

                $object->write();
                DB::alteration_message("Added Bookmark Icon for ${key} (${style})", 'created');
            }
        }

        $inc = Config::inst()->get(ShareThisSTE::class, 'included_icons');

        foreach ($inc as $key) {
            $object = ShareThisDataObject::get()->filter(['Title' => $key, 'IncludeThisIcon' => 0]);

            if ($object->exists()) {
                $object = $object->first();
                $object->IncludeThisIcon = true;
                $object->write();
                DB::alteration_message("Updated inclusion for ${key}", 'created');
            }
        }

        $exc = Config::inst()->get(ShareThisSTE::class, 'excluded_icons');

        foreach ($exc as $key) {
            $object = ShareThisDataObject::get()->filter(['Title' => $key, 'IncludeThisIcon' => 1]);

            if ($object->exists()) {
                $object = $object->first();
                $object->IncludeThisIcon = false;
                $object->write();
                DB::alteration_message("Updated inclusion for ${key}", 'created');
            }
        }
    }
}
