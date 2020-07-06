<?php

namespace SunnysideUp\ShareThis\Model;

use HtmlEditorField;
use SilverStripe\Control\Director;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\Filters\PartialMatchFilter;
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
class FacebookFeedItem extends DataObject
{
    /**
     * @var string
     */
    private static $table_name = 'FacebookFeedItem';

    /**
     * @var array
     */
    private static $db = [
        'Title' => 'Varchar(255)',
        'KeepOnTop' => 'Boolean',
        'Hide' => 'Boolean',
        'UID' => 'Varchar(32)',
        'Author' => 'Varchar(244)',
        'Description' => 'HTMLText',
        'DescriptionWithShortLink' => 'HTMLText',
        'Link' => 'Varchar(244)',
        'PictureLink' => 'Text',
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'Created.Nice' => 'Created',
        'FacebookFeedPage.Title' => 'Feed',
        'Title' => 'Title',
        'KeepOnTopNice' => 'Keep on top',
        'HideNice' => 'Hide',
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'FacebookFeedPage' => FacebookFeedPage::class,
    ];

    /**
     * @var array
     */
    private static $indexes = [
        'UID' => true,
    ];

    /**
     * @var array
     */
    private static $casting = [
        'KeepOnTopNice' => 'Varchar',
        'HideNice' => 'Varchar',
        'FacebookPostLink' => 'Varchar',
    ];

    /**
     * @var array
     */
    private static $searchable_fields = [
        'Title' => PartialMatchFilter::class,
        'Author' => PartialMatchFilter::class,
        'Description' => PartialMatchFilter::class,
    ];

    /**
     * @var string
     */
    private static $singular_name = 'Facebook Item';

    /**
     * @var string
     */
    private static $plural_name = 'Facebook Items';

    /**
     * @var string
     */
    private static $default_sort = '"Created" DESC';

    /**
     * @return boolean
     */
    public function canCreate($member = null, $context = [])
    {
        return false;
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
        return false;
    }

    /**
     * @return string
     */
    public function i18n_singular_name()
    {
        return 'Facebook Item';
    }

    /**
     * @return string
     */
    public function i18n_plural_name()
    {
        return 'Facebook Items';
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $this->DescriptionWithShortLink = $this->Description;
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('UID');
        $fields->removeByName('PictureLink');

        if ($this->PictureLink) {
            $fields->addFieldToTab('Root.Main', LiteralField::create('PictureLinkIMG', '<img src="' . $this->PictureLink . '" alt="" />'), 'Author');
        }

        if ($this->Link) {
            $fields->addFieldToTab('Root.Main', LiteralField::create('LinkLink', '<h2><a href="' . $this->Link . '" >go to link final link: ' . substr($this->Link, 0, 45) . '...</a></h2>'), 'Author');
            $fields->addFieldToTab('Root.Main', LiteralField::create('LinkLink', '<h2><a href="' . $this->getFacebookPostLink() . '" >go to face book post: ' . substr($this->getFacebookPostLink(), 0, 45) . '...</a></h2>'), 'Author');
            $fields->addFieldToTab('Root.RawData', TextField::create('Link', 'Link'));
        }

        if ($this->Description) {
            $fields->addFieldToTab('Root.RawData', HtmlEditorField::create('Description'));
            $fields->addFieldToTab('Root.Main', HtmlEditorField::create('DescriptionWithShortLink', 'Edited Description'));
        }

        return $fields;
    }

    /**
     * KeepOnTopNice
     */
    public function KeepOnTopNice()
    {
        return $this->dbObject('KeepOnTop')->Nice();
    }

    /**
     * HideNice
     */
    public function HideNice()
    {
        return $this->dbObject('Hide')->Nice();
    }

    /**
     * @return string
     */
    public function getFacebookPostLink()
    {
        return 'https://facebook.com/' . $this->UID;
    }

    /**
     * is the link attached to the FB post a link back to this site?
     *
     * @return bool
     */
    public function IsLinkBackToSite()
    {
        $currentURL = Director::absoluteBaseURL();
        if (strpos($this->Link, $currentURL) === false) {
            return false;
        }
        return true;
    }

    /**
     * returns a link back to the same site if that is what the FB post links to
     * or a link to FB if it ultimately links to third-party site.
     *
     * @return strring
     */
    public function SmartLink()
    {
        if ($this->IsLinkBackToSite()) {
            return $this->Link;
        }
        return $this->getFacebookPostLink();
    }

    /**
     * Check whether Facebook post exists
     */
    public function fbpostExists()
    {
        $exists = true;
        $handle = curl_init($this->getFacebookPostLink());
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        /* Get the HTML or whatever is linked in $url. */
        curl_exec($handle);

        /* Check for 404 (file not found). */
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        if ($httpCode !== 200 && $httpCode !== 301) {
            $exists = false;
        }

        curl_close($handle);

        return $exists;
    }

    /**
     * @return boolean
     */
    public function canRemove()
    {
        return Permission::check('CMS_ACCESS_CMSMain') ? true : false;
    }

