<?php

class RemoveFacebookItemController extends Controller
{

    private static $allowed_actions = array(
        'remove' => 'ADMIN'
    );

    private static $url_segment = 'removefacebooklink';

    function Link($action) {
        $urlSegment = Config::inst()->get('RemoveFacebookItemController', 'url_segment');
        return '/'.$urlSegment.'/'.$action;
    }

    function remove($request) {
        $uid = Convert::raw2sql($request->getParam('ID'));
        $item = FacebookFeed_Item::get()->filter(array("UID" => $uid));
        if($item) {
            $item->Hide = true;
            $item->write();
            $this->redirect('/?flush=all');
        }
    }
}
