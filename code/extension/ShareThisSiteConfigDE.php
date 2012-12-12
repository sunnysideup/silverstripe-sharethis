<?php

/**
 * @todo Check that permissions on the 2 tables in the CMS are the same than before
 * @todo Fix the CanEditShareIcons section in updateCMSFields
 */
class ShareThisSiteConfigDE extends DataExtension {

	static $db = array(
		'AlwaysIncludeShareThisLinks' => 'Boolean',
		'AlwaysIncludeSocialNetworkingLinks' => 'Boolean',
		'IncludeByDefaultShareThisLinks' => 'Boolean',
		'IncludeByDefaultSocialNetworkingLinks' => 'Boolean',
		'ShareThisAllInOne' => 'Boolean'
	);

	function updateCMSFields(FieldList $fields) {
		$shareThisExtra = '<h3 style="margin-top: 50px">Select Icons</h3>';
/*		if($this->CanEditShareIcons()) {
			$addedLinks = array();
			$obj = singleton('ShareThisDataObject');
			$addedLinksShort['edit'] = DataObjectOneFieldUpdateController::popup_link('ShareThisDataObject', 'IncludeThisIcon');
			$addedLinksLong['edit'] = DataObjectOneFieldUpdateController::popup_link('ShareThisDataObject', 'IncludeThisIconInExtendedList');
			$addedLinksShort['sort'] = $obj->dataObjectSorterPopupLink('IncludeThisIcon', 1);
			$addedLinksLong['sort'] = $obj->dataObjectSorterPopupLink('IncludeThisIconInExtendedList', 1);
			if(count($addedLinksShort)) {
				$shareThisExtra .= '<p>short list: ' . implode(', ', $addedLinksShort) . '.</p>';
			}
			if(count($addedLinksLong)) {
				$shareThisExtra .= '<p>long list: ' . implode(', ', $addedLinksLong) . '.</p>';
			}
		}
*/		$shareThisTableField = new GridField('ShareThisDataObject', null, ShareThisDataObject::get(), GridFieldConfig_RecordEditor::create());
		//$shareThisTableField->setPermissions(array("edit", "add"));
		$socialNetworkExtra = '<h3 style="margin-top: 50px">Add / Edit / Delete Your Social Networking Home Pages (e.g. www.facebook.com/our-company-page)</h3>';
		$socialNetworkTableField = new GridField('SocialNetworkingLinksDataObject', null, SocialNetworkingLinksDataObject::get(), GridFieldConfig_RecordEditor::create());
		//$socialNetworkTableField->setPermissions(array("edit", "add", "delete", "view"));
		if($this->owner->AlwaysIncludeShareThisLinks) {
			$defaultShareThisCheckbox = new HiddenField('IncludeByDefaultShareThisLinks', true);
		}
		else {
			$defaultShareThisCheckbox = new CheckboxField('IncludeByDefaultShareThisLinks', 'Show links on every page by default (with the ability to turn them off on invididual pages)');
		}
		if($this->owner->AlwaysIncludeSocialNetworkingLinks) {
			$defaultSocialNetworkingCheckbox = new HiddenField('IncludeByDefaultSocialNetworkingLinks', true);
		}
		else {
			$defaultSocialNetworkingCheckbox = new CheckboxField('IncludeByDefaultSocialNetworkingLinks', 'Include on every page by default (with the ability to turn them off on individual pages)');
		}
		$fields->addFieldToTab('Root.SocialMedia',
			new TabSet('SocialNetworkingOptions',
				new Tab(
					'ShareThis',
					new CheckboxField('AlwaysIncludeShareThisLinks', 'Show links on every page (without the ability to turn them off on individual pages)'),
					$defaultShareThisCheckbox,
					new CheckboxField('ShareThisAllInOne', 'Add a \'share\' all-in-one button'),
					new LiteralField('shareThisExtra', $shareThisExtra),
					$shareThisTableField
				),
				new Tab(
					'SocialNetworkingLink',
					new CheckboxField('AlwaysIncludeSocialNetworkingLinks', 'Show links on every page (without the ability to turn them off on individual pages)'),
					$defaultSocialNetworkingCheckbox,
					new LiteralField('socialNetworkExtra', $socialNetworkExtra),
					$socialNetworkTableField
				)
			)
		);
		return $fields;
	}

	function CanEditShareIcons() {
		if(class_exists('DataObjectSorterDOD')) {
			$obj = singleton('ShareThisDataObject');
			if($obj->hasExtension('DataObjectSorterDOD')) {
				return true;
			}
			else {
				USER_ERROR('You have installed DataObjectSorterDOD, but you have not extended ShareThisDataObject with DataObjectSorterDOD, see sharethis/_config.php for more information.', E_USER_NOTICE);
			}
		}
		else {
			USER_ERROR('You need to install the DataObjectSorter module (see sharethis/README or sharethis/_config.php for more information)', E_USER_NOTICE);
		}
	}
}

