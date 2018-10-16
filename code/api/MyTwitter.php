<?php

namespace SunnySideUp\ShareThis;

use Object;








use SilverStripe\Control\Session;
use SilverStripe\Core\Config\Config;
use SunnySideUp\ShareThis\code\api\MyTwitter;
use SunnySideUp\ShareThis\code\model\MyTwitterData;
use SilverStripe\Control\Director;
use SunnySideUp\ShareThis\third_party\twitter_oauth\TwitterOAuth;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\View\ArrayData;



/**
 * @author romain [at] sunnys side up .co.nz + nicolaas [at] sunny side up . co .nz
 * @inspiration: https://github.com/tylerkidd/silverstripe-twitter-feed/
 * @funding: MSO Design (www.msodesign.com)
 *
 **/

class MyTwitter extends Object
{
    private static $debug = false;

    private static $singletons = array();

    private static $favourites_only = false;

    private static $non_replies_only = false;

    private static $twitter_consumer_key = "";

    private static $twitter_consumer_secret = "";

    private static $titter_oauth_token = "";

    private static $titter_oauth_token_secret = "";

    private static $twitter_config = array(
        'include_entities' => 'true',
        'include_rts' => 'true'
    );

    /**
     * returns a DataObjetSet of the last $count tweets.
     * - saves twitter feed to dataobject
     *
     * @param String $username (e.g. mytwitterhandle)
     * @param Int $count - number of tweets to retrieve at any one time
     * @return DataObjectSet | Null
     */
    public static function last_statuses($username, $count = 1, $useHourlyCache = true)
    {
        if (!$username) {
            user_error("No username provided");
        }
        $sessionName = "MyTwitterFeeds$username".date("Ymdh");
        if (Session::get($sessionName) && $useHourlyCache && !Config::inst()->get(MyTwitter::class, "debug")) {
            //do nothing
        } else {
            if (empty(self::$singletons[$username])) {
                self::$singletons[$username] = new MyTwitter($username, $count);
            }
            $dataObjectSet = self::$singletons[$username]->TwitterFeed($username, $count);
            if ($dataObjectSet && $dataObjectSet->count()) {
                foreach ($dataObjectSet as $tweet) {
                    if (!MyTwitterData::get()->filter(array("TwitterID" => $tweet->ID))->count()) {
                        $myTwitterData = new MyTwitterData();
                        $myTwitterData->TwitterID = $tweet->ID;
                        $myTwitterData->Title = $tweet->Title;
                        $myTwitterData->Date = $tweet->Date;
                        $myTwitterData->write();
                    }
                }
            }
            Session::set($sessionName, 1);
        }
        Config::inst()->update(MyTwitterData::class, "username", $username);
        return MyTwitterData::get()->filter(array("Hide" => 0))->limit($count);
    }


    /**
     * retries latest tweets from Twitter
     *
     * @param String $username (e.g. mytwitterhandle)
     * @param Int $count - number of tweets to retrieve at any one time
     * @return DataObjectSet | Null
     */
    public function TwitterFeed($username, $count = 5)
    {
        if (!$username) {
            user_error("No username provided");
        }
        Config::inst()->update(MyTwitterData::class, "username", $username);
        //check settings are available
        $requiredSettings = array("twitter_consumer_key", "twitter_consumer_secret", "titter_oauth_token", "titter_oauth_token");
        foreach ($requiredSettings as $setting) {
            if (!Config::inst()->get(MyTwitter::class, $setting)) {
                user_error(" you must set MyTwitter::$setting", E_USER_NOTICE);
                return null;
            }
        }
        require_once(Director::baseFolder().'/'.SS_SHARETHIS_DIR.'/third_party/twitter_oauth/TwitterOAuthConsumer.php');
        $connection = new TwitterOAuth(
            Config::inst()->get(MyTwitter::class, "twitter_consumer_key"),
            Config::inst()->get(MyTwitter::class, "twitter_consumer_secret"),
            Config::inst()->get(MyTwitter::class, "titter_oauth_token"),
            Config::inst()->get(MyTwitter::class, "titter_oauth_token_secret")
        );
        $config = Config::inst()->get(MyTwitter::class, "twitter_config");
        $config['screen_name'] = $username;
        $tweets = $connection->get('statuses/user_timeline', $config);
        $tweetList = new ArrayList();
        if (count($tweets) > 0 && !isset($tweets->error)) {
            $i = 0;
            foreach ($tweets as $tweet) {
                if (Config::inst()->get(MyTwitter::class, "favourites_only") && $tweet->favorite_count == 0) {
                    break;
                }
                if (Config::inst()->get(MyTwitter::class, "non_replies_only") && $tweet->in_reply_to_status_id) {
                    break;
                }
                if (Config::inst()->get(MyTwitter::class, "debug")) {
                    print_r($tweet);
                }
                if (++$i > $count) {
                    break;
                }

                $date = new DBDatetime();
                $date->setValue(strtotime($tweet->created_at));
                $text = htmlentities($tweet->text, ENT_NOQUOTES, $encoding = "UTF-8", $doubleEncode = false);
                if (!empty($tweet->entities) && !empty($tweet->entities->urls)) {
                    foreach ($tweet->entities->urls as $url) {
                        if (!empty($url->url) && !empty($url->display_url)) {
                            $text = str_replace($url->url, '<a href="'.$url->url.'" class="external">'.$url->display_url.'</a>', $text);
                        }
                    }
                }
                $tweetList->push(
                    new ArrayData(array(
                        'ID' => $tweet->id_str,
                        'Title' => $text,
                        'Date' => $date
                    ))
                );
            }
        }
        return $tweetList;
    }
}
