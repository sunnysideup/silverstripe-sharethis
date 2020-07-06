<?php

namespace SunnysideUp\ShareThis\Control;

use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Convert;

/**
 * RemoveFacebookItemController
 */
class RemoveFacebookItemController extends Controller
{
    /**
     * @var array
     */
    private static $allowed_actions = [
        'remove' => 'ADMIN',
    ];

    /**
     * @var string
     */
    private static $url_segment = 'removefacebooklink';

    /**
     * Link
     *
     * @return  string
     */
    public function Link($action = null)
    {
        $urlSegment = Config::inst()->get(RemoveFacebookItemController::class, 'url_segment');
        return '/' . $urlSegment . '/' . $action;
    }

    /**
     * remove
     */
    public function remove($request)
    {
        $uid = Convert::raw2sql($request->param('ID'));
        $item = FacebookFeedItem::get()->filter(['UID' => $uid])->first();
        if ($item) {
            $item->Hide = true;
            $item->write();
            $this->redirect('/?flush=all');
        }
    }
}
