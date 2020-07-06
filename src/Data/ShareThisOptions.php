<?php

namespace SunnysideUp\ShareThis\Data;

use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Dev\Debug;

/**
 * @author nicolaas [at] sunnysideup.co.nz
 */
class ShareThisOptions
{
    use Injectable;

    /**
     * @var array
     */
    private static $page_specific_data;

    /**
     * @var general_data
     */
    private static $general_data;

    /**
     * @var share_all_data
     */
    private static $share_all_data;

    /**
     * @var non_encoded_page_url
     */
    private static $non_encoded_page_url;

    /**
     * @var encoded_page_url
     */
    private static $encoded_page_url;

    /**
     * @var encoded_page_title
     */
    private static $encoded_page_title;

    /**
     * @var encoded_page_title_space_encoded
     */
    private static $encoded_page_title_space_encoded;

    /**
     * @var encoded_description
     */
    private static $encoded_description;

    /**
     * @var icon
     */
    private static $icon;

    /**
     * Get's all options
     *
     * @return array
     */
    public static function get_all_options($title, $link, $description)
    {
        self::set_variables($title, $link, $description);
        self::$page_specific_data = [
            'email' => [
                'url' => 'mailto:?' . htmlentities('Subject=' . self::$encoded_page_title . '&Body=' . self::$encoded_description . '%0D%0A' . self::$encoded_page_url),
                'faicon' => 'fa-send',
                'title' => 'Email',
            ],
            'print' => [
                'url' => '#',
                'faicon' => 'fa-print',
                'click' => 'window.print(); return false;',
                'title' => 'Print',
            ],
            'favourites' => [
                'url' => '#',
                'faicon' => 'fa-bookmark',
                'click' => "sharethis.bookmark('" . self::$encoded_page_url . "', '" . self::$encoded_page_title . "'); return false;",
                'title' => 'Add to favourites (Internet Explorer Only)',
            ],
            'delicious' => [
                'url' => 'http://del.icio.us/post?' . htmlentities('url=' . self::$encoded_page_url . '&title=' . self::$encoded_page_title),
                'faicon' => 'fa-delicious',
                'title' => 'Delicious',
            ],
            'facebook' => [
                'url' => 'http://www.facebook.com/share.php?' . htmlentities('u=' . self::$encoded_page_url . '&title=' . self::$encoded_page_title),
                'faicon' => 'fa-facebook-square',
                'title' => 'Facebook',
            ],
            'googleplus' => [
                'url' => 'https://plus.google.com/share?url=' . self::$encoded_page_url,
                'faicon' => 'fa-google-plus',
                'title' => 'Google Plus One',
            ],
            'linkedin' => [
                'url' => 'http://www.linkedin.com/shareArticle?' . htmlentities('mini=true&url=' . self::$encoded_page_url . '&title=' . self::$encoded_page_title . '&source=' . Director::absoluteBaseURL()),
                'faicon' => 'fa-linkedin-square',
                'title' => 'LinkedIn',
            ],
            'pinterest' => [
                'url' => 'http://pinterest.com/pin/create/bookmarklet/?' . htmlentities('media=html&url=' . self::$encoded_page_url . '&is_video=false&description=' . self::$encoded_page_title),
                'faicon' => 'fa-pinterest',
                'title' => 'Pinterest',
            ],
            'reddit' => [
                'url' => 'http://reddit.com/submit?' . htmlentities('url=' . self::$encoded_page_url . '&title=' . self::$encoded_page_title),
                'faicon' => 'fa-reddit',
                'title' => 'Reddit',
            ],
            'twitter' => [
                'url' => 'http://twitter.com/home?status=' . htmlentities(urlencode('currently reading: ') . self::$encoded_page_url),
                'faicon' => 'fa-twitter-square',
                'title' => 'Twitter',
            ],
            'tumblr' => [
                'url' => 'http://www.tumblr.com/share/link?url=' . htmlentities(self::$encoded_page_url . '&name=' . self::$encoded_page_title),
                'faicon' => 'fa-tumblr-square',
                'title' => 'Tumblr',
            ],
        ];

        return self::$page_specific_data;
    }

