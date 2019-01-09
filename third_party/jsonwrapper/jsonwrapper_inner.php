<?php

use SilverStripe\Control\Director;
use SunnysideUp\ShareThis\third_party\jsonwrapper\JSON\Services_JSON;

require_once(Director::baseFolder() . '/sapphire/thirdparty/json/JSON.php');

function json_encode($arg)
{
    global $services_json;
    if (!isset($services_json)) {
        $services_json = new Services_JSON();
    }
    return $services_json->encode($arg);
}

function json_decode($arg)
{
    global $services_json;
    if (!isset($services_json)) {
        $services_json = new Services_JSON();
    }
    return $services_json->decode($arg);
}
