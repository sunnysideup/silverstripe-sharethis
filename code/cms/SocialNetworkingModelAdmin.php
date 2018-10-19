<?php

namespace SunnysideUp\ShareThis;

use SunnysideUp\ShareThis\SocialNetworkingLinksDataObject;
use SunnysideUp\ShareThis\ShareThisDataObject;
use SunnysideUp\ShareThis\FacebookFeed_Page;
use SunnysideUp\ShareThis\FacebookFeed_Item;
use SilverStripe\Admin\ModelAdmin;

/**
 *@author: nicolaas[at]sunnysideup.co.nz
 *@description: manages social networking data objects
 **/
class SocialNetworkingModelAdmin extends ModelAdmin
{
	/**
	 * @var array
	 */
    private static $managed_models = [
        SocialNetworkingLinksDataObject::class,
        ShareThisDataObject::class,
        FacebookFeed_Page::class,
        FacebookFeed_Item::class
    ];

    /**
     * @var string
     */
    private static $url_segment = 'social';

    /**
     * @var string
     */
    private static $menu_title = 'Social Media';
}
