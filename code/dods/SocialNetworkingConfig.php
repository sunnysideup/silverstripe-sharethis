<?php


class SocialNetworkingConfig extends DataObjectDecorator {

	function extraStatics(){
		return array(
			'db' => array(
				'AlwaysIncludeShareThisLinks' => 'Boolean',
				'AlwaysIncludeSocialNetworkingLinks' => 'Boolean',
				'IncludeByDefaultShareThisLinks' => 'Boolean',
				'IncludeByDefaultSocialNetworkingLinks' => 'Boolean',
				'ShareThisAllInOne' => 'Boolean'
			)
		);
	}

	function updateCMSFields(&$fields) {
		$shareThisExtra = '<h3 style="margin-top: 50px">Select Icons</h3>';
		if($this->CanEditShareIcons()) {
			$addedLinks = array();
			$obj = singleton("ShareThisDataObject");
			$addedLinksShort["edit"] = DataObjectOneFieldUpdateController::popup_link("ShareThisDataObject", "IncludeThisIcon");
			$addedLinksLong["edit"] = DataObjectOneFieldUpdateController::popup_link("ShareThisDataObject", "IncludeThisIconInExtendedList");
			$addedLinksShort["sort"] = $obj->dataObjectSorterPopupLink("IncludeThisIcon", 1);
			$addedLinksLong["sort"] = $obj->dataObjectSorterPopupLink("IncludeThisIconInExtendedList", 1);
			if(count($addedLinksShort)) {
				$shareThisExtra .= '<p>short list: '.implode(", ",$addedLinksShort).'.</p>';
			}
			if(count($addedLinksLong)) {
				$shareThisExtra .= '<p>long list: '.implode(", ",$addedLinksLong).'.</p>';
			}
		}		
		$shareThisTableField = new ComplexTableField($this->owner, $name = "ShareThisDataObject", $sourceClass = "ShareThisDataObject");
		$shareThisTableField->setPermissions(array("edit", "add"));
		$socialNetworkExtra = '<h3 style="margin-top: 50px">Add / Edit / Delete Your Social Networking Home Pages (e.g. www.facebook.com/our-company-page)</h3>';
		$socialNetworkTableField = new ComplexTableField($this->owner, $name = "SocialNetworkingLinksDataObject", $sourceClass = "SocialNetworkingLinksDataObject");
		$socialNetworkTableField->setPermissions(array("edit", "add", "delete", "view"));
		if($this->owner->AlwaysIncludeShareThisLinks) {
			$defaultShareThisCheckbox = new HiddenField("IncludeByDefaultShareThisLinks", "1");
		}
		else {
			$defaultShareThisCheckbox = new CheckboxField("IncludeByDefaultShareThisLinks", "Show links on every page by default (with the ability to turn them off on invididual pages)");
		}
		if($this->owner->AlwaysIncludeSocialNetworkingLinks) {
			$defaultSocialNetworkingCheckbox = new HiddenField("IncludeByDefaultSocialNetworkingLinks", "1");
		}
		else {
			$defaultSocialNetworkingCheckbox = new CheckboxField("IncludeByDefaultSocialNetworkingLinks", "Include on every page by default (with the ability to turn them off on individual pages)");
		}
		$fields->addFieldToTab(
			"Root.SocialMedia", new TabSet(
				"SocialNetworkingOptions",
				new Tab(
					"ShareThis",
					new CheckboxField("AlwaysIncludeShareThisLinks", "Show links on every page (without the ability to turn them off on individual pages)"),
					$defaultShareThisCheckbox,
					new CheckboxField("ShareThisAllInOne", "Add a 'share' all-in-one button"),
					new LiteralField("shareThisExtra", $shareThisExtra),
					$shareThisTableField
				),
				new Tab(
					"SocialNetworkingLink",
					new CheckboxField("AlwaysIncludeSocialNetworkingLinks", "Show links on every page (without the ability to turn them off on individual pages)"),
					$defaultSocialNetworkingCheckbox,
					new LiteralField("socialNetworkExtra", $socialNetworkExtra),
					$socialNetworkTableField
				)
			)
		);
		return $fields;
	}


	public function CanEditShareIcons() {
		if(class_exists("DataObjectSorterDOD")) {
			$obj = singleton("ShareThisDataObject");
			if($obj->hasExtension("DataObjectSorterDOD")) {
				return true;
			}
			else {
				USER_ERROR("you have installed DataObjectSorterDOD, but you have not extended ShareThisDataObject with DataObjectSorterDOD, see sharethis/_config.php for more information.", E_USER_NOTICE);
			}
		}
		else {
			USER_ERROR("you need to install the DataObjectSorter module (see readme / _config.php file for more information)", E_USER_NOTICE);
		}
	}

	private $AlwaysIncludeShareThisLinksBefore, $AlwaysIncludeSocialNetworkingLinksBefore;

	function onBeforeWrite() {
		$siteConfig = DataObject::get_by_id("SiteConfig", $this->owner->ID);
		if($siteConfig) {
			$this->AlwaysIncludeShareThisLinksBefore  = $siteConfig->AlwaysIncludeShareThisLinks;
			$this->AlwaysIncludeSocialNetworkingLinksBefore = $this->owner->AlwaysIncludeSocialNetworkingLinks;
		}
	}

	//to do, check if / why this is (not) working.
	function onAfterWrite() {
		if($this->owner->isChanged("AlwaysIncludeShareThisLinks") || $this->owner->isChanged("AlwaysIncludeSocialNetworkingLinks")) {
			LeftAndMain::forceReload();
		}
			
	}

}

