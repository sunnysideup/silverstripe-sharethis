<?php

/**
 *@source: http://matthom.com/archive/2011/03/27/using-the-facebook-api-with-php
 *@see: http://blog.theunical.com/facebook-integration/5-steps-to-publish-on-a-facebook-wall-using-php/
 * example usage:
 <code>
 	$myFacebookAPI = FacebookConnect::get_my_facebook_api();
	$attachment = array('message' => 'this is my message',
		'name' => 'This is my demo Facebook application!',
		'caption' => "Caption of the Post",
		'link' => 'http://mylink.com',
		'description' => 'this is a description',
		'picture' => 'http://mysite.com/pic.gif',
		'actions' => array(array('name' => 'Get Search',
		'link' => 'http://www.google.com'))
	);
	$result = $myFacebookAPI->api('/me/feed/','post',$attachment);
 </code>
 *
 *
 *also see: https://github.com/facebook/php-sdk/
 *
 *
 **/

class FacebookConnect extends Object {

	protected static $application_id = "";
		public static function set_application_id($s) {self::$application_id = $s;}
		public static function get_application_id() {return self::$application_id;}

	protected static $application_secret = "";
		public static function set_application_secret($s) {self::$application_secret = $s;}
		public static function get_application_secret() {return self::$application_secret;}

	protected static $my_facebook_api = null;

	protected $facebook_connect_singleton = null;

	public static function get_my_facebook_api () {
		include(SS_SHARETHIS_DIR."/third_party/facebook.php");
		if(!self::$my_facebook_api) {
			self::$facebook_connect_singleton = new FacebookConnect();
			$facebook = self::$facebook_connect_singleton->connectToFacebook(self::get_application_id(), self::get_application_secret());
			$facebook_session = self::$facebook_connect_singleton->getSession($facebook);
			$self::my_facebook_api = null;
			if ($facebook_session) {
				$self::my_facebook_api = self::$facebook_connect_singleton->connectToAccount($facebook, $facebook_session);
			}
		}
		return self::$my_facebook_api;
	}

	/**
	 * @param String $facebook_app_id - to be obtained from FB application settings
	 * @param String $facebook_app_secret - to be obtained from FB application settings
	 * @param Boolean $cookie set a cookie???
	 * @return Facebook Class
	**/
	function connectToFacebook($facebook_app_id, $facebook_app_secret, $cookie = false) {
		$facebook = new Facebook(
			array(
				"appId" => $facebook_app_id,
				"secret" => $facebook_app_secret,
				"cookie" => $cookie
			)
		);
		return $facebook;
	}

	/**
	 * @param Object $init- ?????
	 * @param Object $session ????
	 * @return ????/
	**/
	function connectToAccount($init, $session) {
		$me = null;
		if ($session) {
			try {
				$me = $init->api("/me");
			}
			catch (FacebookApiException $e) {
				user_error("Facebook error: $e", E_USER_NOTICE);
				//nothing yet
			}
		}
		return $me;
	}

	/**
	 * @param Object $init- ?????
	 * @param Object $db_conn  ????
	 * @return session ????
	**/
	function getSession($init, $db_conn = "mlogConn") {
		$session = $init->getSession();
		// IF A TRUE SESSION DOES NOT EXIST (WE ARE NOT CURRENTLY LOGGED IN TO FACEBOOK), CHECK IF WE HAVE IT CACHED
		if (!$session) {
			// FIND SESSION SAVED IN LOCAL DATABASE.
			//$session = &$GLOBALS[$db_conn] -> query("SELECT facebook_session FROM stream_updates_external_on") -> fetch();
			//$session = $session["facebook_session"];
			Session::get("facebooksession");
			if ($session) {
				$session = unserialize($session);
				$session = $init->setSession($session);
			}
		}
		else {
			// CACHE IT FOR RE-USE LATER.
			$session = serialize($session);
			Session::set("facebooksession", $session);
			//$update = &$GLOBALS[$db_conn] -> query("UPDATE stream_updates_external_on SET facebook_session = '" . $session . "'");
			$session = unserialize($session);
		}
		return $session;
	}

	/**
	 * @param Object $init- ?????
	 * @param Object $db_conn  ????
	 * @return session ????
	**/
	function closeSession($init, $db_conn = "mlogConn") {
		Session::clear("facebooksession");
		self::$my_facebook_api = null;
		return true;
	}

	//--------------------------- PRACTICAL STUFF ------------------------------------------------------------------

	/**
	 * @param Object $init- ?????
	 * @param Object $session ????
	 * @param String $login_perms ????
	 * @param String $login_url ????
	 * @param String $logout_url ????
	 * @return array
	**/
	static function facebook_oauth_geturl($init, $session, $login_perms = "", $login_url = "", $logout_url = "") {
		$me = self::$facebook_connect_singleton->connectToAccount($init, $session);
		if ($me) {
			return $init->getLogoutUrl( array("next" => $logout_url) );
		}
		return $init->getLoginUrl( array("next" => $login_url, "req_perms" => $login_perms) );
	}

}
