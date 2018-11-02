<?php

namespace SunnysideUp\ShareThis;

use SilverStripe\Forms\FieldList;
use SunnysideUp\ShareThis\ShareThisDataObject;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridField;
use SunnysideUp\ShareThis\SocialNetworkingLinksDataObject;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TabSet;
use SilverStripe\ORM\DataExtension;

/**
 * @todo Check that permissions on the 2 tables in the CMS are the same than before
 * @todo Fix the CanEditShareIcons section in updateCMSFields
 */
class ShareThisSiteConfigDE extends DataExtension
{
    /**
     * @var array
     */
    private static $db = [
        'AlwaysIncludeShareThisLinks' => 'Boolean',
        'AlwaysIncludeSocialNetworkingLinks' => 'Boolean',
        'IncludeByDefaultShareThisLinks' => 'Boolean',
        'IncludeByDefaultSocialNetworkingLinks' => 'Boolean',
        'ShareThisAllInOne' => 'Boolean'
    ];

    /**
     * @param  FieldList $fields
     *
     * @return FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        $individualPageNoteWith = _t("ShareThis.INDIVIDUAL_PAGE_NOTE_WITH", " (with the ability to turn them off/on on individual pages) ");

        $individualPageNoteWithout  = _t("ShareThis.INDIVIDUAL_PAGE_NOTE_WITHOUT", " (without the ability to turn them off/on on individual pages) ");

        $shareThisExtra = '<h3 style="margin-top: 50px">Select Icons</h3>';

        $shareThisTableField = GridField::create('ShareThisOptions', null, ShareThisDataObject::get(), GridFieldConfig_RecordEditor::create());

        $socialNetworkExtra = '<h3 style="margin-top: 50px">Add / Edit / Delete Your Social Networking Home Pages (e.g. www.facebook.com/our-company-page)</h3>';

        $socialNetworkTableField = GridField::create('JoinUs', null, SocialNetworkingLinksDataObject::get(), GridFieldConfig_RecordEditor::create());

        if ($this->owner->AlwaysIncludeShareThisLinks) {
            $defaultShareThisCheckbox = HiddenField::create('IncludeByDefaultShareThisLinks', true);
        } else {
            $defaultShareThisCheckbox = CheckboxField::create('IncludeByDefaultShareThisLinks', 'Show links on every page by default '.$individualPageNoteWith);
        }

        if ($this->owner->AlwaysIncludeSocialNetworkingLinks) {
            $defaultSocialNetworkingCheckbox = HiddenField::create('IncludeByDefaultSocialNetworkingLinks', true);
        } else {
            $defaultSocialNetworkingCheckbox = CheckboxField::create('IncludeByDefaultSocialNetworkingLinks', 'Include on every page by default '.$individualPageNoteWith);
        }

        $fields->addFieldToTab(
            'Root.SocialMedia',
            TabSet::create(
                'SocialNetworkingOptions',
                Tab::create(
                    'ShareThis',
                    CheckboxField::create('AlwaysIncludeShareThisLinks', 'Show links on every page '.$individualPageNoteWithout),
                    $defaultShareThisCheckbox,
                    CheckboxField::create('ShareThisAllInOne', 'Add a \'share\' all-in-one button'),
                    LiteralField::create('shareThisExtra', $shareThisExtra),
                    $shareThisTableField
                ),
                Tab::create(
                    'JoinUs',
                    CheckboxField::create('AlwaysIncludeSocialNetworkingLinks', 'Show links on every page '.$individualPageNoteWithout),
                    $defaultSocialNetworkingCheckbox,
                    LiteralField::create('socialNetworkExtra', $socialNetworkExtra),
                    $socialNetworkTableField
                )
            )
        );

        return $fields;
    }

    /**
     * CanEditShareIcons
     *
     * @return void
     */
    public function CanEditShareIcons()
    {
        if (class_exists('DataObjectSorterDOD')) {
            $obj = singleton(ShareThisDataObject::class);
            if ($obj->hasExtension('DataObjectSorterDOD')) {
                return true;
            } else {
                USER_ERROR('You have installed DataObjectSorterDOD, but you have not extended ShareThisDataObject with DataObjectSorterDOD, see sharethis/_config.php for more information.', E_USER_NOTICE);
            }
        } else {
            USER_ERROR('You need to install the DataObjectSorter module (see sharethis/README or sharethis/_config.php for more information)', E_USER_NOTICE);
        }
    }
}
