<?php

namespace SunnysideUp\ShareThis;

use SunnysideUp\ShareThis\FacebookFeed_Page;
use SilverStripe\ORM\DB;
use SilverStripe\Dev\BuildTask;

/**
 * FacebookFeed_UpdateTask
 */
class FacebookFeed_UpdateTask extends BuildTask
{
    /**
     * @var string
     */
    protected $title = "Update Facebook News";

    /**
     * @var string
     */
    protected $description = "Checks for updates on Facebook";

    /**
     * @return  void
     */
    public function run($request)
    {
        $facebookPages = FacebookFeed_Page::get();

        if ($facebookPages->Count()) {
            foreach ($facebookPages as $facebookPage) {
                DB::alteration_message("Facebook page #{$facebookPage->ID} '$facebookPage->Title' updated", 'changed');
                $facebookPage->Fetch(true);
            }
        }

        echo "COMPLETED";
    }
}
