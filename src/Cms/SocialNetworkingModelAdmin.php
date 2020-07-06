<?php

namespace SunnysideUp\ShareThis\Cms;

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
        FacebookFeedPage::class,
        FacebookFeedItem::class,
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
