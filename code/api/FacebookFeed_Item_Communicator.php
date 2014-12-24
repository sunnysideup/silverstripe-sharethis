<?php


class FacebookFeed_Item_Communicator extends Object {

	/**
	 *
	 * @param String $url
	 * @param Int $maxnumber
	 * @param Int $facebookFeed_PageID
	 * @param String $timeFormat
	 **/
	function fetchFBFeed($url, $maxnumber = 1, $facebookFeed_PageID = 0, $timeFormat = 'Y-m-d') {
	/* The following line is absolutely necessary to read Facebook feeds.
	 * Facebook will not recognize PHP as a browser and therefore won't fetch anything.
	 * So we define a browser here */
	 ini_set('user_agent', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3');
		$updates = @simplexml_load_file($url);  //Load feed with simplexml
		if($updates){
			foreach ( $updates->channel->item as $fbUpdate ) {
				if ($maxnumber == 0) {
					break;
				}
				else {
					$guid = $fbUpdate->guid;
					$uid = substr($guid, -32);
					if(DB::query("SELECT COUNT(\"ID\") FROM \"FacebookFeed_Item\" WHERE \"UID\" = '$uid';")->value() == 0) {
						$desc = $fbUpdate->description;
						//Add www.facebook.com to hyperlinks
						//$desc = urldecode(urldecode(str_replace('href="http://www.facebook.com/l.php?u=', 'href="', $desc)));
						//Converts UTF-8 into ISO-8859-1 to solve special symbols issues
						$desc = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $desc);
						$desc = $this->stripUnsafe($desc);
						//Get status update time
						$pubDate = strtotime($fbUpdate->pubDate);
						$convertedDate = gmdate($timeFormat, $pubDate);  //Customize this to your liking
						//Get link to update
						//Store values in array
						$facebookFeed_Item = new FacebookFeed_Item();
						$facebookFeed_Item->UID = (string) $uid;
						$facebookFeed_Item->Title = (string) $fbUpdate->title;
						$facebookFeed_Item->Date = $convertedDate;
						$facebookFeed_Item->Author = (string) $fbUpdate->author;
						$facebookFeed_Item->Link = (string) $fbUpdate->link;
						$facebookFeed_Item->Description = $this->stripUnsafe((string) $desc);
						$facebookFeed_Item->FacebookFeed_PageID = $facebookFeed_PageID;
						if(!$facebookFeed_Item->Title || !$facebookFeed_Item->Description) {
							$facebookFeed_Item->Hide = true;
						}
						$facebookFeed_Item->write();

						$maxnumber--;
					}
				}
			}
		}
	}

	function stripUnsafe($string) {
    // Unsafe HTML tags that members may abuse
		$unsafe=array(
			'/onmouseover="(.*?)"/is',
			'/onclick="(.*?)"/is',
			'/style="(.*?)"/is',
			'/target="(.*?)"/is',
			'/onunload="(.*?)"/is',
			'/rel="(.*?)"/is',
			'/<a(.*?)>/is',
			'/<\/a>/is'
		);
		$string= preg_replace($unsafe, " ", $string);
		return $string;
	}


}


