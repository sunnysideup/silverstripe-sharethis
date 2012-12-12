<?php

/**
 * based on the Silverstripe Module
 * developed by www.sunnysideup.co.nz
 * author: Nicolaas - modules [at] sunnysideup.co.nz
 *
 **/

define('SS_SHARETHIS_DIR', 'sharethis');

//copy the lines between the START AND END line to your /mysite/_config.php file and choose the right settings
//===================---------------- START sharethis MODULE ----------------===================
// --- CONFIG -> REQUIRED - configure your social media from the site config panel
//Object::add_extension('SiteConfig', 'ShareThisSiteConfigDE');

// --- SHARE THIS LINK -> links to your visitors page on facebook, linkedin, etc... so that they can share your website
//Object::add_extension('SiteTree', 'ShareThisSTE');
//ShareThisSTE::use_bw_effect = true;
//ShareThisSTE::set_included_icons(array('facebook', 'google', 'linkedin'));   //OR
//ShareThisSTE::set_excluded_icons(array('myspace'));

// --- SOCIAL NETWORKING LINK -> links to your page on facebook, linkedin, etc...
//Object::add_extension('SiteTree', 'SocialNetworksSTE');

// --- SORTING -> allow the links to social media to be sortable
//optional//requires: http://sunny.svnrepository.com/svn/sunny-side-up-general/dataobjectsorter
//Object::add_extension('ShareThisDataObject', 'DataObjectSorterDOD');
//Object::add_extension('SocialNetworkingLinksDataObject', 'DataObjectSorterDOD');
//===================---------------- END sharethis MODULE ----------------===================