    /**
     * Remove the link
     *
     * @return string
     */
    public function RemoveLink()
    {
        $obj = Injector::inst()->get(RemoveFacebookItemController::class);

        return $obj->Link('remove/' . $this->UID . '/');
    }

    protected function createDescriptionWithShortLinks()
    {
        user_error('Needs to be upgraded');
        // require_once(Director::baseFolder()."/".SS_SHARETHIS_DIR.'/code/api/thirdparty/simple_html_dom.php');
        // $html = str_get_html($this->Description);
        if ($html) {
            foreach ($html->find('text') as $element) {
                //what exactly does it do?
                if (! in_array($element->parent()->tag, ['a', 'img'], true)) {
                    $element->innertext = $this->replaceLinksWithProperOnes($element->innertext);
                }
            }
        } else {
            $this->Hide = true;
            $this->write();
        }
    }

    /**
     * @param  string $text
     *
     * @return string
     */
    protected function replaceLinksWithProperOnes($text)
    {
        $rexProtocol = '(https?://)?';
        $rexDomain = '((?:[-a-zA-Z0-9]{1,63}\.)+[-a-zA-Z0-9]{2,63}|(?:[0-9]{1,3}\.){3}[0-9]{1,3})';
        $rexPort = '(:[0-9]{1,5})?';
        $rexPath = '(/[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]*?)?';
        $rexQuery = '(\?[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
        $rexFragment = '(#[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
        $outcome = '';
        $validTlds = array_fill_keys(explode(' ', '.aero .asia .biz .cat .com .coop .edu .gov .info .int .jobs .mil .mobi .museum .name .net .org .pro .tel .travel .ac .ad .ae .af .ag .ai .al .am .an .ao .aq .ar .as .at .au .aw .ax .az .ba .bb .bd .be .bf .bg .bh .bi .bj .bm .bn .bo .br .bs .bt .bv .bw .by .bz .ca .cc .cd .cf .cg .ch .ci .ck .cl .cm .cn .co .cr .cu .cv .cx .cy .cz .de .dj .dk .dm .do .dz .ec .ee .eg .er .es .et .eu .fi .fj .fk .fm .fo .fr .ga .gb .gd .ge .gf .gg .gh .gi .gl .gm .gn .gp .gq .gr .gs .gt .gu .gw .gy .hk .hm .hn .hr .ht .hu .id .ie .il .im .in .io .iq .ir .is .it .je .jm .jo .jp .ke .kg .kh .ki .km .kn .kp .kr .kw .ky .kz .la .lb .lc .li .lk .lr .ls .lt .lu .lv .ly .ma .mc .md .me .mg .mh .mk .ml .mm .mn .mo .mp .mq .mr .ms .mt .mu .mv .mw .mx .my .mz .na .nc .ne .nf .ng .ni .nl .no .np .nr .nu .nz .om .pa .pe .pf .pg .ph .pk .pl .pm .pn .pr .ps .pt .pw .py .qa .re .ro .rs .ru .rw .sa .sb .sc .sd .se .sg .sh .si .sj .sk .sl .sm .sn .so .sr .st .su .sv .sy .sz .tc .td .tf .tg .th .tj .tk .tl .tm .tn .to .tp .tr .tt .tv .tw .tz .ua .ug .uk .us .uy .uz .va .vc .ve .vg .vi .vn .vu .wf .ws .ye .yt .yu .za .zm .zw .xn--0zwm56d .xn--11b5bs3a9aj6g .xn--80akhbyknj4f .xn--9t4b11yi5a .xn--deba0ad .xn--g6w251d .xn--hgbk6aj7f53bba .xn--hlcj6aya9esc7a .xn--jxalpdlp .xn--kgbechtv .xn--zckzah .arpa'), true);

        $position = 0;
        while (preg_match("{\\b${rexProtocol}${rexDomain}${rexPort}${rexPath}${rexQuery}${rexFragment}(?=[?.!,;:\"]?(\s|$))}", $text, $match, PREG_OFFSET_CAPTURE, $position)) {
            list($url, $urlPosition) = $match[0];

            // Print the text leading up to the URL.
            $outcome .= htmlspecialchars(substr($text, $position, $urlPosition - $position));

            $domain = $match[2][0];
            $port = $match[3][0];
            $path = $match[4][0];

            // Check if the TLD is valid - or that $domain is an IP address.
            $tld = strtolower(strrchr($domain, '.'));
            if (preg_match('{\.[0-9]{1,3}}', $tld) || isset($validTlds[$tld])) {
                // Prepend http:// if no protocol specified
                $completeUrl = $match[1][0] ? $url : "http://${url}";

                // Print the hyperlink.
                $outcome .= sprintf('<a href="%s">%s</a>', htmlspecialchars($completeUrl), htmlspecialchars("${domain}${port}${path}"));
            } else {
                // Not a valid URL.
                $outcome .= htmlspecialchars($url);
            }

            // Continue text parsing from after the URL.
            $position = $urlPosition + strlen($url);
        }

        // Print the remainder of the text.
        return $outcome . substr($text, $position);
    }
}
