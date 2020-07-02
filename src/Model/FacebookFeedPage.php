<?php

namespace SunnysideUp\ShareThis;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TreeMultiSelectField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\Security\Permission;

/**
 * FROM: http://www.acornartwork.com/blog/2010/04/19/tutorial-facebook-rss-feed-parser-in-pure-php/
 * EXAMPLE:
 *		//Run the function with the url and a number as arguments
 *		$fb = new TheFaceBook_communicator();
 *		$dos = $fb->fetchFBFeed('http://facebook.com/feeds/status.php?id=xxxxxx&viewer=xxxxxx&key=xxxxx&format=rss20', 3);
 *		//Print Facebook status updates
 *		echo '<ul class="fb-updates">';
 *			 foreach ($dos as $do) {
 *					echo '<li>';
 *					echo '<span class="update">' .$do->Description. '</span>';
 *					echo '<span class="date">' .$do->Date. '</span>';
 *					echo '<span class="link"><a href="' .$do->Link. '">more</a></span>';
 *					echo '</li>';
 *			 }
 *		echo '</ul>';
 *
 *  SEE README on getting facebook URL for RSS Feed.
 *
 *
 **/
class FacebookFeedPage extends DataObject
{
    /**
     * @var string
     */
    private static $table_name = 'FacebookFeedPage';

    /**
     * @var array
     */
    private static $db = [
        'Title' => 'Varchar(244)',
        'FacebookPageID' => 'Varchar(40)',
    ];

    /**
     * @var array
     */
    private static $has_many = [
        'Items' => FacebookFeedItem::class,
    ];

    /**
     * @var array
     */
    private static $many_many = [
        'Pages' => SiteTree::class,
    ];

    /**
     * @return boolean
     */
    public function canCreate($member = null, $context = [])
    {
        return Permission::checkMember($member, 'SOCIAL_MEDIA');
    }

    /**
     * @return boolean
     */
    public function canView($member = null)
    {
        return Permission::checkMember($member, 'SOCIAL_MEDIA');
    }

    /**
     * @return boolean
     */
    public function canEdit($member = null)
    {
        return Permission::checkMember($member, 'SOCIAL_MEDIA');
    }

