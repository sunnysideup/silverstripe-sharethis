<?php

namespace SunnysideUp\ShareThis\Model;

use SilverStripe\Core\Config\Config;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;

/**
 * MyTwitterData
 */
class MyTwitterData extends DataObject
{
    /**
     * @var string
     */
    private static $table_name = 'MyTwitterData';

    /**
     * @var string
     */
    private static $username = '';

    /**
     * @var array
     */
    private static $db = [
        'Date' => 'Datetime',
        'TwitterID' => 'Varchar(64)',
        'Title' => 'HTMLText',
        'Hide' => 'Boolean',
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'Date' => 'Date',
        'Title' => 'Title',
        'HideNice' => 'Hide',
    ];

    /**
     * @var array
     */
    private static $indexes = [
        'TwitterID' => true,
    ];

    /**
     * @var array
     */
    private static $casting = [
        'Link' => 'Varchar',
        'HideNice' => 'Varchar',
    ];

    /**
     * @var string
     */
    private static $default_sort = '"Date" DESC';

    /**
     * @return string
     */
    public function forTemplate()
    {
        return $this->Title;
    }

    /**
     * @return string
     */
    public function Link()
    {
        return 'https://twitter.com/' . Config::inst()->get(MyTwitterData::class, 'username') . '/status/' . $this->TwitterID;
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
        return false;
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
        return false;
    }

    /**
     * @return boolean
     */
    public function HideNice()
    {
        return $this->dbObject('Hide')->Nice();
    }
}
