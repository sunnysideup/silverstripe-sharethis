<?php

namespace SunnySideUp\ShareThis;

use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\Debug;

/**
 * https://developers.facebook.com/tools-and-support/
 */
class SilverstripeFacebookConnector
{
    use Injectable;

    /**
     * @var Facebook Connection
     */
    private static $connection = null;

    /**
     * settings for connection
     * @var array
     */
    private static $connection_config = array();

    /**
     * application ID - get from FB
     * @var string
     */
    private static $app_id = "";

    /**
     * application secret - get from FB
     * @var string
     */
    private static $app_secret = "";


    /**
     * debug
     * @var boolean
     */
    protected static $debug = false;

    /**
     * keep track of errors
     * @var array
     */
    protected static $error = array();

    /**
     * set additional connection details - e.g. default_access_token
     * @param array
     */
    public static function set_connection_config($connectionConfig)
    {
        self::$connection_config = $connectionConfig;
    }

    /**
     * create FB connection...
     * @return Facebook\Facebook
     */
    protected static function get_connection()
    {
        if (!self::$connection) {
            self::$connection_config += array(
                'app_id' => Config::inst()->get(SilverstripeFacebookConnector::class, "app_id"),
                'app_secret' => Config::inst()->get(SilverstripeFacebookConnector::class, "app_secret"),
                'default_graph_version' => 'v2.4',
                //'default_access_token' => '{access-token}', // optional
            );

            self::$connection = new Facebook\Facebook(self::$connection_config);
        }
        return self::$connection;
    }

    /**
     *
     * @param string $openGraphCommand
     *
     * @return FacebookResponse | false
     */
    public static function run_command($openGraphCommand = "")
    {
        $fb = self::get_connection();
        $accessToken = Config::inst()->get(SilverstripeFacebookConnector::class, "app_id")."|".Config::inst()->get(SilverstripeFacebookConnector::class, "app_secret");
        //$helper = $fb->getPageTabHelper();
        try {
            $response = $fb->get($openGraphCommand, $accessToken);
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            self::$error[] = 'Graph returned an error: ' . $e->getMessage();
            return false;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            self::$error[] = 'Facebook SDK returned an error: ' . $e->getMessage();
            return false;
        }
        if (self::$debug) {
            Debug::log(implode(" | ", self::$error));
        }
        return $response;
    }

    /**
     * @return details about logged in person
     */
    public static function whoami()
    {
        $response = self::run_command("/me");
        if ($response) {
            return $response->getGraphUser();
        }
    }


    /**
     * returns an array of recent posts for a page
     * @return array
     */
    public static function get_feed($pageID)
    {
        $response = self::run_command($pageID . "/posts?fields=message,created_time,id,full_picture,link,from,name,description");
        if ($response) {
            $list = $response->getDecodedBody();
            if (isset($list["data"])) {
                return $list["data"];
            }
        }
    }

    /**
     * returns an array of recent posts for a page
     * @return array
     */
    public static function check_if_posts_exists($UID)
    {
        $response = self::run_command('/Post/'.$UID);
        print_r($response);
    }
}
