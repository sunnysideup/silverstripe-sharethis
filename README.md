###############################################
Share This
###############################################

Adds a bunch of social media tricks to your
silverstripe website, such as
- share this page
- check us out (the website owner) on FB / TWITTER / etc
- show my latest tweet on my website
- share FB update on my website
etc...


Developer
-----------------------------------------------
Nicolaas Francken [at] sunnysideup.co.nz

Requirements
-----------------------------------------------
see composer.json
HIGHLY RECOMMENDED: dataobjectsorter:
- https://github.com/sunnysideup/silverstripe-dataobjectsorter
- http://sunny.svnrepository.com/svn/sunny-side-up-general/dataobjectsorter


Documentation
-----------------------------------------------
Please contact author for more details.

Any bug reports and/or feature requests will be
looked at in detail

We are also very happy to provide personalised support
for this module in exchange for a small donation.

The facebook RSS link format is like this https://www.facebook.com/feeds/page.php?format=rss20&id=
To find the id value, you can follow those steps :
1. Go to facebook
2. Find your page (e.g. https://www.facebook.com/EOSAsia)
3. Note the name (e.g. EOSAsia)
4. Go to http://findmyfacebookid.com
5. Enter http://www.facebook.com/EOSAsia
6. You'll get the answer (e.g. 357864420974239)
7. The result link is https://www.facebook.com/feeds/page.php?format=rss20&id=357864420974239


EXAMPLE OF HOW TO ADD FB FEED TO Page_Controller

	public function FacebookNews() {
		return FacebookFeed_Page::all_for_one_page($this->ID, 5);
	}

	protected function downloadFaceBookNews() {
		$facebookPages = DataObject::get("FacebookFeed_Page");
		if($facebookPages && $facebookPages->count()) {
			foreach($facebookPages as $facebookPage) {
				$facebookPage->Fetch();
			}
		}
	}

	function updatefb() {
		if(Permission::check('ADMIN')) {
			$this->downloadFaceBookNews();
			Director::redirect($this->Link());
			return array();
		}
		else {
			return Security::permissionFailure($this, _t('Security.PERMFAILURE',' This page is secured and you need administrator rights to access it. Enter your credentials below and we will send you right along.'));
		}
	}


Installation Instructions
-----------------------------------------------
1. Find out how to add modules to SS and add module as per usual.


2. Review configs and add entries to mysite/_config/config.yml
(or similar) as necessary.
In the _config/ folder of this module
you should to find some examples of config options (if any).

Add the following to your templates:

<% include ShareThis %>
<% include ShareAllExpandedList %>
<% include SocialNetworkingLinks %>

Thank you
-----------------------------------------------
This module is heavily based on the original
SS ShareThis module.



