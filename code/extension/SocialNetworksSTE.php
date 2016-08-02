<?php

/**
 * Add a field to each SiteTree object and it's subclasses to enable "follow us on ...", this can be a blog, twitter, facebook or whatever else.
 * it uses the SocialNetworkingLinksDataObject to get a list of icons.
 * @author nicolaas [at] sunnysideup.co.nz
 * @todo fix populateDefaults to make sure SiteConfig table is built first
 */
class SocialNetworksSTE extends SiteTreeExtension {

	/**
	 * Use the font-awesome icon collection?
	 * @var Boolean
	 */
	private static $use_font_awesome = false;

	/**
	 * list of sitetree extending classnames where
	 * the ShareThis functionality should be included
	 * @var Array
	 */
	private static $always_include_in = array();

	/**
	 * list of sitetree extending classnames where
	 * the ShareThis functionality should NEVER be included
	 * @var Array
	 */
	private static $never_include_in = array();

	private static $db = array(
		'HasSocialNetworkingLinks' => 'Boolean'
	);

	function updateCMSFields(FieldList $fields) {
		if($this->applyToOwnerClass()) {
			$config = $this->owner->getSiteConfig();
			if(! $config->AlwaysIncludeSocialNetworkingLinks) {
				$fields->addFieldToTab('Root.SocialMedia', new HeaderField('SocialNetworksHeader', 'Ask visitors to JOIN YOU on your social media'));
				$fields->addFieldToTab('Root.SocialMedia', new CheckboxField('HasSocialNetworkingLinks', 'Show Join Us on our Social Networks Links on this Page (e.g. follow us on Twitter) - make sure to specify social networking links!'));
			}
			$fields->addFieldToTab('Root.SocialMedia', new LiteralField('LinkToSiteConfigSocialMedia', "<p>There are more social media settings in the <a href=\"{$config->CMSEditLink()}\">Site Config</a>.</p>"));
		}
		return $fields;
	}

	function ShowSocialNetworks() {
		if($this->applyToOwnerClass()) {
			$config = $this->owner->getSiteConfig();
			if($config->AlwaysIncludeSocialNetworkingLinks) {
				return true;
			}
			return $this->owner->HasSocialNetworkingLinks;
		}
		return false;
	}

	function SocialNetworks() {
		Requirements::themedCSS('SocialNetworking', "sharethis");
		if(Config::inst()->get("SocialNetworksSTE", "use_font_awesome")) {
			Requirements::css("http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css");
		}
		return SocialNetworkingLinksDataObject::get();
	}

	private function applyToOwnerClass() {
		$always = Config::inst()->get("SocialNetworksSTE", "always_include_in");
		$never = Config::inst()->get("SocialNetworksSTE", "never_include_in");
		if(count($always) == 0 && count($never) == 0) {
			return true;
		}
		if(count($never) && count($always) == 0) {
			if(in_array($this->owner->ClassName, $never)) {
				return false;
			}
			return true;
		}
		if(count($always) && count($never) == 0) {
			if(in_array($this->owner->ClassName, $always)) {
				return true;
			}
			return false;
		}
		if(count($never) && count($always)) {
			if(in_array($this->owner->ClassName, $never)) {
				return false;
			}
			if(in_array($this->owner->ClassName, $always)) {
				return true;
			}
			//exception... if dev sets both always and never
			//then the ones not set will be included by default.
			return true;
		}
	}

}
