<?php
/**
 * Add a field to each SiteTree object and it's subclasses to enable "follow us on ...", this can be a blog, twitter, facebook or whatever else.
 * it uses the SocialNetworkingLinksDataObject to get a list of icons.
 * @author nicolaas [at] sunnysideup.co.nz
 *
 **/


class SocialNetworkingLinks extends SiteTreeDecorator {


	function extraStatics(){
		return array(
			'db' => array('HasSocialNetworkingLinks' => 'Boolean' )
		);
	}


	function updateCMSFields(FieldSet &$fields) {
		if(!$this->SiteConfig()->AlwaysIncludeSocialNetworkingLinks) {
			$fields->addFieldToTab("Root.Behaviour", new CheckboxField("HasSocialNetworkingLinks","Show Social Networking Links on this Page (e.g. follow us on Twitter) - make sure to specify social networking links!"));
		}
		$fields->addFieldToTab("Root.Behaviour", new LiteralField('LinkToSiteConfigSocialMedia', '<p>There  are more social media settings in the <a href="/admin/show/root/">Site Config</a></p>'));
		return $fields;
	}

	public function ThisPageHasSocialNetworkingLinks() {
		if($this->owner) {
			if($this->SiteConfig()->AlwaysIncludeSocialNetworkingLinks) {
				return true;
			}
			elseif(isset($this->owner->HasSocialNetworkingLinks)) {
				return $this->owner->HasSocialNetworkingLinks;
			}
		}
		return false;
	}

	public function SocialNetworkingLinksDataObjects(){
		if($this->ThisPageHasSocialNetworkingLinks()) {
			if($objects = DataObject::get("SocialNetworkingLinksDataObject")) {
				Requirements::themedCSS("SocialNetworking"); // ALSO added in template
				return $objects;
			}
		}
	}

	protected function SiteConfig(){
		return SiteConfig::current_site_config();
	}

	function populateDefaults() {
		if(DB::isActive()) {
			$this->owner->HasSocialNetworkingLinks = $this->SiteConfig()->IncludeByDefaultSocialNetworkingLinks;
		}
	}

}
