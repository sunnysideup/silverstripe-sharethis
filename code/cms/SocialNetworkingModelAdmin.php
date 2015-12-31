<?php
/**
 *@author: nicolaas[at]sunnysideup.co.nz
 *@description: manages social networking data objects
 **/

class SocialNetworkingModelAdmin extends ModelAdmin
{

    private static $managed_models = array(
        "SocialNetworkingLinksDataObject",
        "ShareThisDataObject",
        "FacebookFeed_Page",
        "FacebookFeed_Item"
    );

    private static $url_segment = 'social';

    private static $menu_title = 'Social Media';
}
