<?php

namespace SunnysideUp\ShareThis\Tasks;

use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DB;

/**
 * FacebookFeed_UpdateTask
 */
class FacebookFeedUpdateTask extends BuildTask
{
    /**
     * @var string
     */
    protected $title = 'Update Facebook News';

    /**
     * @var string
     */
    protected $description = 'Checks for updates on Facebook';

    public function run($request)
    {
        $facebookPages = FacebookFeedPage::get();

        if ($facebookPages->Count()) {
            foreach ($facebookPages as $facebookPage) {
                DB::alteration_message("Facebook page #{$facebookPage->ID} '{$facebookPage->Title}' updated", 'changed');
                $facebookPage->Fetch(true);
            }
        }

        echo 'COMPLETED';
    }
}
