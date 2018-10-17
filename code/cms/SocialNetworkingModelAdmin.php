<?php

namespace SunnySideUp\ShareThis;

use SunnySideUp\ShareThis\SocialNetworkingLinksDataObject;
use SunnySideUp\ShareThis\ShareThisDataObject;
use SunnySideUp\ShareThis\FacebookFeed_Page;
use SunnySideUp\ShareThis\FacebookFeed_Item;
use SilverStripe\Admin\ModelAdmin;

/**
 *@author: nicolaas[at]sunnysideup.co.nz
 *@description: manages social networking data objects
 **/
class SocialNetworkingModelAdmin extends ModelAdmin
{
    private static $managed_models = array(
        SocialNetworkingLinksDataObject::class,
        ShareThisDataObject::class,
        FacebookFeed_Page::class,
        FacebookFeed_Item::class
    );

    private static $url_segment = 'social';

    private static $menu_title = 'Social Media';
}