    /**
     * @param  string $title
     * @param  string $link
     * @param  string $description
     *
     * @return array
     */
    public static function get_page_specific_data($title, $link, $description = '')
    {
        $originalArray = self::$page_specific_data ?: self::get_all_options($title, $link, $description);
        $inc = Config::inst()->get(ShareThisSTE::class, 'included_icons');
        $exc = Config::inst()->get(ShareThisSTE::class, 'excluded_icons');

        if (count($inc)) {
            $new_array_of_icons_to_include = [];

            foreach ($inc as $key => $value) {
                $new_array_of_icons_to_include[$value] = $value;

                if (! isset($originalArray[$value])) {
                    Debug::show("Error in ShareIcons::set_icons_to_include, ${key} does not exist in bookmark list");
                }
            }

            foreach (array_keys($originalArray) as $key) {
                if (! isset($new_array_of_icons_to_include[$key])) {
                    unset($originalArray[$key]);
                }
            }
        }

        //which ones do we exclude
        if (count($exc)) {
            foreach ($exc as $key) {
                if (! isset($originalArray[$key])) {
                    Debug::show("Error in ShareIcons::set_icons_to_exclude, ${key} does not exist in bookmark list");
                } else {
                    unset($originalArray[$key]);
                }
            }
        }

        if (! $link) {
            self::$page_specific_data = null;
        }

        return $originalArray;
    }

    /*
        summary: (required) utf-8 string, defaults to document.title
        content: (optional) utf-8 string, defaults to null
        updated: (optional) ISO 8601 date, defaults to document.lastModified
        published: (optional) ISO 8601 date, defaults to null
        author: currently not implemented
        category: currently not implemented
    */
    public static function get_share_all()
    {
        self::$share_all_data = '
            <script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#&amp;type=website"></script>
            <script type="text/javascript">
                SHARETHIS.addEntry(
                    {
                        title:"' . urldecode(self::$encoded_page_title) . '",
                        summary:"' . urldecode(self::$encoded_page_title) . '",
                        url:"' . urldecode(self::$encoded_page_url) . '",
                        icon:"' . urldecode(self::$icon) . '"
                    },
                    {button:true}
                );
            </script>';

        return self::$share_all_data;
    }

    /**
     * Sets general data
     */
    public static function set_general_data()
    {
        self::$general_data = null;
    }

    /**
     * Get's generic data
     */
    public static function get_general_data()
    {
        if (! self::$general_data) {
            $array = self::get_page_specific_data('', '', '');
            $newArray = [];
            if (count($array)) {
                foreach (array_keys($array) as $key) {
                    $newArray[$key] = $key;
                }
            }
            self::$general_data = $newArray;
        }

        return self::$general_data;
    }

    /**
     * @return string
     */
    protected function facebookLike()
    {
        //see http://developers.facebook.com/docs/reference/plugins/like/
        return '<div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#appId=231498950207168&amp;xfbml=1"></script><fb:like href="www.test.com" send="false" width="450" show_faces="true" font="lucida grande"></fb:like>';
    }

    /**
     * Set's variables
     * @param string $title
     * @param string $link
     * @param string $description
     */
    private static function set_variables($title, $link, $description)
    {
        self::$icon = urlencode(Director::absoluteBaseURL() . 'favicon.ico');
        self::$non_encoded_page_url = Director::absoluteURL($link);
        self::$encoded_page_url = urlencode(self::$non_encoded_page_url);
        self::$encoded_page_title = urlencode($title);
        self::$encoded_page_title_space_encoded = str_replace('+', '%20', urlencode($title));
        if ($description) {
            self::$encoded_description = urlencode($description);
        } else {
            self::$encoded_description = self::$encoded_page_title;
        }
    }
}
