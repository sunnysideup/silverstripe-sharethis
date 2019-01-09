<?php

namespace SunnysideUp\ShareThis;

use SilverStripe\Dev\Debug;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\LiteralField;
use SunnysideUp\ShareThis\ShareThisOptions;
use SilverStripe\View\Requirements;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Convert;
use SilverStripe\View\ArrayData;
use SilverStripe\ORM\ArrayList;
use SunnysideUp\ShareThis\ShareThisDataObject;
use SilverStripe\CMS\Model\SiteTreeExtension;

/**
 * Add a field to each SiteTree object and it's subclasses to enable Share icons.
 * @author nicolaas [at] sunnysideup.co.nz
 * @inspiration: Silverstripe Original Module - full credits to them.  We made our own to improve their module
 * @todo fix populateDefaults to make sure SiteConfig table is built first
 */
class ShareThisSTE extends SiteTreeExtension
{

    /**
     * Use the font-awesome icon collection?
     * @var Boolean
     */
    private static $use_font_awesome = true;

    /**
     * list of sitetree extending classnames where
     * the ShareThis functionality should be included
     * @var Array
     */
    private static $always_include_in = [];

    /**
     * list of sitetree extending classnames where
     * the ShareThis functionality should NEVER be included
     * @var Array
     */
    private static $never_include_in = [];

    /**
    * use BW icons
    * @var boolean
    */
    private static $use_bw_effect = false;

    /**
    * specify icons to be included, if left empty, this variable will be ignored
    * We have this variable so that you can setup a bunch of default icons
    * @var array
    */
    private static $included_icons = [];

    /**
    * specify icons to be excluded, if left empty, this variable will be ignored
    * We have this variable so that you can setup a bunch of default icons
    * @var array
    */
    private static $excluded_icons = [];

    /**
     * standard SS method
     * @var Array
     **/
    private static $db = array(
        'ShareIcons' => 'Boolean'
    );

    /**
     * @param  FieldList $fields
     *
     * @return FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        if ($this->applyToOwnerClass()) {
            $config = $this->owner->getSiteConfig();

            if (! $config->AlwaysIncludeShareThisLinks) {
                $fields->addFieldToTab('Root.SocialMedia', HeaderField::create('ShareThisHeader', 'Allow users to share this page'));

                $fields->addFieldToTab('Root.SocialMedia', CheckboxField::create('ShareIcons', 'Show Share Icons on this page', $config->IncludeByDefault));

                $fields->addFieldToTab('Root.SocialMedia', LiteralField::create('LinkToSiteConfigSocialMedia', "<p>Note: make sure to review the social media settings in the <a href=\"{$config->CMSEditLink()}\">Site Config</a>.</p>"));
            }

            $list = ShareThisOptions::get_all_options($this->owner->Title, $this->owner->Link(), $this->owner->MetaDescription);

            $fields->addFieldToTab('Root.SocialMedia', HeaderField::create('ShareThisNow', 'Share this page on your favourite social media sites...'));

            $html = "<div><p>Click on any of the icons below to share the '<i>{$this->owner->Title}</i>' page. Any click will open a new tab/window where you will need to enter your login details.</p>";

            foreach ($list as $key => $innerArray) {
                if (! isset($innerArray['click'])) {
                    $html .= "<span><a href=\"{$innerArray['url']}\" target=\"_blank\" style=\"whitespace: nowrap; display: inline-block;\"><img src=\"" . SS_SHARETHIS_DIR . "/images/icons/$key.png\" alt=\"$key\"/>{$innerArray['title']}</a></span>&nbsp;&nbsp;";
                }
            }

            $html .= '</div>';
            $fields->addFieldToTab('Root.SocialMedia', LiteralField::create('ShareNow', $html));
        }

        return $fields;
    }

    /**
     * Show the sharing icons
     */
    public function getShowShareIcons()
    {
        if ($this->applyToOwnerClass()) {
            $config = $this->owner->getSiteConfig();
            if ($config->AlwaysIncludeShareThisLinks) {
                return true;
            }
            return $this->owner->ShareIcons;
        }
    }

    /**
     * Get the sharing icons
     */
    public function getShareIcons()
    {
        $bookmarks = $this->makeBookmarks('IncludeThisIcon');
        return $this->makeShareIcons($bookmarks);
    }

