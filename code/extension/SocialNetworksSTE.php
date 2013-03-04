<?php

/**
 * Add a field to each SiteTree object and it's subclasses to enable "follow us on ...", this can be a blog, twitter, facebook or whatever else.
 * it uses the SocialNetworkingLinksDataObject to get a list of icons.
 * @author nicolaas [at] sunnysideup.co.nz
 * @todo fix populateDefaults to make sure SiteConfig table is built first
 */
class SocialNetworksSTE extends SiteTreeExtension {

	static $db = array(
		'HasSocialNetworkingLinks' => 'Boolean'
	);

	function updateCMSFields(FieldList $fields) {
		$config = $this->owner->getSiteConfig();
		if(! $config->AlwaysIncludeSocialNetworkingLinks) {
			$fields->addFieldToTab('Root.SocialMedia', new CheckboxField('HasSocialNetworkingLinks', 'Show Social Networking Links on this Page (e.g. follow us on Twitter) - make sure to specify social networking links!'));
		}
		$fields->addFieldToTab('Root.SocialMedia', new LiteralField('LinkToSiteConfigSocialMedia', "<p>There  are more social media settings in the <a href=\"{$config->CMSEditLink()}\">Site Config</a>.</p>"));
		return $fields;
	}

	function ShowSocialNetworks() {
		$config = $this->owner->getSiteConfig();
		if($config->AlwaysIncludeSocialNetworkingLinks) {
			return true;
		}
		return $this->owner->HasSocialNetworkingLinks;
	}

	function SocialNetworks() {
		Requirements::themedCSS('SocialNetworking', "sharethis");
		return SocialNetworkingLinksDataObject::get();
	}

	function populateDefaults() {
		//$config = $this->owner->getSiteConfig();
		//$this->owner->HasSocialNetworkingLinks = $config->IncludeByDefaultSocialNetworkingLinks;
	}
}
