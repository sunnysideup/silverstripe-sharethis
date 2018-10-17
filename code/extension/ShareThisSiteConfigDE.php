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
    private static $db = array(
        'AlwaysIncludeShareThisLinks' => 'Boolean',
        'AlwaysIncludeSocialNetworkingLinks' => 'Boolean',
        'IncludeByDefaultShareThisLinks' => 'Boolean',
        'IncludeByDefaultSocialNetworkingLinks' => 'Boolean',
        'ShareThisAllInOne' => 'Boolean'
    );

    public function updateCMSFields(FieldList $fields)
    {
        $individualPageNoteWith = _t("ShareThis.INDIVIDUAL_PAGE_NOTE_WITH", " (with the ability to turn them off/on on individual pages) ");

        $individualPageNoteWithout  = _t("ShareThis.INDIVIDUAL_PAGE_NOTE_WITHOUT", " (without the ability to turn them off/on on individual pages) ");

        $shareThisExtra = '<h3 style="margin-top: 50px">Select Icons</h3>';

        $shareThisTableField = new GridField('Share this options', null, ShareThisDataObject::get(), GridFieldConfig_RecordEditor::create());

        $socialNetworkExtra = '<h3 style="margin-top: 50px">Add / Edit / Delete Your Social Networking Home Pages (e.g. www.facebook.com/our-company-page)</h3>';

        $socialNetworkTableField = new GridField('Join Us', null, SocialNetworkingLinksDataObject::get(), GridFieldConfig_RecordEditor::create());

        if ($this->owner->AlwaysIncludeShareThisLinks) {
            $defaultShareThisCheckbox = new HiddenField('IncludeByDefaultShareThisLinks', true);
        } else {
            $defaultShareThisCheckbox = new CheckboxField('IncludeByDefaultShareThisLinks', 'Show links on every page by default '.$individualPageNoteWith);
        }

        if ($this->owner->AlwaysIncludeSocialNetworkingLinks) {
            $defaultSocialNetworkingCheckbox = new HiddenField('IncludeByDefaultSocialNetworkingLinks', true);
        } else {
            $defaultSocialNetworkingCheckbox = new CheckboxField('IncludeByDefaultSocialNetworkingLinks', 'Include on every page by default '.$individualPageNoteWith);
        }

        $fields->addFieldToTab(
            'Root.SocialMedia',
            new TabSet(
                'SocialNetworkingOptions',
                new Tab(
                    'ShareThis',
                    new CheckboxField('AlwaysIncludeShareThisLinks', 'Show links on every page '.$individualPageNoteWithout),
                    $defaultShareThisCheckbox,
                    new CheckboxField('ShareThisAllInOne', 'Add a \'share\' all-in-one button'),
                    new LiteralField('shareThisExtra', $shareThisExtra),
                    $shareThisTableField
                ),
                new Tab(
                    'JoinUs',
                    new CheckboxField('AlwaysIncludeSocialNetworkingLinks', 'Show links on every page '.$individualPageNoteWithout),
                    $defaultSocialNetworkingCheckbox,
                    new LiteralField('socialNetworkExtra', $socialNetworkExtra),
                    $socialNetworkTableField
                )
            )
        );

        return $fields;
    }

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