    /**
     * Grabbing front end dependencies for the expanded sharing list with some extra
     * functionality
     */
    public function ShareAllExpandedList()
    {
        Requirements::javascript('silverstripe/admin: thirdparty/jquery/jquery.min.js');
        Requirements::javascript('sunnysideup/sharethis: javascript/ShareAllExpandedList.js');
        $bookmarks = $this->makeBookmarks('IncludeThisIconInExtendedList');
        return $this->makeShareIcons($bookmarks);
    }

    /**
     * Include share all
     */
    public function IncludeShareAll()
    {
        $config = $this->owner->getSiteConfig();
        return $config->ShareThisAllInOne;
    }

    /**
     * @return boolean
     */
    public function getShareAll()
    {
        if ($this->IncludeShareAll()) {
            return ShareThisOptions::get_share_all();
        }
    }

    /**
     * @return array
     */
    protected function makeShareIcons($bookmarks)
    {
        $icons = [];
        if ($bookmarks) {
            $useFontAwesome = Config::inst()->get(ShareThisSTE::class, "use_font_awesome");
            Requirements::themedCSS('SocialNetworking', "sharethis"); // ALSO  added in template

            if ($useFontAwesome) {
                Requirements::css("//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css");
            }

            Requirements::javascript('sunnysideup/sharethis: javascript/shareThis.js');

            if (Config::inst()->get(ShareThisSTE::class, "use_bw_effect")) {
                Requirements::customScript('sharethis.set_use_BW(true);', 'ShareThisBWEffect');
            }

            foreach ($bookmarks as $key => $bookmark) {
                if (isset($bookmark['title']) && isset($bookmark['url'])) {
                    $icon = array(
                        'Title' => Convert::raw2att($bookmark['title']),
                        'URL' => $bookmark['url'],
                        'Key' => $key,
                        'ImageSource' => "sharethis/images/icons/$key.png",
                        'FAIcon' => $bookmark["faicon"],
                        'UseStandardImage' => true
                    );

                    if (isset($bookmark['click'])) {
                        $icon['OnClick'] = $bookmark['click'];
                    }

                    if ($useFontAwesome) {
                        $icon['ImageSource'] = null;
                        $icon['UseStandardImage'] = false;
                        $icon['FAIcon'] = $bookmark["faicon"];
                    }

                    if (isset($bookmark['icon'])) {
                        $icon['ImageSource'] = $bookmark['icon'];
                        $icon['UseStandardImage'] = false;
                        $icon['FAIcon'] = null;
                    }

                    $icons[] = new ArrayData($icon);
                } else {
                    Debug::show("Title of url not defined for $key");
                }
            }
        }

        return new ArrayList($icons);
    }

    /**
     * Creating the bookmarks
     */
    protected function makeBookmarks($field)
    {
        $finalBookmarks = [];

        $bookmarks = ShareThisOptions::get_page_specific_data($this->owner->Title, $this->owner->Link(), $this->owner->MetaDescription);

        $objects = ShareThisDataObject::get()
            ->filter($field, 1)
            ->sort(array('Sort' => 'ASC', 'Title' => 'ASC'));
        if ($objects->count()) {
            foreach ($objects as $obj) {
                if (isset($bookmarks[$obj->Title])) {
                    $finalBookmarks[$obj->Title] = $bookmarks[$obj->Title];

                    if ($obj->AlternativeIconID && $obj->AlternativeIcon()->exists()) {
                        $finalBookmarks[$obj->Title]['icon'] = $obj->AlternativeIcon()->Link();
                    }
                }
            }
        } else {
            $finalBookmarks = $bookmarks;
        }

        return $finalBookmarks;
    }

    /**
     * @return boolean
     */
    private function applyToOwnerClass()
    {
        $always = Config::inst()->get(ShareThisSTE::class, "always_include_in");
        $never = Config::inst()->get(ShareThisSTE::class, "never_include_in");
        if (count($always) == 0 && count($never) == 0) {
            return true;
        } elseif (count($never) && count($always) == 0) {
            if (in_array($this->owner->ClassName, $never)) {
                return false;
            }

            return true;
        } elseif (count($always) && count($never) == 0) {
            if (in_array($this->owner->ClassName, $always)) {
                return true;
            }

            return false;
        } elseif (count($never) && count($always)) {
            if (in_array($this->owner->ClassName, $never)) {
                return false;
            }

            if (in_array($this->owner->ClassName, $always)) {
                return true;
            }

            //exception... if dev sets both always and never
            //then the ones not set will be included by default.
            return true;
        } else {
            user_error("Strange condition!", E_USER_NOTICE);
        }
    }
}
