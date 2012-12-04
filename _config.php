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
//DataObject::add_extension('SiteConfig', 'SocialNetworkingConfig');

// --- SHARE THIS LINK -> links to your visitors page on facebook, linkedin, etc... so that they can share your website
//DataObject::add_extension('SiteTree', 'ShareThis');
//ShareThis::set_use_bw_effect(true);
//ShareThis::set_share_this_icons_to_include(array("facebook", "google", "linkedin"));   //OR
//ShareThis::set_share_this_icons_to_exclude(array("myspace"));

// --- SOCIAL NETWORKING LINK -> links to your page on facebook, linkedin, etc...
//DataObject::add_extension('SiteTree', 'SocialNetworkingLinks');

// --- SORTING -> allow the links to social media to be sortable
//optional//requires: http://sunny.svnrepository.com/svn/sunny-side-up-general/dataobjectsorter
//Object::add_extension('ShareThisDataObject', 'DataObjectSorterDOD');
//Object::add_extension('SocialNetworkingLinksDataObject', 'DataObjectSorterDOD');
//===================---------------- END sharethis MODULE ----------------===================
