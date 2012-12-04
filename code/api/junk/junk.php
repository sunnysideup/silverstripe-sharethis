
require_once 'facebook-php-sdk/src/facebook.php';

// Create our Application instance.
$facebook = new Facebook(array(
	'appId' => '216039248422508',
	'secret' => 'bf0ca9026ea1715a2ddc1c2746948ec8',
	'cookie' => true,
));


/**
 *
 * Copyright 2011 Facebook, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */


require '../src/facebook.php';

// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
	'appId'  => '117743971608120',
	'secret' => '943716006e74d9b9283d4d5d8ab93204',
	'cookie' => true,
));

// We may or may not have this data based on a $_GET or $_COOKIE based session.
//
// If we get a session here, it means we found a correctly signed session using
// the Application Secret only Facebook and the Application know. We dont know
// if it is still valid until we make an API call using the session. A session
// can become invalid if it has already expired (should not be getting the
// session back in this case) or if the user logged out of Facebook.
$session = $facebook->getSession();

$me = null;
// Session based API call.
if ($session) {
	try {
		$uid = $facebook->getUser();
		$me = $facebook->api('/me');
	} catch (FacebookApiException $e) {
		error_log($e);
	}
}

// login or logout url will be needed depending on current user state.
if ($me) {
	$logoutUrl = $facebook->getLogoutUrl();
} else {
	$loginUrl = $facebook->getLoginUrl();
}

// This call will always work since we are fetching public data.
$naitik = $facebook->api('/naitik');

?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
	<head>
		<title>php-sdk</title>
		<style>
			body {
				font-family: 'Lucida Grande', Verdana, Arial, sans-serif;
			}
			h1 a {
				text-decoration: none;
				color: #3b5998;
			}
			h1 a:hover {
				text-decoration: underline;
			}
		</style>
	</head>
	<body>
		<!--
			We use the JS SDK to provide a richer user experience. For more info,
			look here: http://github.com/facebook/connect-js
		-->
		<div id="fb-root"></div>
		<script>
			window.fbAsyncInit = function() {
				FB.init({
					appId   : '<?php echo $facebook->getAppId(); ?>',
					session : <?php echo json_encode($session); ?>, // don't refetch the session when PHP already has it
					status  : true, // check login status
					cookie  : true, // enable cookies to allow the server to access the session
					xfbml   : true // parse XFBML
				});

				// whenever the user logs in, we refresh the page
				FB.Event.subscribe('auth.login', function() {
					window.location.reload();
				});
			};

			(function() {
				var e = document.createElement('script');
				e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
				e.async = true;
				document.getElementById('fb-root').appendChild(e);
			}());
		</script>


		<h1><a href="example.php">php-sdk</a></h1>

		<?php if ($me): ?>
		<a href="<?php echo $logoutUrl; ?>">
			<img src="http://static.ak.fbcdn.net/rsrc.php/z2Y31/hash/cxrz4k7j.gif">
		</a>
		<?php else: ?>
		<div>
			Using JavaScript &amp; XFBML: <fb:login-button></fb:login-button>
		</div>
		<div>
			Without using JavaScript &amp; XFBML:
			<a href="<?php echo $loginUrl; ?>">
				<img src="http://static.ak.fbcdn.net/rsrc.php/zB6N8/hash/4li2k73z.gif">
			</a>
		</div>
		<?php endif ?>

		<h3>Session</h3>
		<?php if ($me): ?>
		<pre><?php print_r($session); ?></pre>

		<h3>You</h3>
		<img src="https://graph.facebook.com/<?php echo $uid; ?>/picture">
		<?php echo $me['name']; ?>

		<h3>Your User Object</h3>
		<pre><?php print_r($me); ?></pre>
		<?php else: ?>
		<strong><em>You are not Connected.</em></strong>
		<?php endif ?>

		<h3>Naitik</h3>
		<img src="https://graph.facebook.com/naitik/picture">
		<?php echo $naitik['name']; ?>
	</body>
</html>

/*

$app_id = "YOUR_APP_ID";
	$app_secret = "YOUR_APP_SECRET";
	$my_url = "YOUR_POST_LOGIN_URL";

	$code = $_REQUEST["code"];

	if(empty($code)) {
		$dialog_url = "http://www.facebook.com/dialog/oauth?client_id="
		. $app_id . "&redirect_uri=" . urlencode($my_url) . "&scope=email";

		echo("<script>top.location.href='" . $dialog_url . "'</script>");
	}

	$token_url = "https://graph.facebook.com/oauth/access_token?client_id="
		. $app_id . "&redirect_uri=" . urlencode($my_url)
		. "&client_secret=" . $app_secret
		. "&code=" . $code;

	$access_token = file_get_contents($token_url);
	$graph_url="https://graph.facebook.com/me/permissions?".$access_token;
	echo "graph_url=" . $graph_url . "<br />";
	$user_permissions = json_decode(file_get_contents($graph_url));
	print_r($user_permissions);


*/
