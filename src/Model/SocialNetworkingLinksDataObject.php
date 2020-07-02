<?php

namespace SunnysideUp\ShareThis;

use \Page;
use SilverStripe\Assets\Image;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\Filters\PartialMatchFilter;
use SilverStripe\Security\Permission;

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *@description: creates a list of places where people can follow you (e.g. twitter, your blog, etc...)
 */
class SocialNetworkingLinksDataObject extends DataObject
{
    /**
     * @var string
     */
    private static $table_name = 'SocialNetworkingLinksDataObject';

    /**
     * @var array
     */
    private static $db = [
        'URL' => 'Varchar(255)',
        'Title' => 'Varchar(255)',
        'Sort' => 'Int',
    ];

    /**
     * @var array
     */
    private static $casting = [
        'Code' => 'Varchar(255)',
        'Link' => 'Varchar(255)',
        'IconHTML' => 'HTMLText',
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'Icon' => Image::class,
        'InternalLink' => Page::class,
    ];

    /**
     * @var array
     */
    private static $searchable_fields = [
        'Title' => PartialMatchFilter::class,
    ];

    /**
     * @return array
     */
    private static $field_labels = [
        'InternalLink' => 'Internal Link',
        'URL' => 'External Link (e.g. http://twitter.com/myname/) - will override internal link',
        'Title' => 'Title',
        'Sort' => 'Sort Index (lower numbers shown first)',
        'IconID' => 'Icon (preferably 32px X 32px)',
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'Title' => 'Title',
        'IconHTML' => 'Icon',
    ];

    /**
     * @var string
     */
    private static $default_sort = 'Sort ASC, Title ASC';

    /**
     * @var string
     */
    private static $singular_name = 'Join Us link';

    /**
     * @var string
     */
    private static $plural_name = 'Join Us links';

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
     * @return string - returns the title with all non-alphanumeric + spaces removed.
     */
    public function Code()
    {
        return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $this->Title));
    }

    /**
     * @return DBField
     */
    public function IconHTML()
    {
        return $this->getIconHTML();
    }

    /**
     * @return DBField / icon
     */
    public function getIconHTML()
    {
        $icon = $this->Icon();
        if ($icon && $icon->exists()) {
            $html = $icon->ScaleHeight(32);
        } else {
            $html = DBField::create_field('HTMLText', '<img src="/' . SS_SHARETHIS_DIR . "/images/icons/{$this->Code}.png\" alt=\"{$this->Code}\"/>");
        }
        return $html;
    }

    /**
     * Link
     *
     * @return string
     */
    public function Link()
    {
        if ($this->URL) {
            return $this->URL;
        } elseif ($this->InternalLinkID) {
            $page = SiteTree::get()->byID($this->InternalLinkID);
            if ($page->exists()) {
                return $page->Link();
            }
        }
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        if ($this->ID) {
            $fields->addFieldToTab('Root.Main', LiteralField::create('Code', "<p>Code: {$this->Code()}</p>"));
            $fields->addFieldToTab('Root.Main', LiteralField::create('Link', "<p>Link: <a href=\"{$this->Link()}\">{$this->Link()}</a></p>"));
            $fields->addFieldToTab('Root.Main', LiteralField::create('Link', "<p>{$this->IconHTML()}</p>"));
        }

        $fields->removeFieldFromTab('Root.Main', 'InternalLinkID');
        $fields->addFieldToTab('Root.Main', TreeDropdownField::create('InternalLinkID', 'Internal Link', SiteTree::class), 'URL');

        return $fields;
    }
}