    /**
     * @return boolean
     */
    public function canDelete($member = null)
    {
        return Permission::checkMember($member, 'SOCIAL_MEDIA');
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab(
            'Root.Main',
            LiteralField::create(
                'HowToFindPageID',
                "<p>
                To find the Facebook Page ID value, you can follow these steps :</p>
                <ol>
                    <li>Open a new tab and open <a href=\"http://www.facebook.com\" target=\"_blank\">facebook</a></li>
                    <li>Find your page (e.g. https://www.facebook.com/EOSAsia)</li>
                    <li>Note the name (e.g. EOSAsia)</li>
                    <li>Go to <a href=\"http://findmyfacebookid.com\" target=\"_blank\">http://findmyfacebookid.com</a></li>
                    <li>Enter http://www.facebook.com/EOSAsia</li>
                    <li>You'll get the answer (e.g. 357864420974239)</li>
                </ol>"
            )
        );

        $fields->addFieldToTab(
            'Root.Pages',
            TreeMultiSelectField::create('Pages', 'Show on', SiteTree::class)
        );

        $pages = $this->Pages();

        if ($pages && $pages->count()) {
            $links = [];

            foreach ($pages as $page) {
                $links[] = '<li><a href="' . $page->Link('updatefb') . '">' . $page->Title . '</a></li>';
            }

            if (count($links)) {
                $fields->addFieldToTab(
                    'Root.Pages',
                    LiteralField::create(
                        'LinksToCheck',
                        '<p>
                            Choose the links below to view your facebook feed:
                        <ol>
                            ' . implode('', $links) . '
                        </ol>'
                    )
                );
            }
        }

        return $fields;
    }

    /**
     * @param SiteTree | Int $page - page or page id
     * @param int $limit
     */
    public static function all_for_one_page($page, $limit = 10)
    {
        if ($page instanceof SiteTree) {
            $pageID = $page->ID;
        } else {
            $pageID = $page;
        }

        $feedIDs = [];

        $sql = "
            SELECT \"FacebookFeedPage_Pages\".\"FacebookFeedPageID\"
            FROM \"FacebookFeedPage_Pages\"
            WHERE \"FacebookFeedPage_Pages\".\"SiteTreeID\" = ${pageID}";

        $rows = DB::query($sql);

        if ($rows) {
            foreach ($rows as $row) {
                $feedIDs[$row['FacebookFeedPageID']] = $row['FacebookFeedPageID'];
            }
        }

        if (count($feedIDs)) {
            return FacebookFeedItem::get()->filter(
                [
                    'FacebookFeedPageID' => $feedIDs,
                    'Hide' => 0,
                ]
            )
                ->limit($limit);
        }
    }

    /**
     * ShowableItems
     * @param integer $limit
     */
    public function ShowableItems($limit = 10)
    {
        return $this->getComponents('Items', 'Hide = 0', null, '', $limit);
    }

    /**
     * Fetch
     * @param boolean $verbose
     */
    public function Fetch($verbose = false)
    {
        $count = 0;
        if ($this->FacebookPageID) {
            $items = SilverstripeFacebookConnector::get_feed($this->FacebookPageID);

            if ($items) {
                foreach ($items as $item) {
                    $filter = [
                        'UID' => $item['id'],
                    ];

                    if (! FacebookFeedItem::get()->filter($filter)->first()) {
                        $count++;
                        $message = '';

                        if (isset($item['message'])) {
                            $message = $item['message'];
                        } elseif (isset($item['description'])) {
                            $message = $item['description'];
                        }

                        //Converts UTF-8 into ISO-8859-1 to solve special symbols issues
                        $message = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $message);
                        $message = $this->stripUnsafe($message);

                        //Get status update time
                        $pubDate = strtotime(isset($item['created_time']) ? $item['created_time'] : 'today');

                        //Customize this to your liking
                        $convertedDate = gmdate($timeFormat = 'Y-m-d', $pubDate);

                        //Store values in array
                        $obj = FacebookFeedItem::create($filter);
                        $obj->Title = (string) (isset($item['name']) ? $item['name'] : '');
                        $obj->Date = $convertedDate;
                        $obj->Author = (string) (isset($item['from']['name']) ? $item['from']['name'] : '');
                        $obj->Link = (string) (isset($item['link']) ? $item['link'] : '');
                        $obj->PictureLink = (string) (isset($item['full_picture']) ? $item['full_picture'] : '');
                        $obj->Description = $message;
                        $obj->FacebookFeedPageID = $this->ID;
                        $obj->write();
                    }
                }
            } else {
                if ($verbose) {
                    DB::alteration_message('ERROR: no data returned', 'deleted');
                }
            }

            if ($count === 0 && $verbose) {
                DB::alteration_message('Nothing to add.');
            }
        } else {
            if ($verbose) {
                DB::alteration_message('ERROR: no Facebook Page ID provided', 'deleted');
            }
        }

        if ($count && $verbose) {
            DB::alteration_message("Added ${count} items", 'created');
        }
    }

    /**
     * stripUnsafe
     * @param  string $string
     *
     * @return string
     */
    public function stripUnsafe($string)
    {
        // Unsafe HTML tags that members may abuse
        $unsafe = [
            '/onmouseover="(.*?)"/is',
            '/onclick="(.*?)"/is',
            '/style="(.*?)"/is',
            '/target="(.*?)"/is',
            '/onunload="(.*?)"/is',
            '/rel="(.*?)"/is',
            '/<a(.*?)>/is',
            '/<\/a>/is',
        ];

        return preg_replace($unsafe, ' ', $string);
    }
}
